<?php
/**
 * Reverse Withdrawal System
 * Handles Cash on Delivery (COD) commissions where vendor owes admin
 */

if (!defined('ABSPATH')) {
    exit;
}

class VendorPro_Reverse_Withdrawal
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
        // We hook into the commission calculation process
        add_filter('vendorpro_should_process_commission', array($this, 'check_cod_order'), 10, 2);
        add_action('vendorpro_process_cod_commission', array($this, 'process_cod_commission'), 10, 2);
    }

    /**
     * Check if order is COD
     * If so, we handle it differently (Vendor owes Admin)
     */
    public function check_cod_order($should_process, $order)
    {
        if ($order->get_payment_method() === 'cod') {
            // It's a COD order!
            // We trigger our custom COD processor and return false to stop the standard commission processor
            do_action('vendorpro_process_cod_commission', $order->get_id(), $order);
            return false;
        }

        return $should_process;
    }

    /**
     * Process COD Commission
     * Logic: Vendor collected 100% cash. Admin is owed commission.
     * Action: Debit the vendor's balance by the commission amount.
     */
    public function process_cod_commission($order_id, $order)
    {
        // Avoid duplicates
        if (get_post_meta($order_id, '_vendorpro_cod_commission_processed', true)) {
            return;
        }

        $items = $order->get_items();

        foreach ($items as $item) {
            $product_id = $item->get_product_id();
            $vendor_id = get_post_field('post_author', $product_id);

            if ($vendor_id) {
                // Calculate commission
                $total = $item->get_total();
                $commission_rate = vendorpro_get_commission_rate();
                $commission_type = vendorpro_get_commission_type();

                $admin_commission = 0;

                if ($commission_type === 'percentage') {
                    $admin_commission = ($total * $commission_rate) / 100;
                } else {
                    $admin_commission = $commission_rate * $item->get_quantity();
                }

                // In COD, Vendor keeps everything ($total).
                // Vendor owes Admin ($admin_commission).
                // So we DEBIT the vendor's ledger.

                VendorPro_Database::instance()->update_vendor_balance(
                    $vendor_id,
                    $admin_commission,
                    'debit', // DEBIT because they owe us
                    $order_id,
                    'reverse_withdrawal',
                    sprintf(__('Commission for COD Order #%s', 'vendorpro'), $order_id)
                );

                // Log the commission for reporting (but mark as 'unpaid' effectively)
                $commission_data = array(
                    'vendor_id' => $vendor_id,
                    'order_id' => $order_id,
                    'product_id' => $product_id,
                    'order_total' => $total,
                    'admin_commission' => $admin_commission,
                    'vendor_earning' => $total - $admin_commission, // Theoretically what they kept
                    'status' => 'reverse_withdrawal', // Special status
                );

                VendorPro_Database::instance()->insert_commission($commission_data);
            }
        }

        update_post_meta($order_id, '_vendorpro_cod_commission_processed', true);
    }
}
