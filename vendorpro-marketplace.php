<?php
/**
 * Plugin Name: VendorPro Marketplace
 * Plugin URI: https://vendorpro-marketplace.com
 * Description: A complete multi-vendor marketplace solution for WordPress & WooCommerce. Enable vendors to sell products on your site with commission management, vendor dashboards, and more.
 * Version: 1.0.0
 * Author: Bhanu Thammali
 * Author URI: https://github.com/bhanuthammali
 * Text Domain: vendorpro
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 * WC tested up to: 8.0
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('VENDORPRO_VERSION', '1.0.0');
define('VENDORPRO_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('VENDORPRO_PLUGIN_URL', plugin_dir_url(__FILE__));
define('VENDORPRO_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('VENDORPRO_INCLUDES_DIR', VENDORPRO_PLUGIN_DIR . 'includes/');
define('VENDORPRO_TEMPLATES_DIR', VENDORPRO_PLUGIN_DIR . 'templates/');
define('VENDORPRO_ASSETS_URL', VENDORPRO_PLUGIN_URL . 'assets/');

/**
 * Main VendorPro Marketplace Class
 */
final class VendorPro_Marketplace
{

    /**
     * The single instance of the class
     */
    protected static $_instance = null;

    /**
     * Main VendorPro Instance
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
        $this->init_hooks();
        $this->includes();
        $this->init_classes();
    }

    /**
     * Hook into actions and filters
     */
    private function init_hooks()
    {
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));

        add_action('plugins_loaded', array($this, 'check_dependencies'));
        add_action('init', array($this, 'init'), 0);
        add_action('admin_notices', array($this, 'admin_notices'));

        // Declare WooCommerce HPOS compatibility
        add_action('before_woocommerce_init', array($this, 'declare_wc_compatibility'));
    }

    /**
     * Declare WooCommerce HPOS compatibility
     */
    public function declare_wc_compatibility()
    {
        if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('orders_cache', __FILE__, true);
        }
    }

    /**
     * Include required core files
     */
    private function includes()
    {
        // Core includes
        require_once VENDORPRO_INCLUDES_DIR . 'class-install.php';
        require_once VENDORPRO_INCLUDES_DIR . 'class-database.php';
        require_once VENDORPRO_INCLUDES_DIR . 'class-vendor.php';
        require_once VENDORPRO_INCLUDES_DIR . 'class-commission.php';
        require_once VENDORPRO_INCLUDES_DIR . 'class-reverse-withdrawal.php';
        require_once VENDORPRO_INCLUDES_DIR . 'class-ai-assist.php';
        require_once VENDORPRO_INCLUDES_DIR . 'class-withdrawal.php';
        require_once VENDORPRO_INCLUDES_DIR . 'class-email.php';
        require_once VENDORPRO_INCLUDES_DIR . 'functions.php';

        // Admin includes
        if (is_admin()) {
            require_once VENDORPRO_INCLUDES_DIR . 'admin/class-admin.php';
            require_once VENDORPRO_INCLUDES_DIR . 'admin/class-admin-settings.php';
            require_once VENDORPRO_INCLUDES_DIR . 'admin/class-admin-vendors.php';
            require_once VENDORPRO_INCLUDES_DIR . 'admin/class-admin-commissions.php';
            require_once VENDORPRO_INCLUDES_DIR . 'admin/class-admin-withdrawals.php';
        }

        // Vendor dashboard includes
        require_once VENDORPRO_INCLUDES_DIR . 'vendor/class-vendor-dashboard.php';
        require_once VENDORPRO_INCLUDES_DIR . 'vendor/class-vendor-products.php';
        require_once VENDORPRO_INCLUDES_DIR . 'vendor/class-vendor-orders.php';
        require_once VENDORPRO_INCLUDES_DIR . 'vendor/class-vendor-earnings.php';
        require_once VENDORPRO_INCLUDES_DIR . 'vendor/class-vendor-profile.php';

        // Frontend includes
        require_once VENDORPRO_INCLUDES_DIR . 'frontend/class-frontend.php';
        require_once VENDORPRO_INCLUDES_DIR . 'frontend/class-vendor-registration.php';
        require_once VENDORPRO_INCLUDES_DIR . 'frontend/class-vendor-store.php';

        // API includes
        require_once VENDORPRO_INCLUDES_DIR . 'api/class-ajax-handler.php';
        require_once VENDORPRO_INCLUDES_DIR . 'api/class-rest-api.php';
    }

    /**
     * Initialize classes
     */
    private function init_classes()
    {
        VendorPro_Database::instance();
        VendorPro_Vendor::instance();
        VendorPro_Commission::instance();
        VendorPro_Reverse_Withdrawal::instance();
        VendorPro_AI_Assist::instance();
        VendorPro_Withdrawal::instance();
        VendorPro_Email::instance();

        if (is_admin()) {
            VendorPro_Admin::instance();
        }

        VendorPro_Vendor_Dashboard::instance();
        VendorPro_Frontend::instance();
        VendorPro_Ajax_Handler::instance();
        VendorPro_REST_API::instance();
    }

    /**
     * Check if required dependencies are active
     */
    public function check_dependencies()
    {
        if (!class_exists('WooCommerce')) {
            add_action('admin_notices', array($this, 'woocommerce_missing_notice'));
            return false;
        }
        return true;
    }

    /**
     * WooCommerce missing notice
     */
    public function woocommerce_missing_notice()
    {
        ?>
        <div class="error">
            <p><?php esc_html_e('VendorPro Marketplace requires WooCommerce to be installed and active.', 'vendorpro'); ?></p>
        </div>
        <?php
    }

    /**
     * Initialize plugin
     */
    public function init()
    {
        // Set up localization
        load_plugin_textdomain('vendorpro', false, dirname(VENDORPRO_PLUGIN_BASENAME) . '/languages');

        // Register custom post types and taxonomies
        $this->register_post_types();

        // Add rewrite rules
        $this->add_rewrite_rules();
    }

    /**
     * Register custom post types
     */
    private function register_post_types()
    {
        // Register vendor store page (if needed)
        flush_rewrite_rules();
    }

    /**
     * Add custom rewrite rules
     */
    private function add_rewrite_rules()
    {
        // Vendor dashboard
        add_rewrite_rule('^vendor-dashboard/?$', 'index.php?vendor_dashboard=home', 'top');
        add_rewrite_rule('^vendor-dashboard/([^/]+)/?$', 'index.php?vendor_dashboard=$matches[1]', 'top');

        // Vendor store
        add_rewrite_rule('^store/([^/]+)/?$', 'index.php?vendor_store=$matches[1]', 'top');

        // Add query vars
        add_filter('query_vars', function ($vars) {
            $vars[] = 'vendor_dashboard';
            $vars[] = 'vendor_store';
            return $vars;
        });
    }

    /**
     * Plugin activation
     */
    public function activate()
    {
        // Check dependencies
        if (!class_exists('WooCommerce')) {
            deactivate_plugins(VENDORPRO_PLUGIN_BASENAME);
            wp_die(esc_html__('VendorPro Marketplace requires WooCommerce to be installed and active.', 'vendorpro'));
        }

        // Run installation
        require_once VENDORPRO_INCLUDES_DIR . 'class-install.php';
        VendorPro_Install::activate();
    }

    /**
     * Plugin deactivation
     */
    public function deactivate()
    {
        flush_rewrite_rules();
    }

    /**
     * Admin notices
     */
    public function admin_notices()
    {
        // Add any admin notices here
    }

    /**
     * Get template
     */
    public function get_template($template_name, $args = array(), $template_path = '', $default_path = '')
    {
        if (!$default_path) {
            $default_path = VENDORPRO_TEMPLATES_DIR;
        }

        if ($args && is_array($args)) {
            extract($args);
        }

        $located = $this->locate_template($template_name, $template_path, $default_path);

        if (!file_exists($located)) {
            return;
        }

        include $located;
    }

    /**
     * Locate template
     */
    public function locate_template($template_name, $template_path = '', $default_path = '')
    {
        if (!$template_path) {
            $template_path = 'vendorpro/';
        }

        if (!$default_path) {
            $default_path = VENDORPRO_TEMPLATES_DIR;
        }

        // Look within passed path within the theme
        $template = locate_template(array(
            trailingslashit($template_path) . $template_name,
            $template_name
        ));

        // Get default template
        if (!$template) {
            $template = $default_path . $template_name;
        }

        return $template;
    }
}

/**
 * Main instance of VendorPro Marketplace
 */
function vendorpro()
{
    return VendorPro_Marketplace::instance();
}

// Initialize the plugin
vendorpro();
