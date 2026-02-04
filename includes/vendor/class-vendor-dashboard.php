<?php
/**
 * Vendor Dashboard
 */

if (!defined('ABSPATH')) {
    exit;
}

class VendorPro_Vendor_Dashboard
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
        add_action('template_redirect', array($this, 'handle_dashboard'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    /**
     * Handle dashboard
     */
    public function handle_dashboard()
    {
        $dashboard_page = get_query_var('vendor_dashboard');

        if (!$dashboard_page && !is_page(get_option('vendorpro_vendor_dashboard_page_id'))) {
            return;
        }

        if (!is_user_logged_in()) {
            wp_redirect(wp_login_url(get_permalink()));
            exit;
        }

        if (!vendorpro_is_vendor()) {
            wp_die(__('You must be a vendor to access this page.', 'vendorpro'));
        }
    }

    /**
     * Enqueue scripts
     */
    public function enqueue_scripts()
    {
        if (!is_page(get_option('vendorpro_vendor_dashboard_page_id'))) {
            return;
        }

        wp_enqueue_style('vendorpro-dashboard', VENDORPRO_ASSETS_URL . 'css/dashboard.css', array(), VENDORPRO_VERSION);
        wp_enqueue_script('vendorpro-dashboard', VENDORPRO_ASSETS_URL . 'js/dashboard.js', array('jquery'), VENDORPRO_VERSION, true);

        wp_localize_script('vendorpro-dashboard', 'vendorpro', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('vendorpro_nonce')
        ));
    }

    /**
     * Get dashboard URL
     */
    public function get_dashboard_url($page = '')
    {
        return vendorpro_get_dashboard_url($page);
    }
}
