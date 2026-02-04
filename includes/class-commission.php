<?php
/**
 * Commission calculation and management
 */

if (!defined('ABSPATH')) {
    exit;
}

class VendorPro_Commission
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
        add_action('woocommerce_order_status_completed', array($this, 'create_commission_on_order_complete'));
        add_action('woocommerce_order_status_processing', array($this, 'create_commission_on_order_processing'));
        add_action('woocommerce_order_status_refunded', array($this, 'handle_refund'));
    }

    /**
     * Create commission when order is completed
     */
    public function create_commission_on_order_complete($order_id)
    {
        $this->create_commission($order_id, 'paid');
    }

    /**
     * Create commission when order is processing
     */
    public function create_commission_on_order_processing($order_id)
    {
        $commission_on_processing = apply_filters('vendorpro_commission_on_processing', true);

        if ($commission_on_processing) {
            $this->create_commission($order_id, 'unpaid');
        }
    }

    /**
     * Create commission for order
     */
    public function create_commission($order_id, $status = 'unpaid')
    {
        $order = wc_get_order($order_id);

        if (!$order) {
            return false;
        }

        // Check if commission already created
        if (get_post_meta($order_id, '_vendorpro_commission_created', true)) {
            return false;
        }

        $db = VendorPro_Database::instance();

        foreach ($order->get_items() as $item_id => $item) {
            $product_id = $item->get_product_id();
            $product = $item->get_product();

            if (!$product) {
                continue;
            }

            // Get vendor for this product
            $vendor_id = get_post_meta($product_id, '_vendor_id', true);

            if (!$vendor_id) {
                // Get vendor by product author
                $post = get_post($product_id);
                $vendor = $db->get_vendor_by_user($post->post_author);

                if (!$vendor) {
                    continue;
                }

                $vendor_id = $vendor->id;
            }

            $vendor = $db->get_vendor($vendor_id);

            if (!$vendor || $vendor->status !== 'approved') {
                continue;
            }

            // Calculate commission
            $item_total = $item->get_total();
            $commission_data = $this->calculate_commission($item_total, $vendor);

            // Insert commission record
            $commission_id = $db->insert_commission(array(
                'vendor_id' => $vendor_id,
                'order_id' => $order_id,
                'order_item_id' => $item_id,
                'product_id' => $product_id,
                'order_total' => $item_total,
                'vendor_earning' => $commission_data['vendor_earning'],
                'admin_commission' => $commission_data['admin_commission'],
                'commission_rate' => $commission_data['rate'],
                'commission_type' => $commission_data['type'],
                'status' => $status
            ));

            // Update vendor balance if paid
            if ($status === 'paid' && $commission_id) {
                $db->update_vendor_balance(
                    $vendor_id,
                    $commission_data['vendor_earning'],
                    'credit',
                    $commission_id,
                    'commission',
                    sprintf(__('Commission from order #%s', 'vendorpro'), $order_id)
                );

                // Update commission with paid date
                $db->update_commission($commission_id, array(
                    'paid_date' => current_time('mysql')
                ));
            }
        }

        // Mark commission as created
        update_post_meta($order_id, '_vendorpro_commission_created', true);

        do_action('vendorpro_commission_created', $order_id);

        return true;
    }

    /**
     * Calculate commission
     */
    public function calculate_commission($amount, $vendor)
    {
        $commission_rate = $vendor->commission_rate;
        $commission_type = $vendor->commission_type;

        if ($commission_type === 'percentage') {
            $admin_commission = ($amount * $commission_rate) / 100;
            $vendor_earning = $amount - $admin_commission;
        } else {
            // Fixed commission
            $admin_commission = $commission_rate;
            $vendor_earning = $amount - $admin_commission;
        }

        return array(
            'vendor_earning' => max(0, $vendor_earning),
            'admin_commission' => $admin_commission,
            'rate' => $commission_rate,
            'type' => $commission_type
        );
    }

    /**
     * Mark commission as paid
     */
    public function mark_as_paid($commission_id)
    {
        $db = VendorPro_Database::instance();

        // Get commission
        global $wpdb;
        $commission = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}vendorpro_commissions WHERE id = %d",
            $commission_id
        ));

        if (!$commission || $commission->status === 'paid') {
            return false;
        }

        // Update commission status
        $db->update_commission($commission_id, array(
            'status' => 'paid',
            'paid_date' => current_time('mysql')
        ));

        // Update vendor balance
        $db->update_vendor_balance(
            $commission->vendor_id,
            $commission->vendor_earning,
            'credit',
            $commission_id,
            'commission',
            sprintf(__('Commission from order #%s', 'vendorpro'), $commission->order_id)
        );

        do_action('vendorpro_commission_paid', $commission_id, $commission);

        return true;
    }

    /**
     * Handle refund
     */
    public function handle_refund($order_id)
    {
        global $wpdb;

        $db = VendorPro_Database::instance();

        // Get all commissions for this order
        $commissions = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}vendorpro_commissions WHERE order_id = %d",
            $order_id
        ));

        foreach ($commissions as $commission) {
            if ($commission->status === 'paid') {
                // Deduct from vendor balance
                $db->update_vendor_balance(
                    $commission->vendor_id,
                    $commission->vendor_earning,
                    'debit',
                    $commission->id,
                    'refund',
                    sprintf(__('Refund for order #%s', 'vendorpro'), $order_id)
                );
            }

            // Update commission status
            $db->update_commission($commission->id, array(
                'status' => 'refunded'
            ));
        }

        do_action('vendorpro_commission_refunded', $order_id);
    }

    /**
     * Get commission by ID
     */
    public function get_commission($commission_id)
    {
        global $wpdb;

        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}vendorpro_commissions WHERE id = %d",
            $commission_id
        ));
    }

    /**
     * Get all commissions
     */
    public function get_commissions($args = array())
    {
        global $wpdb;

        $defaults = array(
            'vendor_id' => 0,
            'status' => '',
            'limit' => 20,
            'offset' => 0,
            'orderby' => 'created_at',
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

        $sql = "SELECT * FROM {$wpdb->prefix}vendorpro_commissions 
                WHERE {$where_clause} 
                ORDER BY {$orderby} 
                LIMIT %d OFFSET %d";

        return $wpdb->get_results($wpdb->prepare($sql, $args['limit'], $args['offset']));
    }
}
