<?php
/**
 * Add Vendor Dashboard link to My Account menu
 */

if (!defined('ABSPATH')) {
    exit;
}

class VendorPro_My_Account_Menu
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
        // Add vendor dashboard link to My Account menu
        add_filter('woocommerce_account_menu_items', array($this, 'add_vendor_dashboard_link'), 40);

        // Customize the link URL to point directly to dashboard
        add_filter('woocommerce_get_endpoint_url', array($this, 'custom_vendor_dashboard_url'), 10, 4);

        // Keep endpoint registration as fallback
        add_action('init', array($this, 'add_endpoints'));
        add_action('woocommerce_account_vendor-dashboard_endpoint', array($this, 'vendor_dashboard_endpoint_content'));
    }

    /**
     * Add vendor dashboard link to My Account menu
     */
    public function add_vendor_dashboard_link($items)
    {
        // Check if user is a vendor
        if (is_user_logged_in() && VendorPro_Vendor::instance()->is_vendor(get_current_user_id())) {
            // Insert vendor dashboard link after dashboard
            $new_items = array();
            foreach ($items as $key => $value) {
                $new_items[$key] = $value;
                if ($key === 'dashboard') {
                    $new_items['vendor-dashboard'] = __('Vendor Dashboard', 'vendorpro');
                }
            }
            return $new_items;
        }

        return $items;
    }

    /**
     * Customize the endpoint URL
     */
    public function custom_vendor_dashboard_url($url, $endpoint, $value, $permalink)
    {
        if ($endpoint === 'vendor-dashboard') {
            return vendorpro_get_dashboard_url();
        }
        return $url;
    }

    /**
     * Add endpoints
     */
    public function add_endpoints()
    {
        add_rewrite_endpoint('vendor-dashboard', EP_ROOT | EP_PAGES);
    }

    /**
     * Vendor dashboard endpoint content (Fallback)
     */
    public function vendor_dashboard_endpoint_content()
    {
        // Redirect to actual vendor dashboard
        wp_redirect(vendorpro_get_dashboard_url());
        exit;
    }
}

// Initialize
VendorPro_My_Account_Menu::instance();
