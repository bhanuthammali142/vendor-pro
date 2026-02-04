<?php
/**
 * Admin Modules Page
 */

if (!defined('ABSPATH')) {
    exit;
}

class VendorPro_Admin_Modules
{
    /**
     * Render page
     */
    public static function render_page()
    {
        // Handle Module Toggles
        if (isset($_GET['action']) && isset($_GET['module']) && check_admin_referer('vendorpro-toggle-module')) {
            self::toggle_module($_GET['module'], $_GET['action']);
        }

        include VENDORPRO_TEMPLATES_DIR . 'admin/modules.php';
    }

    /**
     * Toggle module
     */
    private static function toggle_module($module, $action)
    {
        $enabled = ($action === 'enable') ? 'yes' : 'no';

        switch ($module) {
            case 'ai_assist':
                // For AI Assist, we might just have a setting or check if key exists. 
                // Let's assume we use a specific option for 'enabled'
                update_option('vendorpro_enable_ai_assist', $enabled);
                break;
            case 'reverse_withdrawal':
                update_option('vendorpro_enable_reverse_withdrawal', $enabled);
                break;
            case 'vendor_verification':
                update_option('vendorpro_vendor_approval', $enabled);
                break;
            case 'store_policies':
                update_option('vendorpro_enable_privacy_policy', $enabled);
                break;
            case 'store_support':
                update_option('vendorpro_show_contact_form', $enabled);
                break;
        }

        wp_redirect(remove_query_arg(array('action', 'module', '_wpnonce')));
        exit;
    }

    /**
     * Get Modules
     */
    public static function get_modules()
    {
        return array(
            'ai_assist' => array(
                'title' => __('AI Assistant', 'vendorpro'),
                'description' => __('Empower vendors to generate product content instantly using AI.', 'vendorpro'),
                'icon' => 'dashicons-superhero',
                'active' => get_option('vendorpro_enable_ai_assist', 'yes') === 'yes',
                'settings_slug' => 'ai_assist'
            ),
            'reverse_withdrawal' => array(
                'title' => __('Reverse Withdrawal', 'vendorpro'),
                'description' => __('Collect commission payments from vendors for COD orders.', 'vendorpro'),
                'icon' => 'dashicons-update',
                'active' => get_option('vendorpro_enable_reverse_withdrawal', 'no') === 'yes',
                'settings_slug' => 'reverse_withdraw'
            ),
            'vendor_verification' => array(
                'title' => __('Vendor Verification', 'vendorpro'),
                'description' => __('Require admin approval for new vendor registrations.', 'vendorpro'),
                'icon' => 'dashicons-saved',
                'active' => get_option('vendorpro_vendor_approval', 'yes') === 'yes',
                'settings_slug' => 'general'
            ),
            'store_policies' => array(
                'title' => __('Store Policies', 'vendorpro'),
                'description' => __('Allow vendors to set their own shipping and refund policies.', 'vendorpro'),
                'icon' => 'dashicons-shield',
                'active' => get_option('vendorpro_enable_privacy_policy', 'yes') === 'yes',
                'settings_slug' => 'privacy'
            ),
            'store_support' => array(
                'title' => __('Store Support', 'vendorpro'),
                'description' => __('Enable customer support contact form on vendor stores.', 'vendorpro'),
                'icon' => 'dashicons-email',
                'active' => get_option('vendorpro_show_contact_form', 'yes') === 'yes',
                'settings_slug' => 'appearance'
            ),
        );
    }
}
