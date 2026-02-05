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
        add_action('init', array($this, 'register_rewrite_rules'));
        add_filter('query_vars', array($this, 'register_query_vars'));
        add_action('template_redirect', array($this, 'handle_dashboard'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    /**
     * Register rewrite rules
     */
    public function register_rewrite_rules()
    {
        add_rewrite_rule('^vendor-dashboard/([^/]*)/?', 'index.php?pagename=vendor-dashboard&vendor_dashboard=$matches[1]', 'top');

        // Ensure rewrite rules are flushed if needed (e.g. on plugin activation/update)
        // This is a simplified check/flush mechanism
        if (get_option('vendorpro_flush_dashboard_rules')) {
            flush_rewrite_rules();
            delete_option('vendorpro_flush_dashboard_rules');
        }
    }

    /**
     * Register query vars
     */
    public function register_query_vars($vars)
    {
        $vars[] = 'vendor_dashboard';
        return $vars;
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

        // Enqueue Chart.js for Reports
        wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', array(), '4.4.0', true);

        wp_enqueue_script('vendorpro-dashboard', VENDORPRO_ASSETS_URL . 'js/dashboard.js', array('jquery', 'chart-js'), VENDORPRO_VERSION, true);

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
