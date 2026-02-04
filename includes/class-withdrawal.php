<?php
/**
 * Withdrawal management
 */

if (!defined('ABSPATH')) {
    exit;
}

class VendorPro_Withdrawal
{

    /**
     * Instance
     */
    protected static $_instance = null;

    /**
     * Get instance
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        // Hooks will be added here
    }

    /**
     * Request withdrawal
     */
    public function request_withdrawal($vendor_id, $amount, $method, $payment_details = '', $note = '')
    {
        $db = VendorPro_Database::instance();

        // Get vendor
        $vendor = $db->get_vendor($vendor_id);

        if (!$vendor) {
            return new WP_Error('invalid_vendor', __('Invalid vendor.', 'vendorpro'));
        }

        // Check vendor status
        if ($vendor->status !== 'approved') {
            return new WP_Error('vendor_not_approved', __('Vendor is not approved.', 'vendorpro'));
        }

        // Get current balance
        $balance = $db->get_vendor_balance($vendor_id);

        // Check minimum withdrawal amount
        $min_amount = get_option('vendorpro_min_withdraw_amount', 50);

        if ($amount < $min_amount) {
            return new WP_Error(
                'min_amount_error',
                sprintf(__('Minimum withdrawal amount is %s.', 'vendorpro'), wc_price($min_amount))
            );
        }

        // Check if vendor has sufficient balance
        if ($amount > $balance) {
            return new WP_Error('insufficient_balance', __('Insufficient balance.', 'vendorpro'));
        }

        // Check for pending withdrawals
        $pending = $this->has_pending_withdrawal($vendor_id);

        if ($pending) {
            return new WP_Error('pending_withdrawal', __('You already have a pending withdrawal request.', 'vendorpro'));
        }

        // Create withdrawal request
        $withdrawal_data = array(
            'vendor_id' => $vendor_id,
            'amount' => $amount,
            'method' => $method,
            'payment_details' => $payment_details,
            'note' => $note,
            'status' => 'pending',
            'ip_address' => $this->get_ip_address()
        );

        $withdrawal_id = $db->insert_withdrawal($withdrawal_data);

        if ($withdrawal_id) {
            // Deduct from balance (hold)
            $db->update_vendor_balance(
                $vendor_id,
                $amount,
                'debit',
                $withdrawal_id,
                'withdrawal_request',
                __('Withdrawal request', 'vendorpro')
            );

            do_action('vendorpro_withdrawal_requested', $withdrawal_id, $vendor_id);

            return $withdrawal_id;
        }

        return new WP_Error('withdrawal_failed', __('Failed to create withdrawal request.', 'vendorpro'));
    }

