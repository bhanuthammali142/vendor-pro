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
        add_action('wp_enqueue_scripts', array($this, 'enqueue_registration_scripts'), 20);
    }

    /**
     * Enqueue Scripts
     */
    public function enqueue_registration_scripts()
    {
        if (is_account_page() && !is_user_logged_in()) {
            // Ensure jQuery is loaded
            wp_enqueue_script('jquery');

            // Enqueue the main vendorpro script if not already enqueued
            if (!wp_script_is('vendorpro', 'enqueued')) {
                wp_enqueue_script('vendorpro', VENDORPRO_ASSETS_URL . 'js/frontend.js', array('jquery'), VENDORPRO_VERSION, true);
            }

            // Add inline script for vendor registration toggle
            $script = "
            jQuery(document).ready(function($) {
                // Hide vendor fields initially
                $('.vendorpro-vendor-fields').hide();
                
                // Toggle vendor fields based on role selection
                $('input[name=\"role\"]').on('change', function() {
                    if ($(this).val() === 'seller') {
                        $('.vendorpro-vendor-fields').slideDown(300);
                    } else {
                        $('.vendorpro-vendor-fields').slideUp(300);
                    }
                });
                
                // Check if seller is already selected on page load
                if ($('input[name=\"role\"]:checked').val() === 'seller') {
                    $('.vendorpro-vendor-fields').show();
                }
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
        <style>
            .vendorpro-role-selector {
                margin-bottom: 20px;
                padding: 15px;
                background: #f8f9fa;
                border-radius: 8px;
                border: 2px solid #e9ecef;
            }

            .vendorpro-role-selector label:first-child {
                display: block;
                margin-bottom: 12px;
                font-weight: 600;
                color: #333;
                font-size: 15px;
            }

            .vendorpro-role-options {
                display: flex;
                gap: 20px;
            }

            .vendorpro-role-option {
                flex: 1;
                position: relative;
            }

            .vendorpro-role-option input[type="radio"] {
                position: absolute;
                opacity: 0;
            }

            .vendorpro-role-option label {
                display: block;
                padding: 12px 20px;
                background: white;
                border: 2px solid #dee2e6;
                border-radius: 6px;
                text-align: center;
                cursor: pointer;
                transition: all 0.3s ease;
                font-weight: 500;
            }

            .vendorpro-role-option input[type="radio"]:checked+label {
                background: #0071DC;
                color: white;
                border-color: #0071DC;
            }

            .vendorpro-role-option label:hover {
                border-color: #0071DC;
            }

            .vendorpro-vendor-fields {
                margin-top: 20px;
                padding: 20px;
                background: #f8f9fa;
                border-radius: 8px;
                border: 2px solid #e9ecef;
            }

            .vendorpro-vendor-fields h4 {
                margin: 0 0 15px 0;
                color: #333;
                font-size: 16px;
                padding-bottom: 10px;
                border-bottom: 2px solid #dee2e6;
            }

            .vendorpro-field-row {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 15px;
                margin-bottom: 15px;
            }

            .vendorpro-field-row.full-width {
                grid-template-columns: 1fr;
            }

            .vendorpro-shop-url-wrapper {
                display: flex;
                align-items: stretch;
            }

            .vendorpro-shop-url-prefix {
                background: #e9ecef;
                padding: 0 12px;
                border: 1px solid #ced4da;
                border-right: 0;
                border-radius: 4px 0 0 4px;
                display: flex;
                align-items: center;
                font-size: 13px;
                color: #6c757d;
                white-space: nowrap;
            }

            .vendorpro-shop-url-wrapper input {
                border-radius: 0 4px 4px 0 !important;
                flex: 1;
            }

            #url-status {
                display: block;
                margin-top: 5px;
                font-size: 12px;
            }

            #url-status.available {
                color: #28a745;
            }

            #url-status.unavailable {
                color: #dc3545;
            }

            @media (max-width: 768px) {
                .vendorpro-role-options {
                    flex-direction: column;
                }

                .vendorpro-field-row {
                    grid-template-columns: 1fr;
                }
            }
        </style>

        <div class="vendorpro-role-selector">
            <label><?php _e('I want to register as:', 'vendorpro'); ?></label>
            <div class="vendorpro-role-options">
                <div class="vendorpro-role-option">
                    <input type="radio" name="role" id="role_customer" value="customer" checked="checked">
                    <label for="role_customer">
                        <?php _e('👤 Customer', 'vendorpro'); ?>
                    </label>
                </div>
                <div class="vendorpro-role-option">
                    <input type="radio" name="role" id="role_seller" value="seller">
                    <label for="role_seller">
                        <?php _e('🏪 Vendor / Seller', 'vendorpro'); ?>
                    </label>
                </div>
            </div>
        </div>

        <div class="vendorpro-vendor-fields" style="display: none;">
            <h4><?php _e('Vendor Information', 'vendorpro'); ?></h4>

            <div class="vendorpro-field-row">
                <p class="form-row">
                    <label for="reg_first_name"><?php _e('First Name', 'vendorpro'); ?> <span class="required">*</span></label>
                    <input type="text" class="input-text" name="first_name" id="reg_first_name" value="<?php if (!empty($_POST['first_name']))
                        echo esc_attr($_POST['first_name']); ?>" />
                </p>

                <p class="form-row">
                    <label for="reg_last_name"><?php _e('Last Name', 'vendorpro'); ?> <span class="required">*</span></label>
                    <input type="text" class="input-text" name="last_name" id="reg_last_name" value="<?php if (!empty($_POST['last_name']))
                        echo esc_attr($_POST['last_name']); ?>" />
                </p>
            </div>

            <div class="vendorpro-field-row full-width">
                <p class="form-row">
                    <label for="shop_name"><?php _e('Shop Name', 'vendorpro'); ?> <span class="required">*</span></label>
                    <input type="text" class="input-text" name="shop_name" id="shop_name"
                        placeholder="<?php _e('Enter your shop name', 'vendorpro'); ?>" value="<?php if (!empty($_POST['shop_name']))
                               echo esc_attr($_POST['shop_name']); ?>" />
                </p>
            </div>

            <div class="vendorpro-field-row full-width">
                <p class="form-row">
                    <label for="shop_url"><?php _e('Shop URL', 'vendorpro'); ?> <span class="required">*</span></label>
                <div class="vendorpro-shop-url-wrapper">
                    <span class="vendorpro-shop-url-prefix"><?php echo home_url('/store/'); ?></span>
                    <input type="text" class="input-text" name="shop_url" id="shop_url"
                        placeholder="<?php _e('your-shop-name', 'vendorpro'); ?>" value="<?php if (!empty($_POST['shop_url']))
                               echo esc_attr($_POST['shop_url']); ?>" />
                </div>
                <small id="url-status"></small>
                </p>
            </div>

            <div class="vendorpro-field-row full-width">
                <p class="form-row">
                    <label for="shop_phone"><?php _e('Phone Number', 'vendorpro'); ?> <span class="required">*</span></label>
                    <input type="tel" class="input-text" name="shop_phone" id="shop_phone"
                        placeholder="<?php _e('+1 (555) 123-4567', 'vendorpro'); ?>" value="<?php if (!empty($_POST['shop_phone']))
                               echo esc_attr($_POST['shop_phone']); ?>" />
                </p>
            </div>
        </div>

        <script>
            jQuery(function ($) {
                // Auto-generate shop URL from shop name
                $('#shop_name').on('keyup', function () {
                    var val = $(this).val().toLowerCase()
                        .replace(/[^a-z0-9\s-]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/-+/g, '-')
                        .trim();
                    $('#shop_url').val(val);

                    // Show status message
                    if (val.length > 0) {
                        $('#url-status').text('<?php _e('Checking availability...', 'vendorpro'); ?>').removeClass('available unavailable');
                        // TODO: Add AJAX check for URL availability
                    } else {
                        $('#url-status').text('');
                    }
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
                    $errors->add('shop_url_exists', __('Shop URL is not available.', 'vendorpro'));
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
            $user->set_role('vendor'); // Fixed: Use 'vendor' role that's created in install

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
            // Store a transient message for the vendor
            set_transient('vendorpro_new_vendor_' . get_current_user_id(), array(
                'message' => __('Welcome! Your vendor account has been created successfully.', 'vendorpro'),
                'dashboard_url' => vendorpro_get_dashboard_url()
            ), 300); // 5 minutes

            // Redirect to Setup Wizard
            return site_url('/vendor-setup/');
        }
        return $redirect_to;
    }
}

// Initialize
VendorPro_Vendor_Registration::instance();
