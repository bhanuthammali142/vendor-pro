<?php
/**
 * Admin settings
 */

if (!defined('ABSPATH')) {
    exit;
}

class VendorPro_Admin_Settings
{

    /**
     * Render page
     */
    public static function render_page()
    {
        // Handle form submission
        if (isset($_POST['submit']) && check_admin_referer('vendorpro-settings')) {
            self::save_settings();
            echo '<div class="notice notice-success"><p>' . __('Settings saved successfully.', 'vendorpro') . '</p></div>';
        }

        $current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';

        include VENDORPRO_TEMPLATES_DIR . 'admin/settings.php';
    }

    /**
     * Save settings
     */
    private static function save_settings()
    {
        // General settings
        if (isset($_POST['vendorpro_vendor_registration'])) {
            update_option('vendorpro_vendor_registration', sanitize_text_field($_POST['vendorpro_vendor_registration']));
        }

        if (isset($_POST['vendorpro_vendor_approval'])) {
            update_option('vendorpro_vendor_approval', sanitize_text_field($_POST['vendorpro_vendor_approval']));
        }

        if (isset($_POST['vendorpro_product_approval'])) {
            update_option('vendorpro_product_approval', sanitize_text_field($_POST['vendorpro_product_approval']));
        }

        if (isset($_POST['vendorpro_vendor_per_page'])) {
            update_option('vendorpro_vendor_per_page', intval($_POST['vendorpro_vendor_per_page']));
        }

        // Commission settings
        if (isset($_POST['vendorpro_commission_rate'])) {
            update_option('vendorpro_commission_rate', floatval($_POST['vendorpro_commission_rate']));
        }

        if (isset($_POST['vendorpro_commission_type'])) {
            update_option('vendorpro_commission_type', sanitize_text_field($_POST['vendorpro_commission_type']));
        }

        // Withdrawal settings
        if (isset($_POST['vendorpro_min_withdraw_amount'])) {
            update_option('vendorpro_min_withdraw_amount', floatval($_POST['vendorpro_min_withdraw_amount']));
        }

        if (isset($_POST['vendorpro_withdraw_methods'])) {
            update_option('vendorpro_withdraw_methods', array_map('sanitize_text_field', $_POST['vendorpro_withdraw_methods']));
        }
    }

    /**
     * Get tabs
     */
    public static function get_tabs()
    {
        return array(
            'general' => __('General', 'vendorpro'),
            'commission' => __('Commission', 'vendorpro'),
            'withdrawal' => __('Withdrawal', 'vendorpro'),
            'pages' => __('Pages', 'vendorpro'),
        );
    }
}