    /**
     * Approve withdrawal
     */
    public function approve_withdrawal($withdrawal_id, $note = '')
    {
        global $wpdb;
        $db = VendorPro_Database::instance();

        // Get withdrawal
        $withdrawal = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}vendorpro_withdrawals WHERE id = %d",
            $withdrawal_id
        ));

        if (!$withdrawal) {
            return new WP_Error('invalid_withdrawal', __('Invalid withdrawal.', 'vendorpro'));
        }

        if ($withdrawal->status !== 'pending') {
            return new WP_Error('invalid_status', __('Withdrawal is not pending.', 'vendorpro'));
        }

        // Update withdrawal status
        $update_data = array(
            'status' => 'completed',
            'processed_at' => current_time('mysql'),
            'processed_by' => get_current_user_id()
        );

        if ($note) {
            $update_data['note'] = $note;
        }

        $result = $db->update_withdrawal($withdrawal_id, $update_data);

        if ($result !== false) {
            do_action('vendorpro_withdrawal_approved', $withdrawal_id, $withdrawal);
            return true;
        }

        return new WP_Error('update_failed', __('Failed to update withdrawal.', 'vendorpro'));
    }

    /**
     * Reject withdrawal
     */
    public function reject_withdrawal($withdrawal_id, $note = '')
    {
        global $wpdb;
        $db = VendorPro_Database::instance();

        // Get withdrawal
        $withdrawal = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}vendorpro_withdrawals WHERE id = %d",
            $withdrawal_id
        ));

        if (!$withdrawal) {
            return new WP_Error('invalid_withdrawal', __('Invalid withdrawal.', 'vendorpro'));
        }

        if ($withdrawal->status !== 'pending') {
            return new WP_Error('invalid_status', __('Withdrawal is not pending.', 'vendorpro'));
        }

        // Update withdrawal status
        $update_data = array(
            'status' => 'rejected',
            'processed_at' => current_time('mysql'),
            'processed_by' => get_current_user_id()
        );

        if ($note) {
            $update_data['note'] = $note;
        }

        $result = $db->update_withdrawal($withdrawal_id, $update_data);

        if ($result !== false) {
            // Refund the amount back to vendor balance
            $db->update_vendor_balance(
                $withdrawal->vendor_id,
                $withdrawal->amount,
                'credit',
                $withdrawal_id,
                'withdrawal_rejected',
                __('Withdrawal request rejected', 'vendorpro')
            );

            do_action('vendorpro_withdrawal_rejected', $withdrawal_id, $withdrawal);
            return true;
        }

        return new WP_Error('update_failed', __('Failed to update withdrawal.', 'vendorpro'));
    }

    /**
     * Cancel withdrawal
     */
    public function cancel_withdrawal($withdrawal_id, $vendor_id)
    {
        global $wpdb;
        $db = VendorPro_Database::instance();

        // Get withdrawal
        $withdrawal = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}vendorpro_withdrawals WHERE id = %d AND vendor_id = %d",
            $withdrawal_id,
            $vendor_id
        ));

        if (!$withdrawal) {
            return new WP_Error('invalid_withdrawal', __('Invalid withdrawal.', 'vendorpro'));
        }

        if ($withdrawal->status !== 'pending') {
            return new WP_Error('invalid_status', __('Only pending withdrawals can be cancelled.', 'vendorpro'));
        }

        // Update withdrawal status
        $result = $db->update_withdrawal($withdrawal_id, array(
            'status' => 'cancelled',
            'processed_at' => current_time('mysql')
        ));

        if ($result !== false) {
            // Refund the amount back to vendor balance
            $db->update_vendor_balance(
                $vendor_id,
                $withdrawal->amount,
                'credit',
                $withdrawal_id,
                'withdrawal_cancelled',
                __('Withdrawal request cancelled', 'vendorpro')
            );

            do_action('vendorpro_withdrawal_cancelled', $withdrawal_id, $withdrawal);
            return true;
        }

        return new WP_Error('update_failed', __('Failed to cancel withdrawal.', 'vendorpro'));
    }

    /**
     * Check if vendor has pending withdrawal
     */
    public function has_pending_withdrawal($vendor_id)
    {
        global $wpdb;

        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}vendorpro_withdrawals 
             WHERE vendor_id = %d AND status = 'pending'",
            $vendor_id
        ));

        return $count > 0;
    }

    /**
     * Get withdrawal
     */
    public function get_withdrawal($withdrawal_id)
    {
        global $wpdb;

        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}vendorpro_withdrawals WHERE id = %d",
            $withdrawal_id
        ));
    }

    /**
     * Get all withdrawals
     */
    public function get_withdrawals($args = array())
    {
        global $wpdb;

        $defaults = array(
            'vendor_id' => 0,
            'status' => '',
            'limit' => 20,
            'offset' => 0,
            'orderby' => 'requested_at',
            'order' => 'DESC'
        );

        $args = wp_parse_args($args, $defaults);

        $where = array('1=1');

        if ($args['vendor_id']) {
            $where[] = $wpdb->prepare("vendor_id = %d", $args['vendor_id']);
        }

        if (!empty($args['status'])) {
            $where[] = $wpdb->prepare("status = %s", $args['status']);
        }

        $where_clause = implode(' AND ', $where);
        $orderby = sanitize_sql_orderby($args['orderby'] . ' ' . $args['order']);

        $sql = "SELECT * FROM {$wpdb->prefix}vendorpro_withdrawals 
                WHERE {$where_clause} 
                ORDER BY {$orderby} 
                LIMIT %d OFFSET %d";

        return $wpdb->get_results($wpdb->prepare($sql, $args['limit'], $args['offset']));
    }

    /**
     * Get available withdrawal methods
     */
    public function get_withdrawal_methods()
    {
        $methods = array(
            'paypal' => __('PayPal', 'vendorpro'),
            'bank' => __('Bank Transfer', 'vendorpro'),
            'stripe' => __('Stripe', 'vendorpro')
        );

        return apply_filters('vendorpro_withdrawal_methods', $methods);
    }

    /**
     * Get IP address
     */
    private function get_ip_address()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }
}
