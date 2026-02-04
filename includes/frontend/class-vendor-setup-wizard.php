<?php
/**
 * Vendor Setup Wizard
 */

if (!defined('ABSPATH')) {
    exit;
}

class VendorPro_Vendor_Setup_Wizard
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
        add_action('init', array($this, 'add_rewrite_rules'));
        add_filter('query_vars', array($this, 'add_query_vars'));
        add_action('template_redirect', array($this, 'setup_wizard_controller'));
    }

    /**
     * Add rewrite rules
     */
    public function add_rewrite_rules()
    {
        add_rewrite_rule(
            '^vendor-setup/?$',
            'index.php?vendorpro_setup=1',
            'top'
        );
    }

    /**
     * Add query vars
     */
    public function add_query_vars($vars)
    {
        $vars[] = 'vendorpro_setup';
        return $vars;
    }

    /**
     * Setup Wizard Controller
     */
    public function setup_wizard_controller()
    {
        if (get_query_var('vendorpro_setup')) {

            // Check if user is logged in
            if (!is_user_logged_in()) {
                wp_redirect(wp_login_url(site_url('/vendor-setup/')));
                exit;
            }

            // Check if user is vendor
            if (!VendorPro_Vendor::instance()->is_vendor(get_current_user_id())) {
                wp_redirect(home_url());
                exit;
            }

            // Handle Form Submissions
            $this->handle_setup_actions();

            // Load Template
            $this->load_template();
            exit;
        }
    }

    /**
     * Handle actions
     */
    private function handle_setup_actions()
    {
        if (isset($_POST['vendorpro_setup_step'])) {
            if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'vendorpro_setup_action')) {
                return;
            }

            $step = sanitize_text_field($_POST['vendorpro_setup_step']);
            $user_id = get_current_user_id();
            $vendor = VendorPro_Vendor::instance()->get_vendor_by_user($user_id);
            $vendor_id = $vendor->id;

            if ($step === 'store') {
                // Save Store Settings
                $address = sanitize_textarea_field($_POST['address']);
                $phone = sanitize_text_field($_POST['phone']);
                $paypal = sanitize_email($_POST['paypal']);

                VendorPro_Database::instance()->update_vendor($vendor_id, array(
                    'address' => $address,
                    'phone' => $phone
                ));

                update_user_meta($user_id, 'vendorpro_paypal_email', $paypal);

                // Redirect to next step (or finish)
                wp_redirect(add_query_arg('step', 'finish', site_url('/vendor-setup/')));
                exit;
            }
        }
    }

    /**
     * Load Template
     */
    private function load_template()
    {
        // Simple inline template for now, or include a file
        // Ideally we keep logic here and HTML in templates/frontend/setup-wizard.php

        $logo = get_option('vendorpro_setup_wizard_logo');
        $message = get_option('vendorpro_setup_wizard_message', 'Welcome to the Marketplace!');
        $step = isset($_GET['step']) ? sanitize_text_field($_GET['step']) : 'store';

        // Simple HTML Output
        ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>

        <head>
            <meta charset="<?php bloginfo('charset'); ?>">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>
                <?php _e('Vendor Setup', 'vendorpro'); ?>
            </title>
            <?php wp_head(); ?>
            <style>
                body {
                    background: #f0f2f5;
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Open Sans", "Helvetica Neue", sans-serif;
                }

                .vendorpro-setup-wrap {
                    max-width: 600px;
                    margin: 50px auto;
                    background: #fff;
                    padding: 40px;
                    border-radius: 8px;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                }

                .vendorpro-setup-logo {
                    text-align: center;
                    margin-bottom: 30px;
                }

                .vendorpro-setup-logo img {
                    max-height: 80px;
                }

                .vendorpro-setup-steps {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 30px;
                    border-bottom: 1px solid #eee;
                    padding-bottom: 20px;
                }

                .step {
                    font-weight: bold;
                    color: #ccc;
                }

                .step.active {
                    color: #0071DC;
                }

                .form-row {
                    margin-bottom: 20px;
                }

                .form-row label {
                    display: block;
                    margin-bottom: 8px;
                    font-weight: 600;
                }

                .form-row input,
                .form-row textarea {
                    width: 100%;
                    padding: 10px;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                }

                .btn {
                    background: #0071DC;
                    color: #fff;
                    border: none;
                    padding: 12px 24px;
                    border-radius: 4px;
                    cursor: pointer;
                    font-size: 16px;
                    width: 100%;
                }

                .skip-link {
                    display: block;
                    text-align: center;
                    margin-top: 15px;
                    color: #666;
                    text-decoration: none;
                }
            </style>
        </head>

        <body>
            <div class="vendorpro-setup-wrap">
                <div class="vendorpro-setup-logo">
                    <?php if ($logo): ?>
                        <img src="<?php echo esc_url($logo); ?>" alt="Logo">
                    <?php else: ?>
                        <h1>
                            <?php echo get_bloginfo('name'); ?>
                        </h1>
                    <?php endif; ?>
                </div>

                <?php if ($step === 'store'): ?>
                    <p style="text-align: center; color: #666; margin-bottom: 30px;">
                        <?php echo esc_html($message); ?>
                    </p>

                    <form method="post">
                        <?php wp_nonce_field('vendorpro_setup_action'); ?>
                        <input type="hidden" name="vendorpro_setup_step" value="store">

                        <div class="form-row">
                            <label>
                                <?php _e('Store Address', 'vendorpro'); ?>
                            </label>
                            <textarea name="address" required></textarea>
                        </div>

                        <div class="form-row">
                            <label>
                                <?php _e('Phone Number', 'vendorpro'); ?>
                            </label>
                            <input type="text" name="phone" required>
                        </div>

                        <div class="form-row">
                            <label>
                                <?php _e('PayPal Email', 'vendorpro'); ?>
                            </label>
                            <input type="email" name="paypal" required>
                        </div>

                        <button type="submit" class="btn">
                            <?php _e('Continue', 'vendorpro'); ?>
                        </button>
                    </form>

                <?php elseif ($step === 'finish'): ?>
                    <div style="text-align: center;">
                        <h2>
                            <?php _e('You represent ready!', 'vendorpro'); ?>
                        </h2>
                        <p>
                            <?php _e('Your store is set up and ready to go.', 'vendorpro'); ?>
                        </p>
                        <a href="<?php echo esc_url(get_permalink(get_option('vendorpro_vendor_dashboard_page_id'))); ?>"
                            class="btn" style="display: inline-block; text-decoration: none; margin-top: 20px;">
                            <?php _e('Go to Dashboard', 'vendorpro'); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </body>

        </html>
        <?php
    }
}
