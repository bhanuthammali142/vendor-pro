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

            if (!$vendor) {
                return;
            }

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
        $logo = get_option('vendorpro_setup_wizard_logo');
        $step = isset($_GET['step']) ? sanitize_text_field($_GET['step']) : 'store';

        // Progress Bar Calculation
        $steps = array(
            'store' => __('Store', 'vendorpro'),
            'payment' => __('Payment', 'vendorpro'),
            'finish' => __('Ready!', 'vendorpro')
        );
        $current_step_index = array_search($step, array_keys($steps));
        ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>

        <head>
            <meta charset="<?php bloginfo('charset'); ?>">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title><?php _e('Vendor Setup', 'vendorpro'); ?></title>
            <?php wp_head(); ?>
            <style>
                body {
                    background: #f6f6f6;
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
                    color: #444;
                }

                .vendorpro-setup-wrap {
                    max-width: 700px;
                    margin: 60px auto;
                    background: #fff;
                    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.13);
                    border-radius: 4px;
                    overflow: hidden;
                }

                .vendorpro-setup-header {
                    text-align: center;
                    padding: 30px;
                    border-bottom: 1px solid #eee;
                }

                .vendorpro-progress-bar {
                    display: flex;
                    justify-content: space-between;
                    padding: 20px 40px;
                    background: #fcfcfc;
                    border-bottom: 1px solid #eee;
                }

                .vendorpro-progress-bar li {
                    list-style: none;
                    text-transform: uppercase;
                    font-size: 12px;
                    font-weight: 600;
                    color: #ccc;
                    position: relative;
                    flex: 1;
                    text-align: center;
                }

                .vendorpro-progress-bar li.active {
                    color: #007cba;
                }

                .vendorpro-progress-bar li:before {
                    content: '';
                    width: 10px;
                    height: 10px;
                    background: #ccc;
                    display: block;
                    margin: 0 auto 10px;
                    border-radius: 50%;
                }

                .vendorpro-progress-bar li.active:before {
                    background: #007cba;
                }

                .vendorpro-setup-content {
                    padding: 40px;
                }

                .form-row {
                    margin-bottom: 20px;
                }

                .form-row label {
                    display: block;
                    font-weight: 600;
                    margin-bottom: 8px;
                }

                .form-row input[type="text"],
                .form-row input[type="email"],
                .form-row textarea {
                    width: 100%;
                    padding: 10px 12px;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                    font-size: 14px;
                }

                .form-actions {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-top: 30px;
                }

                .btn {
                    background: #007cba;
                    color: #fff;
                    border: none;
                    padding: 12px 30px;
                    border-radius: 4px;
                    font-size: 14px;
                    cursor: pointer;
                    text-decoration: none;
                    font-weight: 500;
                    transition: background 0.2s;
                }

                .btn:hover {
                    background: #006ba1;
                }

                .btn-secondary {
                    background: #f6f7f7;
                    color: #555;
                    border: 1px solid #ddd;
                }

                .btn-secondary:hover {
                    background: #f0f0f1;
                }
            </style>
        </head>

        <body>
            <div class="vendorpro-setup-wrap">
                <div class="vendorpro-setup-header">
                    <?php if ($logo): ?>
                        <img src="<?php echo esc_url($logo); ?>" alt="Logo" style="max-height: 50px;">
                    <?php else: ?>
                        <h1 style="margin:0; font-size: 24px;"><?php bloginfo('name'); ?></h1>
                    <?php endif; ?>
                </div>

                <ul class="vendorpro-progress-bar">
                    <?php
                    $i = 0;
                    foreach ($steps as $key => $label) {
                        $active_class = ($key === $step || $i <= $current_step_index) ? 'active' : '';
                        echo '<li class="' . $active_class . '">' . esc_html($label) . '</li>';
                        $i++;
                    }
                    ?>
                </ul>

                <div class="vendorpro-setup-content">
                    <?php if ($step === 'store'): ?>
                        <form method="post">
                            <?php wp_nonce_field('vendorpro_setup_action'); ?>
                            <input type="hidden" name="vendorpro_setup_step" value="store">

                            <h2 style="margin-top:0;"><?php _e('Store Setup', 'vendorpro'); ?></h2>
                            <p><?php _e('Details about your store address and contact info.', 'vendorpro'); ?></p>
                            <hr style="margin: 20px 0; border: 0; border-top: 1px solid #eee;">

                            <div class="form-row">
                                <label><?php _e('Store Address', 'vendorpro'); ?></label>
                                <textarea name="address" rows="3" required></textarea>
                            </div>
                            <div class="form-row">
                                <label><?php _e('Phone Number', 'vendorpro'); ?></label>
                                <input type="text" name="phone" required>
                            </div>

                            <div class="form-actions">
                                <a href="<?php echo esc_url(add_query_arg('step', 'payment')); ?>"
                                    class="btn btn-secondary"><?php _e('Skip this step', 'vendorpro'); ?></a>
                                <button type="submit" class="btn"><?php _e('Continue', 'vendorpro'); ?></button>
                            </div>
                        </form>

                    <?php elseif ($step === 'payment'): ?>
                        <form method="post">
                            <?php wp_nonce_field('vendorpro_setup_action'); ?>
                            <input type="hidden" name="vendorpro_setup_step" value="payment">

                            <h2 style="margin-top:0;"><?php _e('Payment Setup', 'vendorpro'); ?></h2>
                            <p><?php _e('How do you want to receive your payments?', 'vendorpro'); ?></p>
                            <hr style="margin: 20px 0; border: 0; border-top: 1px solid #eee;">

                            <div class="form-row">
                                <label><?php _e('PayPal Email', 'vendorpro'); ?></label>
                                <input type="email" name="paypal" required placeholder="you@example.com">
                                <p style="font-size: 12px; color: #888; margin-top: 5px;">
                                    <?php _e('Your commissions will be sent to this PayPal account.', 'vendorpro'); ?></p>
                            </div>

                            <div class="form-actions">
                                <a href="<?php echo esc_url(add_query_arg('step', 'finish')); ?>"
                                    class="btn btn-secondary"><?php _e('Skip this step', 'vendorpro'); ?></a>
                                <button type="submit" class="btn"><?php _e('Continue', 'vendorpro'); ?></button>
                            </div>
                        </form>

                    <?php elseif ($step === 'finish'): ?>
                        <div style="text-align: center; padding: 20px;">
                            <span class="dashicons dashicons-yes-alt"
                                style="font-size: 64px; width: 64px; height: 64px; color: #46b450; margin-bottom: 20px;"></span>
                            <h2><?php _e('You are ready!', 'vendorpro'); ?></h2>
                            <p><?php _e('Your store is set up. You can now start adding products.', 'vendorpro'); ?></p>

                            <div style="margin-top: 40px;">
                                <a href="<?php echo esc_url(get_permalink(get_option('vendorpro_vendor_dashboard_page_id'))); ?>"
                                    class="btn"><?php _e('Go to your Dashboard', 'vendorpro'); ?></a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </body>

        </html>
        <?php
    }
}
