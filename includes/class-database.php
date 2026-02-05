<?php
/**
 * Database operations
 */

if (!defined('ABSPATH')) {
    exit;
}

class VendorPro_Database
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
     * Get vendor by ID
     */
    public function get_vendor($vendor_id)
    {
        global $wpdb;

        $vendor = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}vendorpro_vendors WHERE id = %d",
            $vendor_id
        ));

        return $vendor;
    }

    /**
     * Get vendor by user ID
     */
    public function get_vendor_by_user($user_id)
    {
        global $wpdb;

        $vendor = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}vendorpro_vendors WHERE user_id = %d",
            $user_id
        ));

        return $vendor;
    }

    /**
     * Get vendor by slug
     */
    public function get_vendor_by_slug($slug)
    {
        global $wpdb;

        $vendor = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}vendorpro_vendors WHERE store_slug = %s",
            $slug
        ));

        return $vendor;
    }

    /**
     * Insert vendor
     */
    public function insert_vendor($data)
    {
        global $wpdb;

        $wpdb->insert(
            $wpdb->prefix . 'vendorpro_vendors',
            $data,
            array('%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%f', '%s', '%d', '%d', '%s')
        );

        return $wpdb->insert_id;
    }

    /**
     * Update vendor
     */
    public function update_vendor($vendor_id, $data)
    {
        global $wpdb;

        return $wpdb->update(
            $wpdb->prefix . 'vendorpro_vendors',
            $data,
            array('id' => $vendor_id),
            null,
            array('%d')
        );
    }

    /**
     * Delete vendor
     */
    public function delete_vendor($vendor_id)
    {
        global $wpdb;

        return $wpdb->delete(
            $wpdb->prefix . 'vendorpro_vendors',
            array('id' => $vendor_id),
            array('%d')
        );
    }

    /**
     * Get all vendors
     */
    public function get_vendors($args = array())
    {
        global $wpdb;

        $defaults = array(
            'status' => '',
            'featured' => '',
            'limit' => 20,
            'offset' => 0,
            'orderby' => 'created_at',
            'order' => 'DESC',
            'search' => ''
        );

        $args = wp_parse_args($args, $defaults);

        $where = array('1=1');

        if (!empty($args['status'])) {
            $where[] = $wpdb->prepare("status = %s", $args['status']);
        }

        if ($args['featured'] !== '') {
            $where[] = $wpdb->prepare("featured = %d", $args['featured']);
        }

        if (!empty($args['search'])) {
            $search = '%' . $wpdb->esc_like($args['search']) . '%';
            $where[] = $wpdb->prepare("(store_name LIKE %s OR email LIKE %s)", $search, $search);
        }

        $where_clause = implode(' AND ', $where);

        $orderby = sanitize_sql_orderby($args['orderby'] . ' ' . $args['order']);

        $sql = "SELECT * FROM {$wpdb->prefix}vendorpro_vendors 
                WHERE {$where_clause} 
                ORDER BY {$orderby} 
                LIMIT %d OFFSET %d";

        $vendors = $wpdb->get_results($wpdb->prepare($sql, $args['limit'], $args['offset']));

        return $vendors;
    }

    /**
     * Count vendors
     */
    public function count_vendors($args = array())
    {
        global $wpdb;

        $where = array('1=1');

        if (!empty($args['status'])) {
            $where[] = $wpdb->prepare("status = %s", $args['status']);
        }

        if (isset($args['featured']) && $args['featured'] !== '') {
            $where[] = $wpdb->prepare("featured = %d", $args['featured']);
        }

        if (!empty($args['search'])) {
            $search = '%' . $wpdb->esc_like($args['search']) . '%';
            $where[] = $wpdb->prepare("(store_name LIKE %s OR email LIKE %s)", $search, $search);
        }

        $where_clause = implode(' AND ', $where);

        $count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}vendorpro_vendors WHERE {$where_clause}");

        return $count;
    }

    /**
     * Insert commission
     */
    public function insert_commission($data)
    {
        global $wpdb;

        $wpdb->insert(
            $wpdb->prefix . 'vendorpro_commissions',
            $data
        );

        return $wpdb->insert_id;
    }

    /**
     * Update commission
     */
    public function update_commission($commission_id, $data)
    {
        global $wpdb;

        return $wpdb->update(
            $wpdb->prefix . 'vendorpro_commissions',
            $data,
            array('id' => $commission_id),
            null,
            array('%d')
        );
    }

    /**
     * Get vendor commissions
     */
    public function get_vendor_commissions($vendor_id, $args = array())
    {
        global $wpdb;

        $defaults = array(
            'status' => '',
            'limit' => 20,
            'offset' => 0,
            'orderby' => 'created_at',
            'order' => 'DESC'
        );

        $args = wp_parse_args($args, $defaults);

        $where = array($wpdb->prepare("vendor_id = %d", $vendor_id));

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

    /**
     * Get vendor total earnings
     */
    public function get_vendor_earnings($vendor_id, $status = 'paid')
    {
        global $wpdb;

        $earnings = $wpdb->get_var($wpdb->prepare(
            "SELECT SUM(vendor_earning) FROM {$wpdb->prefix}vendorpro_commissions 
             WHERE vendor_id = %d AND status = %s",
            $vendor_id,
            $status
        ));

        return $earnings ? floatval($earnings) : 0;
    }

    /**
     * Insert withdrawal
     */
    public function insert_withdrawal($data)
    {
        global $wpdb;

        $wpdb->insert(
            $wpdb->prefix . 'vendorpro_withdrawals',
            $data
        );

        return $wpdb->insert_id;
    }

    /**
     * Update withdrawal
     */
    public function update_withdrawal($withdrawal_id, $data)
    {
        global $wpdb;

        return $wpdb->update(
            $wpdb->prefix . 'vendorpro_withdrawals',
            $data,
            array('id' => $withdrawal_id),
            null,
            array('%d')
        );
    }

    /**
     * Get vendor withdrawals
     */
    public function get_vendor_withdrawals($vendor_id, $args = array())
    {
        global $wpdb;

        $defaults = array(
            'status' => '',
            'limit' => 20,
            'offset' => 0,
            'orderby' => 'requested_at',
            'order' => 'DESC'
        );

        $args = wp_parse_args($args, $defaults);

        $where = array($wpdb->prepare("vendor_id = %d", $vendor_id));

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
     * Get total withdrawn amount
     */
    public function get_vendor_withdrawn_amount($vendor_id)
    {
        global $wpdb;

        $amount = $wpdb->get_var($wpdb->prepare(
            "SELECT SUM(amount) FROM {$wpdb->prefix}vendorpro_withdrawals 
             WHERE vendor_id = %d AND status = %s",
            $vendor_id,
            'completed'
        ));

        return $amount ? floatval($amount) : 0;
    }

    /**
     * Update vendor balance
     */
    public function update_vendor_balance($vendor_id, $amount, $type, $trn_id = null, $trn_type = null, $particulars = '')
    {
        global $wpdb;

        // Get current balance
        $current_balance = $this->get_vendor_balance($vendor_id);

        $data = array(
            'vendor_id' => $vendor_id,
            'particulars' => $particulars,
            'trn_id' => $trn_id,
            'trn_type' => $trn_type
        );

        if ($type === 'credit') {
            $data['credit'] = $amount;
            $data['debit'] = 0;
            $data['balance'] = $current_balance + $amount;
        } else {
            $data['debit'] = $amount;
            $data['credit'] = 0;
            $data['balance'] = $current_balance - $amount;
        }

        $wpdb->insert(
            $wpdb->prefix . 'vendorpro_vendor_balance',
            $data
        );

        return $wpdb->insert_id;
    }

    /**
     * Get vendor balance
     */
    public function get_vendor_balance($vendor_id)
    {
        global $wpdb;

        $balance = $wpdb->get_var($wpdb->prepare(
            "SELECT balance FROM {$wpdb->prefix}vendorpro_vendor_balance 
             WHERE vendor_id = %d 
             ORDER BY id DESC 
             LIMIT 1",
            $vendor_id
        ));

        return $balance ? floatval($balance) : 0;
    }
    /**
     * Get order commission amount
     */
    public function get_order_commission_amount($order_id, $vendor_id)
    {
        global $wpdb;

        $commission = $wpdb->get_var($wpdb->prepare(
            "SELECT vendor_earning FROM {$wpdb->prefix}vendorpro_commissions 
             WHERE order_id = %d AND vendor_id = %d",
            $order_id,
            $vendor_id
        ));

        return $commission ? floatval($commission) : 0;
    }
}
