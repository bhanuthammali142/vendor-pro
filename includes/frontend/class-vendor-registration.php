<?php
/**
 * Vendor Registration Handler
 * Integrates Vendor Registration into WooCommerce My Account
 */

if (!defined('ABSPATH')) {
    exit;
}

class VendorPro_Vendor_Registration
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
        // 1. Add extra fields to the registration form
        add_action('woocommerce_register_form', array($this, 'add_vendor_registration_fields'));

        // 2. Validate registration fields
        add_filter('woocommerce_registration_errors', array($this, 'validate_vendor_registration_fields'), 10, 3);

        // 3. Save vendor data on user registration
        add_action('woocommerce_created_customer', array($this, 'save_vendor_details'));

        // 4. Redirect after registration
        add_filter('woocommerce_registration_redirect', array($this, 'redirect_to_wizard'));

        // Add scripts for toggling fields
        add_action('wp_enqueue_scripts', array($this, 'enqueue_registration_scripts'));
    }

    /**
     * Enqueue Scripts
     */
    public function enqueue_registration_scripts()
    {
        if (is_account_page() && !is_user_logged_in()) {
            $script = "
            jQuery(document).ready(function($) {
                $('.vendorpro-vendor-fields').hide();
                $('input[name=\"role\"]').change(function() {
                    if ($(this).val() === 'seller') {
                        $('.vendorpro-vendor-fields').slideDown();
                    } else {
                        $('.vendorpro-vendor-fields').slideUp();
                    }
                });
            });
            ";
            wp_add_inline_script('vendorpro', $script);
        }
    }

    /**
     * Add Vendor Registration Fields
     */
    public function add_vendor_registration_fields()
    {
        ?>
        <p class="form-row form-row-wide">
            <label><?php _e('I am a...', 'vendorpro'); ?></label>
            <span style="display: block; margin-top: 5px;">
                <label style="display: inline-block; margin-right: 15px;">
                    <input type="radio" name="role" value="customer" checked="checked">
                    <?php _e('Customer', 'vendorpro'); ?>
                </label>
                <label style="display: inline-block;">
                    <input type="radio" name="role" value="seller">
                    <?php _e('Vendor', 'vendorpro'); ?>
                </label>
            </span>
        </p>

        <div class="vendorpro-vendor-fields"
            style="display: none; border: 1px solid #eee; padding: 15px; border-radius: 5px; margin-bottom: 20px; background: #fafafa;">

            <p class="form-row form-row-first">
                <label for="reg_first_name"><?php _e('First Name', 'vendorpro'); ?> <span class="required">*</span></label>
                <input type="text" class="input-text" name="first_name" id="reg_first_name"
                    value="<?php if (!empty($_POST['first_name']))
                        echo esc_attr($_POST['first_name']); ?>" />
            </p>

            <p class="form-row form-row-last">
                <label for="reg_last_name"><?php _e('Last Name', 'vendorpro'); ?> <span class="required">*</span></label>
                <input type="text" class="input-text" name="last_name" id="reg_last_name"
                    value="<?php if (!empty($_POST['last_name']))
                        echo esc_attr($_POST['last_name']); ?>" />
            </p>

            <p class="form-row form-row-wide">
                <label for="shop_name"><?php _e('Shop Name', 'vendorpro'); ?> <span class="required">*</span></label>
                <input type="text" class="input-text" name="shop_name" id="shop_name"
                    value="<?php if (!empty($_POST['shop_name']))
                        echo esc_attr($_POST['shop_name']); ?>" />
            </p>

            <p class="form-row form-row-wide">
                <label for="shop_url"><?php _e('Shop URL', 'vendorpro'); ?> <span class="required">*</span></label>
                <span style="display: flex; align-items: center;">
                    <span
                        style="background: #f0f0f0; padding: 8px; border: 1px solid #ccc; border-right: 0; font-size: 12px; color: #666;"><?php echo home_url('/store/'); ?></span>
                    <input type="text" class="input-text" name="shop_url" id="shop_url"
                        style="border-top-left-radius: 0; border-bottom-left-radius: 0;"
                        value="<?php if (!empty($_POST['shop_url']))
                            echo esc_attr($_POST['shop_url']); ?>" />
                </span>
                <small id="url-status"></small>
            </p>

            <p class="form-row form-row-wide">
                <label for="shop_phone"><?php _e('Phone Number', 'vendorpro'); ?> <span class="required">*</span></label>
                <input type="text" class="input-text" name="shop_phone" id="shop_phone"
                    value="<?php if (!empty($_POST['shop_phone']))
                        echo esc_attr($_POST['shop_phone']); ?>" />
            </p>
        </div>

        <script>
            jQuery(function ($) {
                $('#shop_name').on('keyup', function () {
                    var val = $(this).val().toLowerCase().replace(/[^a-z0-9]/g, '-');
                    $('#shop_url').val(val);
                    // Check availability ajax could go here
                });
            });
        </script>
        <?php
    }

    /**
     * Validate Fields
     */
    public function validate_vendor_registration_fields($errors, $username, $email)
    {
        if (isset($_POST['role']) && $_POST['role'] === 'seller') {
            if (empty($_POST['first_name'])) {
                $errors->add('first_name_error', __('First Name is required.', 'vendorpro'));
            }
            if (empty($_POST['last_name'])) {
                $errors->add('last_name_error', __('Last Name is required.', 'vendorpro'));
            }
            if (empty($_POST['shop_name'])) {
                $errors->add('shop_name_error', __('Shop Name is required.', 'vendorpro'));
            }
            if (empty($_POST['shop_url'])) {
                $errors->add('shop_url_error', __('Shop URL is required.', 'vendorpro'));
            } else {
                // Check if slug exists
                $vendor = VendorPro_Database::instance()->get_vendor_by_slug(sanitize_title($_POST['shop_url']));
                if ($vendor) {
                    $errors->add('shop_url_exists', __('Shop URL is already available.', 'vendorpro'));
                }
            }
            if (empty($_POST['shop_phone'])) {
                $errors->add('shop_phone_error', __('Phone Number is required.', 'vendorpro'));
            }
        }
        return $errors;
    }

    /**
     * Save Vendor Details
     */
    public function save_vendor_details($user_id)
    {
        if (isset($_POST['role']) && $_POST['role'] === 'seller') {

            // Set User Role to Vendor
            $user = new WP_User($user_id);
            $user->set_role('vendorpro_vendor');

            // Save basic meta
            update_user_meta($user_id, 'first_name', sanitize_text_field($_POST['first_name']));
            update_user_meta($user_id, 'last_name', sanitize_text_field($_POST['last_name']));
            update_user_meta($user_id, 'billing_phone', sanitize_text_field($_POST['shop_phone']));

            // Generate Token
            $token = wp_generate_password(20, false);

            // Create Vendor Record in Custom Table
            $vendor_data = array(
                'user_id' => $user_id,
                'store_name' => sanitize_text_field($_POST['shop_name']),
                'store_slug' => sanitize_title($_POST['shop_url']),
                'email' => $user->user_email,
                'phone' => sanitize_text_field($_POST['shop_phone']),
                'status' => 'pending', // Or 'approved' based on settings
                'commission_rate' => vendorpro_get_commission_rate(),
                'commission_type' => vendorpro_get_commission_type(),
                'logo' => '',
                'banner' => '',
                'address' => '',
                'description' => '',
                'token' => $token,
                'featured' => 0
            );

            // Check auto-approve setting
            if (get_option('vendorpro_vendor_approval', 'yes') === 'no') {
                $vendor_data['status'] = 'approved';
            }

            VendorPro_Database::instance()->insert_vendor($vendor_data);
        }
    }

    /**
     * Redirect to Wizard
     */
    public function redirect_to_wizard($redirect_to)
    {
        if (isset($_POST['role']) && $_POST['role'] === 'seller') {
            // Check if vendor data saved successfully? 
            // We assume save_vendor_detailsran first.

            // Redirect to Setup Wizard
            return site_url('/vendor-setup/');
        }
        return $redirect_to;
    }
}

// Initialize
VendorPro_Vendor_Registration::instance();
