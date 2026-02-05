<?php
/**
 * Plugin Name: VendorPro Marketplace
 * Plugin URI: https://vendorpro-marketplace.com
 * Description: A complete multi-vendor marketplace solution for WordPress & WooCommerce. Enable vendors to sell products on your site with commission management, vendor dashboards, and more.
 * Version: 1.6.1
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
define('VENDORPRO_VERSION', '1.6.1');
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
     * Stores error messages
     */
    private $errors = array();

    /**
     * Flag to check if dependencies are met
     */
    private $dependencies_met = false;

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

        // Only load plugin if dependencies are met
        add_action('plugins_loaded', array($this, 'init_plugin'), 5);
    }

    /**
     * Hook into actions and filters
     */
    private function init_hooks()
    {
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));

        add_action('admin_notices', array($this, 'admin_notices'));

        // Declare WooCommerce HPOS compatibility
        add_action('before_woocommerce_init', array($this, 'declare_wc_compatibility'));
    }

    /**
     * Initialize plugin after checking dependencies
     */
    public function init_plugin()
    {
        // Check dependencies first
        if (!$this->check_dependencies()) {
            return;
        }

        $this->dependencies_met = true;

        // Load plugin files
        if (!$this->includes()) {
            $this->add_error('Failed to load required plugin files. Please check file permissions and ensure all plugin files are present.');
            return;
        }

        // Initialize classes
        if (!$this->init_classes()) {
            $this->add_error('Failed to initialize plugin classes. There may be a conflict with another plugin or theme.');
            return;
        }

        // Initialize plugin
        add_action('init', array($this, 'init'), 0);
    }

    /**
     * Declare WooCommerce HPOS compatibility
     */
    public function declare_wc_compatibility()
    {
        if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
            try {
                \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
                \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('orders_cache', __FILE__, true);
            } catch (Exception $e) {
                // Silently fail - not critical
            }
        }
    }

    /**
     * Safely include a file
     */
    private function safe_include($file)
    {
        if (!file_exists($file)) {
            $this->add_error("Required file not found: " . basename($file));
            return false;
        }

        try {
            require_once $file;
            return true;
        } catch (Exception $e) {
            $this->add_error("Error loading file " . basename($file) . ": " . $e->getMessage());
            return false;
        }
    }

    /**
     * Include required core files
     */
    private function includes()
    {
        $core_files = array(
            VENDORPRO_INCLUDES_DIR . 'class-install.php',
            VENDORPRO_INCLUDES_DIR . 'class-database.php',
            VENDORPRO_INCLUDES_DIR . 'class-vendor.php',
            VENDORPRO_INCLUDES_DIR . 'class-commission.php',
            VENDORPRO_INCLUDES_DIR . 'class-reverse-withdrawal.php',
            VENDORPRO_INCLUDES_DIR . 'class-ai-assist.php',
            VENDORPRO_INCLUDES_DIR . 'class-withdrawal.php',
            VENDORPRO_INCLUDES_DIR . 'class-email.php',
            VENDORPRO_INCLUDES_DIR . 'functions.php',
        );

        // Load core files
        foreach ($core_files as $file) {
            if (!$this->safe_include($file)) {
                return false;
            }
        }

        // Admin includes
        if (is_admin()) {
            $admin_files = array(
                VENDORPRO_INCLUDES_DIR . 'admin/class-admin.php',
                VENDORPRO_INCLUDES_DIR . 'admin/class-admin-settings.php',
                VENDORPRO_INCLUDES_DIR . 'admin/class-admin-vendors.php',
                VENDORPRO_INCLUDES_DIR . 'admin/class-admin-commissions.php',
                VENDORPRO_INCLUDES_DIR . 'admin/class-admin-withdrawals.php',
                VENDORPRO_INCLUDES_DIR . 'admin/class-admin-reverse-withdrawal.php',
                VENDORPRO_INCLUDES_DIR . 'admin/class-admin-modules.php',
                VENDORPRO_INCLUDES_DIR . 'admin/class-admin-status.php',
                VENDORPRO_INCLUDES_DIR . 'admin/class-admin-help.php',
            );

            foreach ($admin_files as $file) {
                if (!$this->safe_include($file)) {
                    return false;
                }
            }
        }

        // Vendor dashboard includes
        $vendor_files = array(
            VENDORPRO_INCLUDES_DIR . 'vendor/class-vendor-dashboard.php',
            VENDORPRO_INCLUDES_DIR . 'vendor/class-vendor-products.php',
            VENDORPRO_INCLUDES_DIR . 'vendor/class-vendor-orders.php',
            VENDORPRO_INCLUDES_DIR . 'vendor/class-vendor-earnings.php',
            VENDORPRO_INCLUDES_DIR . 'vendor/class-vendor-profile.php',
        );

        foreach ($vendor_files as $file) {
            if (!$this->safe_include($file)) {
                return false;
            }
        }

        // Frontend includes
        $frontend_files = array(
            VENDORPRO_INCLUDES_DIR . 'frontend/class-frontend.php',
            VENDORPRO_INCLUDES_DIR . 'frontend/class-vendor-registration.php',
            VENDORPRO_INCLUDES_DIR . 'frontend/class-vendor-setup-wizard.php',
            VENDORPRO_INCLUDES_DIR . 'frontend/class-my-account-menu.php',
        );

        foreach ($frontend_files as $file) {
            if (!$this->safe_include($file)) {
                return false;
            }
        }

        // API includes
        $api_files = array(
            VENDORPRO_INCLUDES_DIR . 'api/class-ajax-handler.php',
            VENDORPRO_INCLUDES_DIR . 'api/class-rest-api.php',
        );

        foreach ($api_files as $file) {
            if (!$this->safe_include($file)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Initialize classes with error handling
     */
    private function init_classes()
    {
        $classes = array(
            'VendorPro_Database',
            'VendorPro_Vendor',
            'VendorPro_Commission',
            'VendorPro_Reverse_Withdrawal',
            'VendorPro_AI_Assist',
            'VendorPro_Withdrawal',
            'VendorPro_Email',
        );

        // Initialize core classes
        foreach ($classes as $class) {
            if (!class_exists($class)) {
                $this->add_error("Required class not found: {$class}");
                return false;
            }

            try {
                $class::instance();
            } catch (Exception $e) {
                $this->add_error("Error initializing {$class}: " . $e->getMessage());
                return false;
            }
        }

        // Initialize admin classes
        if (is_admin()) {
            if (class_exists('VendorPro_Admin')) {
                try {
                    VendorPro_Admin::instance();
                } catch (Exception $e) {
                    $this->add_error("Error initializing admin: " . $e->getMessage());
                }
            }
        }

        // Initialize frontend classes
        $frontend_classes = array(
            'VendorPro_Vendor_Dashboard',
            'VendorPro_Frontend',
            'VendorPro_Vendor_Setup_Wizard',
            'VendorPro_Ajax_Handler',
            'VendorPro_REST_API',
        );

        foreach ($frontend_classes as $class) {
            if (class_exists($class)) {
                try {
                    $class::instance();
                } catch (Exception $e) {
                    $this->add_error("Error initializing {$class}: " . $e->getMessage());
                }
            }
        }

        return true;
    }

    /**
     * Check if required dependencies are active
     */
    public function check_dependencies()
    {
        // Check PHP version
        if (version_compare(PHP_VERSION, '7.4', '<')) {
            $this->add_error('VendorPro Marketplace requires PHP 7.4 or higher. You are running PHP ' . PHP_VERSION);
            return false;
        }

        // Check WordPress version
        global $wp_version;
        if (version_compare($wp_version, '5.8', '<')) {
            $this->add_error('VendorPro Marketplace requires WordPress 5.8 or higher. You are running WordPress ' . $wp_version);
            return false;
        }

        // Check WooCommerce
        if (!class_exists('WooCommerce')) {
            $this->add_error('VendorPro Marketplace requires WooCommerce to be installed and active. Please install and activate WooCommerce first.');
            return false;
        }

        // Check WooCommerce version
        if (defined('WC_VERSION') && version_compare(WC_VERSION, '5.0', '<')) {
            $this->add_error('VendorPro Marketplace requires WooCommerce 5.0 or higher. You are running WooCommerce ' . WC_VERSION);
            return false;
        }

        return true;
    }

    /**
     * Add error message
     */
    private function add_error($message)
    {
        $this->errors[] = $message;
    }

    /**
     * Initialize plugin
     */
    public function init()
    {
        if (!$this->dependencies_met) {
            return;
        }

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
        // Rules are now registered in their respective classes
        // VendorPro_Vendor_Dashboard handles dashboard rules
        // VendorPro_Frontend handles store rules
    }

    /**
     * Plugin activation
     */
    public function activate()
    {
        // Check PHP version
        if (version_compare(PHP_VERSION, '7.4', '<')) {
            deactivate_plugins(VENDORPRO_PLUGIN_BASENAME);
            wp_die(
                '<h1>Plugin Activation Failed</h1>' .
                '<p><strong>VendorPro Marketplace</strong> requires PHP 7.4 or higher.</p>' .
                '<p>You are currently running PHP ' . PHP_VERSION . '</p>' .
                '<p>Please contact your hosting provider to upgrade PHP.</p>' .
                '<p><a href="' . admin_url('plugins.php') . '">&laquo; Back to Plugins</a></p>'
            );
        }

        // Check WordPress version
        global $wp_version;
        if (version_compare($wp_version, '5.8', '<')) {
            deactivate_plugins(VENDORPRO_PLUGIN_BASENAME);
            wp_die(
                '<h1>Plugin Activation Failed</h1>' .
                '<p><strong>VendorPro Marketplace</strong> requires WordPress 5.8 or higher.</p>' .
                '<p>You are currently running WordPress ' . $wp_version . '</p>' .
                '<p>Please update WordPress to the latest version.</p>' .
                '<p><a href="' . admin_url('plugins.php') . '">&laquo; Back to Plugins</a></p>'
            );
        }

        // Check WooCommerce
        if (!class_exists('WooCommerce')) {
            deactivate_plugins(VENDORPRO_PLUGIN_BASENAME);
            wp_die(
                '<h1>Plugin Activation Failed</h1>' .
                '<p><strong>VendorPro Marketplace</strong> requires WooCommerce to be installed and active.</p>' .
                '<p>Please install and activate WooCommerce before activating this plugin.</p>' .
                '<p><a href="' . admin_url('plugin-install.php?s=woocommerce&tab=search&type=term') . '">Install WooCommerce</a> | ' .
                '<a href="' . admin_url('plugins.php') . '">Back to Plugins</a></p>'
            );
        }

        // Check WooCommerce version
        if (defined('WC_VERSION') && version_compare(WC_VERSION, '5.0', '<')) {
            deactivate_plugins(VENDORPRO_PLUGIN_BASENAME);
            wp_die(
                '<h1>Plugin Activation Failed</h1>' .
                '<p><strong>VendorPro Marketplace</strong> requires WooCommerce 5.0 or higher.</p>' .
                '<p>You are currently running WooCommerce ' . WC_VERSION . '</p>' .
                '<p>Please update WooCommerce to the latest version.</p>' .
                '<p><a href="' . admin_url('plugins.php') . '">&laquo; Back to Plugins</a></p>'
            );
        }

        // Run installation with error handling
        try {
            $install_file = VENDORPRO_INCLUDES_DIR . 'class-install.php';
            if (file_exists($install_file)) {
                require_once $install_file;
                if (class_exists('VendorPro_Install')) {
                    VendorPro_Install::activate();
                }
            }
        } catch (Exception $e) {
            deactivate_plugins(VENDORPRO_PLUGIN_BASENAME);
            wp_die(
                '<h1>Plugin Activation Failed</h1>' .
                '<p><strong>VendorPro Marketplace</strong> encountered an error during activation:</p>' .
                '<p>' . esc_html($e->getMessage()) . '</p>' .
                '<p>Please contact support with this error message.</p>' .
                '<p><a href="' . admin_url('plugins.php') . '">&laquo; Back to Plugins</a></p>'
            );
        }
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
        // Display any errors
        if (!empty($this->errors)) {
            foreach ($this->errors as $error) {
                ?>
                <div class="notice notice-error is-dismissible">
                    <p><strong>VendorPro Marketplace Error:</strong> <?php echo esc_html($error); ?></p>
                </div>
                <?php
            }
        }

        // Show helpful notice if plugin is not fully loaded
        if (!$this->dependencies_met && current_user_can('activate_plugins')) {
            ?>
            <div class="notice notice-warning">
                <p><strong>VendorPro Marketplace</strong> is installed but not fully active. Please check the error messages above
                    and resolve any issues.</p>
                <p><em>Tip: If you recently added a cache plugin or made changes, try temporarily renaming the plugin folder to
                        deactivate it, then rename it back.</em></p>
            </div>
            <?php
        }
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
