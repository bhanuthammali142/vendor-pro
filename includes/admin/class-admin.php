<?php
/**
 * Admin class
 */

if (!defined('ABSPATH')) {
    exit;
}

class VendorPro_Admin
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
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_filter('plugin_action_links_' . VENDORPRO_PLUGIN_BASENAME, array($this, 'plugin_action_links'));

        // Block Admin Access
        add_action('admin_init', array($this, 'restrict_admin_access'));
    }

    /**
     * Restrict admin access for vendors
     */
    public function restrict_admin_access()
    {
        if (defined('DOING_AJAX') && DOING_AJAX) {
            return;
        }

        // Check if restriction is enabled
        // Default is 'yes' (Prevent access)
        if (get_option('vendorpro_admin_access', 'yes') !== 'yes') {
            return;
        }

        if (!is_user_logged_in()) {
            return;
        }

        $user = wp_get_current_user();

        // If user is a vendor AND NOT an admin
        if (in_array('vendor', (array) $user->roles) && !in_array('administrator', (array) $user->roles) && !current_user_can('manage_options')) {
            // Redirect to vendor dashboard
            $dashboard_page_id = get_option('vendorpro_vendor_dashboard_page_id');
            if ($dashboard_page_id) {
                wp_redirect(get_permalink($dashboard_page_id));
            } else {
                wp_redirect(home_url());
            }
            exit;
        }
    }

    /**
     * Add admin menu
     */
    /**
     * Add admin menu
     */
    public function admin_menu()
    {
        add_menu_page(
            __('VendorPro', 'vendorpro'),
            __('VendorPro', 'vendorpro'),
            'manage_options',
            'vendorpro',
            array($this, 'dashboard_page'),
            'dashicons-store',
            56
        );

        add_submenu_page(
            'vendorpro',
            __('Dashboard', 'vendorpro'),
            __('Dashboard', 'vendorpro'),
            'manage_options',
            'vendorpro',
            array($this, 'dashboard_page')
        );

        add_submenu_page(
            'vendorpro',
            __('Withdraw', 'vendorpro'),
            __('Withdraw', 'vendorpro'),
            'manage_options',
            'vendorpro-withdrawals',
            array('VendorPro_Admin_Withdrawals', 'render_page')
        );

        add_submenu_page(
            'vendorpro',
            __('Reverse Withdrawal', 'vendorpro'),
            __('Reverse Withdrawal', 'vendorpro'),
            'manage_options',
            'vendorpro-reverse-withdrawal',
            array('VendorPro_Admin_Reverse_Withdrawal', 'render_page')
        );

        add_submenu_page(
            'vendorpro',
            __('Vendors', 'vendorpro'),
            __('Vendors', 'vendorpro'),
            'manage_options',
            'vendorpro-vendors',
            array('VendorPro_Admin_Vendors', 'render_page')
        );

        // Keeping Commissions as it is essential, though not in the specific screenshot provided
        add_submenu_page(
            'vendorpro',
            __('Commissions', 'vendorpro'),
            __('Commissions', 'vendorpro'),
            'manage_options',
            'vendorpro-commissions',
            array('VendorPro_Admin_Commissions', 'render_page')
        );

        add_submenu_page(
            'vendorpro',
            __('Modules', 'vendorpro'),
            __('Modules', 'vendorpro'),
            'manage_options',
            'vendorpro-modules',
            array('VendorPro_Admin_Modules', 'render_page')
        );

        add_submenu_page(
            'vendorpro',
            __('Status', 'vendorpro'),
            __('Status', 'vendorpro'),
            'manage_options',
            'vendorpro-status',
            array('VendorPro_Admin_Status', 'render_page')
        );

        add_submenu_page(
            'vendorpro',
            __('Help', 'vendorpro'),
            __('Help', 'vendorpro'),
            'manage_options',
            'vendorpro-help',
            array('VendorPro_Admin_Help', 'render_page')
        );

        add_submenu_page(
            'vendorpro',
            __('Settings', 'vendorpro'),
            __('Settings', 'vendorpro'),
            'manage_options',
            'vendorpro-settings',
            array('VendorPro_Admin_Settings', 'render_page')
        );
    }

    /**
     * Dashboard page
     */
    public function dashboard_page()
    {
        $db = VendorPro_Database::instance();

        // Get stats
        $total_vendors = $db->count_vendors();
        $pending_vendors = $db->count_vendors(array('status' => 'pending'));
        $approved_vendors = $db->count_vendors(array('status' => 'approved'));

        // Get pending withdrawals
        global $wpdb;
        $pending_withdrawals = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->prefix}vendorpro_withdrawals WHERE status = 'pending'"
        );

        $total_withdrawal_amount = $wpdb->get_var(
            "SELECT SUM(amount) FROM {$wpdb->prefix}vendorpro_withdrawals WHERE status = 'pending'"
        );

        // Get recent vendors
        $recent_vendors = $db->get_vendors(array('limit' => 5, 'orderby' => 'created_at', 'order' => 'DESC'));

        include VENDORPRO_TEMPLATES_DIR . 'admin/dashboard.php';
    }

    /**
     * Enqueue admin scripts
     */
    public function enqueue_scripts($hook)
    {
        if (strpos($hook, 'vendorpro') === false) {
            return;
        }

        wp_enqueue_style('vendorpro-admin', VENDORPRO_ASSETS_URL . 'css/admin.css', array(), VENDORPRO_VERSION);
        wp_enqueue_script('vendorpro-admin', VENDORPRO_ASSETS_URL . 'js/admin.js', array('jquery'), VENDORPRO_VERSION, true);

        wp_localize_script('vendorpro-admin', 'vendorpro_admin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('vendorpro_admin_nonce')
        ));
    }

    /**
     * Plugin action links
     */
    public function plugin_action_links($links)
    {
        $action_links = array(
            'settings' => '<a href="' . admin_url('admin.php?page=vendorpro-settings') . '">' . __('Settings', 'vendorpro') . '</a>',
        );

        return array_merge($action_links, $links);
    }
}
