<?php
/**
 * Admin Settings Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class VendorPro_Admin_Settings
{

    /**
     * Render settings page
     */
    public static function render_page()
    {
        $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';

        // Save settings if posted
        if (isset($_POST['vendorpro_settings_nonce']) && wp_verify_nonce($_POST['vendorpro_settings_nonce'], 'vendorpro_save_settings')) {
            self::save_settings();
        }

        $tabs = self::get_settings_tabs();
        ?>
        <div class="wrap vendorpro-settings-wrap">
            <h1 class="wp-heading-inline"><?php _e('VendorPro Settings', 'vendorpro'); ?></h1>
            <p class="vendorpro-settings-description">
                <?php _e('Configure your marketplace settings, modules, and integrations.', 'vendorpro'); ?>
            </p>

            <div class="vendorpro-settings-container">
                <!-- Vertical Navigation -->
                <div class="vendorpro-settings-nav">
                    <ul>
                        <?php foreach ($tabs as $key => $tab): ?>
                            <li class="<?php echo $active_tab == $key ? 'active' : ''; ?>">
                                <a href="<?php echo admin_url('admin.php?page=vendorpro-settings&tab=' . $key); ?>">
                                    <span class="dashicons <?php echo $tab['icon']; ?>"></span>
                                    <span class="tab-label"><?php echo $tab['label']; ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Settings Content -->
                <div class="vendorpro-settings-content">
                    <form method="post" action="">
                        <?php wp_nonce_field('vendorpro_save_settings', 'vendorpro_settings_nonce'); ?>
                        <input type="hidden" name="active_tab" value="<?php echo $active_tab; ?>">

                        <div class="vendorpro-card">
                            <div class="vendorpro-card-header">
                                <h2><?php echo $tabs[$active_tab]['label']; ?></h2>
                                <p class="description"><?php echo $tabs[$active_tab]['desc']; ?></p>
                            </div>

                            <div class="vendorpro-card-body">
                                <?php self::render_tab_content($active_tab); ?>
                            </div>

                            <div class="vendorpro-card-footer">
                                <button type="submit"
                                    class="button button-primary button-hero"><?php _e('Save Changes', 'vendorpro'); ?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Get settings tabs
     */
    private static function get_settings_tabs()
    {
        return array(
            'general' => array(
                'label' => __('General', 'vendorpro'),
                'icon' => 'dashicons-admin-settings',
                'desc' => __('Site settings and store options', 'vendorpro')
            ),
            'selling' => array(
                'label' => __('Selling Options', 'vendorpro'),
                'icon' => 'dashicons-cart',
                'desc' => __('Store settings, commissions, and fees', 'vendorpro')
            ),
            'withdraw' => array(
                'label' => __('Withdraw Options', 'vendorpro'),
                'icon' => 'dashicons-money-alt',
                'desc' => __('Withdraw settings and thresholds', 'vendorpro')
            ),
            'reverse_withdraw' => array(
                'label' => __('Reverse Withdrawal', 'vendorpro'),
                'icon' => 'dashicons-update',
                'desc' => __('Admin commission config (Enable for COD)', 'vendorpro')
            ),
            'pages' => array(
                'label' => __('Page Settings', 'vendorpro'),
                'icon' => 'dashicons-admin-page',
                'desc' => __('Configure marketplace pages', 'vendorpro')
            ),
            'appearance' => array(
                'label' => __('Appearance', 'vendorpro'),
                'icon' => 'dashicons-art', // or dashicons-layout
                'desc' => __('Custom store appearance and map settings', 'vendorpro')
            ),
            'privacy' => array(
                'label' => __('Privacy Policy', 'vendorpro'),
                'icon' => 'dashicons-shield',
                'desc' => __('Update store privacy policies', 'vendorpro')
            ),
            'ai_assist' => array(
                'label' => __('AI Assist', 'vendorpro'),
                'icon' => 'dashicons-superhero',
                'desc' => __('Set up AI to elevate your platform', 'vendorpro')
            ),
        );
    }

    /**
     * Save settings
     */
    private static function save_settings()
    {
        $tab = isset($_POST['active_tab']) ? sanitize_text_field($_POST['active_tab']) : 'general';

        switch ($tab) {
            case 'general':
                // Site Settings
                update_option('vendorpro_admin_access', isset($_POST['vendorpro_admin_access']) ? 'yes' : 'no');
                
                // Flush rewriting rules if slug changes
                $old_slug = get_option('vendorpro_store_url_slug', 'store');
                $new_slug = sanitize_text_field($_POST['vendorpro_store_url_slug']);
                if ($old_slug !== $new_slug) {
                    update_option('vendorpro_flush_rewrite_rules', true);
                }
                update_option('vendorpro_store_url_slug', $new_slug);
                
                update_option('vendorpro_setup_wizard_logo', sanitize_text_field($_POST['vendorpro_setup_wizard_logo']));
                update_option('vendorpro_setup_wizard_message', sanitize_textarea_field($_POST['vendorpro_setup_wizard_message']));
                update_option('vendorpro_disable_welcome_wizard', isset($_POST['vendorpro_disable_welcome_wizard']) ? 'yes' : 'no');
                update_option('vendorpro_data_clear', isset($_POST['vendorpro_data_clear']) ? 'yes' : 'no');
                
                // Vendor Store Settings
                update_option('vendorpro_store_terms', isset($_POST['vendorpro_store_terms']) ? 'yes' : 'no');
                update_option('vendorpro_store_products_per_page', intval($_POST['vendorpro_store_products_per_page']));
                update_option('vendorpro_enable_address_fields', isset($_POST['vendorpro_enable_address_fields']) ? 'yes' : 'no');
                
                // Keep these for legacy or move them if we removed from UI? 
                // We removed them from UI in the previous step (Allow Registration/Approval).
                // But we should default them or hidden them. Let's assume they are effectively "Managed elsewhere" or we keep the old values if not posted.
                // Actually, if we remove them from UI, we can't update them here via POST.
                
                // Product Page Settings
                update_option('vendorpro_show_vendor_info', isset($_POST['vendorpro_show_vendor_info']) ? 'yes' : 'no');
                update_option('vendorpro_enable_more_products_tab', isset($_POST['vendorpro_enable_more_products_tab']) ? 'yes' : 'no');
                break;

            case 'selling':
                update_option('vendorpro_commission_rate', floatval($_POST['vendorpro_commission_rate']));
                update_option('vendorpro_commission_fixed_amt', floatval($_POST['vendorpro_commission_fixed_amt']));
                update_option('vendorpro_commission_type', sanitize_text_field($_POST['vendorpro_commission_type']));
                update_option('vendorpro_shipping_recipient', sanitize_text_field($_POST['vendorpro_shipping_recipient']));
                update_option('vendorpro_tax_recipient', sanitize_text_field($_POST['vendorpro_tax_recipient']));

                // Capabilities
                update_option('vendorpro_product_status', sanitize_text_field($_POST['vendorpro_product_status']));
                update_option('vendorpro_can_change_order_status', isset($_POST['vendorpro_can_change_order_status']) ? 'yes' : 'no');
                update_option('vendorpro_catalog_mode', isset($_POST['vendorpro_catalog_mode']) ? 'yes' : 'no');
                break;

            case 'withdraw':
                update_option('vendorpro_min_withdraw_amount', floatval($_POST['vendorpro_min_withdraw_amount']));
                update_option('vendorpro_withdraw_paypal', isset($_POST['vendorpro_withdraw_paypal']) ? 'yes' : 'no');
                update_option('vendorpro_withdraw_bank', isset($_POST['vendorpro_withdraw_bank']) ? 'yes' : 'no');

                update_option('vendorpro_withdraw_charge_percent', floatval($_POST['vendorpro_withdraw_charge_percent']));
                update_option('vendorpro_withdraw_charge_fixed', floatval($_POST['vendorpro_withdraw_charge_fixed']));

                $statuses = isset($_POST['vendorpro_withdraw_order_status']) ? array_map('sanitize_text_field', $_POST['vendorpro_withdraw_order_status']) : array();
                update_option('vendorpro_withdraw_order_status', $statuses);

                update_option('vendorpro_exclude_cod_withdraw', isset($_POST['vendorpro_exclude_cod_withdraw']) ? 'yes' : 'no');
                break;

            case 'reverse_withdraw':
                update_option('vendorpro_enable_reverse_withdrawal', isset($_POST['vendorpro_enable_reverse_withdrawal']) ? 'yes' : 'no');
                update_option('vendorpro_reverse_threshold', floatval($_POST['vendorpro_reverse_threshold']));
                update_option('vendorpro_reverse_grace_period', intval($_POST['vendorpro_reverse_grace_period']));

                // Actions
                update_option('vendorpro_reverse_disable_atc', isset($_POST['vendorpro_reverse_disable_atc']) ? 'yes' : 'no');
                update_option('vendorpro_reverse_hide_withdraw', isset($_POST['vendorpro_reverse_hide_withdraw']) ? 'yes' : 'no');
                update_option('vendorpro_reverse_inactive_vendor', isset($_POST['vendorpro_reverse_inactive_vendor']) ? 'yes' : 'no');
                break;

            case 'pages':
                update_option('vendorpro_vendor_dashboard_page_id', intval($_POST['vendorpro_vendor_dashboard_page_id']));
                update_option('vendorpro_vendor_registration_page_id', intval($_POST['vendorpro_vendor_registration_page_id']));
                update_option('vendorpro_vendors_page_id', intval($_POST['vendorpro_vendors_page_id']));
                break;

            case 'appearance':
                update_option('vendorpro_map_api_source', sanitize_text_field($_POST['vendorpro_map_api_source']));
                update_option('vendorpro_google_maps_api_key', sanitize_text_field($_POST['vendorpro_google_maps_api_key']));
                update_option('vendorpro_store_header_template', sanitize_text_field($_POST['vendorpro_store_header_template']));
                update_option('vendorpro_show_contact_form', isset($_POST['vendorpro_show_contact_form']) ? 'yes' : 'no');
                break;

            case 'ai_assist':
                update_option('vendorpro_openai_api_key', sanitize_text_field($_POST['vendorpro_openai_api_key']));
                update_option('vendorpro_ai_model', sanitize_text_field($_POST['vendorpro_ai_model']));
                break;
        }

        echo '<div class="notice notice-success is-dismissible"><p>' . __('Settings saved successfully.', 'vendorpro') . '</p></div>';
    }

    /**
     * Render tab content
     */
    private static function render_tab_content($tab)
    {
        switch ($tab) {
            case 'general':
                self::render_general_settings();
                break;
            case 'selling':
                self::render_selling_settings();
                break;
            case 'withdraw':
                self::render_withdraw_settings();
                break;
            case 'reverse_withdraw':
                self::render_reverse_withdraw_settings(); // New
                break;
            case 'pages':
                self::render_page_settings();
                break;
            case 'appearance':
                self::render_appearance_settings(); // New
                break;
            case 'privacy':
                self::render_privacy_settings(); // New
                break;
            case 'ai_assist':
                self::render_ai_settings(); // New
                break;
            default:
                self::render_general_settings();
                break;
        }
    }

    // ... Existing render methods (General, Selling, Withdraw, Pages) ...
    // Keeping existing methods but wrapping inputs in better HTML structure if needed

    private static function render_general_settings()
    {
        ?>
        <div class="vendorpro-feature-intro">
            <h3><?php _e('Site Settings', 'vendorpro'); ?></h3>
            <p><?php _e('Configure your site settings and control access to your site.', 'vendorpro'); ?></p>
        </div>

        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Admin Area Access', 'vendorpro'); ?></th>
                <td>
                    <label class="vendorpro-toggle">
                        <input type="checkbox" name="vendorpro_admin_access" value="yes" <?php checked(get_option('vendorpro_admin_access', 'yes'), 'yes'); ?>>
                        <span class="slider round"></span>
                    </label>
                    <p class="description">
                        <?php _e('Prevent vendors from accessing the wp-admin dashboard area.', 'vendorpro'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Vendor Store URL', 'vendorpro'); ?></th>
                <td>
                    <div style="display: flex; align-items: center;">
                        <span
                            style="background: #eee; padding: 6px 10px; border: 1px solid #ddd; border-right: 0; border-radius: 3px 0 0 3px; color: #555;"><?php echo home_url('/'); ?></span>
                        <input type="text" name="vendorpro_store_url_slug"
                            value="<?php echo esc_attr(get_option('vendorpro_store_url_slug', 'store')); ?>"
                            class="regular-text" style="border-radius: 0 3px 3px 0;">
                        <span
                            style="background: #eee; padding: 6px 10px; border: 1px solid #ddd; border-left: 0; border-radius: 0 3px 3px 0; color: #555;">/[vendor-name]</span>
                    </div>
                    <p class="description"><?php _e('Define the vendor store URL.', 'vendorpro'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Vendor Setup Wizard Logo', 'vendorpro'); ?></th>
                <td>
                    <input type="text" name="vendorpro_setup_wizard_logo"
                        value="<?php echo esc_attr(get_option('vendorpro_setup_wizard_logo')); ?>" class="regular-text"
                        placeholder="Paste image URL">
                    <button type="button" class="button"><?php _e('Choose File', 'vendorpro'); ?></button>
                    <p class="description"><?php _e('Recommended logo size (270px X 90px).', 'vendorpro'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Vendor Setup Wizard Message', 'vendorpro'); ?></th>
                <td>
                    <textarea name="vendorpro_setup_wizard_message" rows="3"
                        class="large-text"><?php echo esc_textarea(get_option('vendorpro_setup_wizard_message', 'Thank you for choosing The Marketplace to power your online store!')); ?></textarea>
                </td>
            </tr>
        </table>

        <div class="vendorpro-section-divider"></div>

        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Disable Welcome Wizard', 'vendorpro'); ?></th>
                <td>
                    <label class="vendorpro-toggle">
                        <input type="checkbox" name="vendorpro_disable_welcome_wizard" value="yes" <?php checked(get_option('vendorpro_disable_welcome_wizard'), 'yes'); ?>>
                        <span class="slider round"></span>
                    </label>
                    <p class="description"><?php _e('Disable welcome wizard for newly registered vendors', 'vendorpro'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Data Clear', 'vendorpro'); ?></th>
                <td>
                    <label class="vendorpro-toggle">
                        <input type="checkbox" name="vendorpro_data_clear" value="yes" <?php checked(get_option('vendorpro_data_clear'), 'yes'); ?>>
                        <span class="slider round"></span>
                    </label>
                    <p class="description" style="color: #dc3232;">
                        <?php _e('Delete all data and tables related to VendorPro when deleting the plugin.', 'vendorpro'); ?>
                    </p>
                </td>
            </tr>
        </table>

        <h3><?php _e('Vendor Store Settings', 'vendorpro'); ?></h3>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Store Terms and Conditions', 'vendorpro'); ?></th>
                <td>
                    <label class="vendorpro-toggle">
                        <input type="checkbox" name="vendorpro_store_terms" value="yes" <?php checked(get_option('vendorpro_store_terms'), 'yes'); ?>>
                        <span class="slider round"></span>
                    </label>
                    <p class="description"><?php _e('Enable terms and conditions for vendor stores', 'vendorpro'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Store Products Per Page', 'vendorpro'); ?></th>
                <td>
                    <input type="number" name="vendorpro_store_products_per_page"
                        value="<?php echo esc_attr(get_option('vendorpro_store_products_per_page', 12)); ?>"
                        class="regular-text">
                    <p class="description">
                        <?php _e('Set how many products to display per page on the vendor store page.', 'vendorpro'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Enable Address Fields', 'vendorpro'); ?></th>
                <td>
                    <label class="vendorpro-toggle">
                        <input type="checkbox" name="vendorpro_enable_address_fields" value="yes" <?php checked(get_option('vendorpro_enable_address_fields'), 'yes'); ?>>
                        <span class="slider round"></span>
                    </label>
                    <p class="description"><?php _e('Add Address Fields on the Vendor Registration form', 'vendorpro'); ?></p>
                </td>
            </tr>
        </table>

        <h3><?php _e('Product Page Settings', 'vendorpro'); ?></h3>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Show Vendor Info', 'vendorpro'); ?></th>
                <td>
                    <label class="vendorpro-toggle">
                        <input type="checkbox" name="vendorpro_show_vendor_info" value="yes" <?php checked(get_option('vendorpro_show_vendor_info', 'yes'), 'yes'); ?>>
                        <span class="slider round"></span>
                    </label>
                    <p class="description"><?php _e('Show vendor information on single product page', 'vendorpro'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Enable More Products Tab', 'vendorpro'); ?></th>
                <td>
                    <label class="vendorpro-toggle">
                        <input type="checkbox" name="vendorpro_enable_more_products_tab" value="yes" <?php checked(get_option('vendorpro_enable_more_products_tab', 'yes'), 'yes'); ?>>
                        <span class="slider round"></span>
                    </label>
                    <p class="description"><?php _e('Enable "More Products" tab on the single product page.', 'vendorpro'); ?>
                    </p>
                </td>
            </tr>
        </table>
        <?php
    }

    private static function render_selling_settings()
    {
        ?>
        <div class="vendorpro-feature-intro">
            <h3><?php _e('Commission Settings', 'vendorpro'); ?></h3>
            <p><?php _e('Define commission types, fees, and who receives tax/shipping.', 'vendorpro'); ?></p>
        </div>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Commission Type', 'vendorpro'); ?></th>
                <td>
                    <select name="vendorpro_commission_type" class="regular-text">
                        <option value="percentage" <?php selected(get_option('vendorpro_commission_type', 'percentage'), 'percentage'); ?>><?php _e('Percentage', 'vendorpro'); ?></option>
                        <option value="fixed" <?php selected(get_option('vendorpro_commission_type'), 'fixed'); ?>>
                            <?php _e('Fixed Amount', 'vendorpro'); ?>
                        </option>
                        <option value="combined" <?php selected(get_option('vendorpro_commission_type'), 'combined'); ?>>
                            <?php _e('Combined (Percentage + Fixed)', 'vendorpro'); ?>
                        </option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Commission Amount', 'vendorpro'); ?></th>
                <td>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <input type="number" step="0.01" name="vendorpro_commission_rate"
                            value="<?php echo esc_attr(get_option('vendorpro_commission_rate', 10)); ?>" style="width: 100px;">
                        <span>%</span>
                        <span>+</span>
                        <input type="number" step="0.01" name="vendorpro_commission_fixed_amt"
                            value="<?php echo esc_attr(get_option('vendorpro_commission_fixed_amt', 0)); ?>"
                            style="width: 100px;">
                        <span><?php echo get_woocommerce_currency_symbol(); ?></span>
                    </div>
                    <p class="description">
                        <?php _e('Enter percentage and/or fixed amount depending on commission type.', 'vendorpro'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Shipping Fee Recipient', 'vendorpro'); ?></th>
                <td>
                    <div class="vendorpro-radio-group">
                        <label>
                            <input type="radio" name="vendorpro_shipping_recipient" value="vendor" <?php checked(get_option('vendorpro_shipping_recipient', 'vendor'), 'vendor'); ?>>
                            <?php _e('Vendor', 'vendorpro'); ?>
                        </label>
                        <label style="margin-left: 15px;">
                            <input type="radio" name="vendorpro_shipping_recipient" value="admin" <?php checked(get_option('vendorpro_shipping_recipient'), 'admin'); ?>>
                            <?php _e('Admin', 'vendorpro'); ?>
                        </label>
                    </div>
                    <p class="description"><?php _e('Who receives the shipping fees?', 'vendorpro'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Tax Fee Recipient', 'vendorpro'); ?></th>
                <td>
                    <div class="vendorpro-radio-group">
                        <label>
                            <input type="radio" name="vendorpro_tax_recipient" value="vendor" <?php checked(get_option('vendorpro_tax_recipient', 'vendor'), 'vendor'); ?>>
                            <?php _e('Vendor', 'vendorpro'); ?>
                        </label>
                        <label style="margin-left: 15px;">
                            <input type="radio" name="vendorpro_tax_recipient" value="admin" <?php checked(get_option('vendorpro_tax_recipient'), 'admin'); ?>>
                            <?php _e('Admin', 'vendorpro'); ?>
                        </label>
                    </div>
                    <p class="description"><?php _e('Who receives the tax fees?', 'vendorpro'); ?></p>
                </td>
            </tr>
        </table>

        <div class="vendorpro-section-divider"></div>

        <h3><?php _e('Vendor Capabilities', 'vendorpro'); ?></h3>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('New Product Status', 'vendorpro'); ?></th>
                <td>
                    <select name="vendorpro_product_status" class="regular-text">
                        <option value="publish" <?php selected(get_option('vendorpro_product_status', 'publish'), 'publish'); ?>><?php _e('Published', 'vendorpro'); ?></option>
                        <option value="pending" <?php selected(get_option('vendorpro_product_status'), 'pending'); ?>>
                            <?php _e('Pending Review', 'vendorpro'); ?>
                        </option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Order Status Change', 'vendorpro'); ?></th>
                <td>
                    <label class="vendorpro-toggle">
                        <input type="checkbox" name="vendorpro_can_change_order_status" value="yes" <?php checked(get_option('vendorpro_can_change_order_status', 'yes'), 'yes'); ?>>
                        <span class="slider round"></span>
                    </label>
                    <p class="description"><?php _e('Allow vendors to update order status', 'vendorpro'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Hide Add to Cart', 'vendorpro'); ?></th>
                <td>
                    <label class="vendorpro-toggle">
                        <input type="checkbox" name="vendorpro_catalog_mode" value="yes" <?php checked(get_option('vendorpro_catalog_mode', 'no'), 'yes'); ?>>
                        <span class="slider round"></span>
                    </label>
                    <p class="description"><?php _e('Enable catalog mode (removes Add to Cart button)', 'vendorpro'); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }

    private static function render_withdraw_settings()
    {
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Withdraw Methods', 'vendorpro'); ?></th>
                <td>
                    <label style="margin-right: 20px;">
                        <input type="checkbox" name="vendorpro_withdraw_paypal" value="yes" <?php checked(get_option('vendorpro_withdraw_paypal', 'yes'), 'yes'); ?>>
                        <?php _e('PayPal', 'vendorpro'); ?>
                    </label>
                    <label>
                        <input type="checkbox" name="vendorpro_withdraw_bank" value="yes" <?php checked(get_option('vendorpro_withdraw_bank', 'yes'), 'yes'); ?>>
                        <?php _e('Bank Transfer', 'vendorpro'); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Withdraw Charges', 'vendorpro'); ?></th>
                <td>
                    <div style="margin-bottom: 20px;">
                        <span style="display:inline-block; width: 120px;"><?php _e('Bank Transfer', 'vendorpro'); ?></span>
                        <input type="number" step="0.01" name="vendorpro_withdraw_charge_percent"
                            value="<?php echo esc_attr(get_option('vendorpro_withdraw_charge_percent', 0)); ?>"
                            style="width: 70px;"> %
                        +
                        <input type="number" step="0.01" name="vendorpro_withdraw_charge_fixed"
                            value="<?php echo esc_attr(get_option('vendorpro_withdraw_charge_fixed', 0)); ?>"
                            style="width: 70px;">
                        <?php echo get_woocommerce_currency_symbol(); ?>
                    </div>
                    <p class="description"><?php _e('Select suitable withdraw charges for vendors', 'vendorpro'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Minimum Withdrawal', 'vendorpro'); ?></th>
                <td>
                    <input type="number" step="0.01" name="vendorpro_min_withdraw_amount"
                        value="<?php echo esc_attr(get_option('vendorpro_min_withdraw_amount', 50)); ?>" class="regular-text">
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Order Status for Withdraw', 'vendorpro'); ?></th>
                <td>
                    <?php
                    $statuses = get_option('vendorpro_withdraw_order_status', array('wc-completed'));
                    ?>
                    <label style="display: block; margin-bottom: 5px;">
                        <input type="checkbox" name="vendorpro_withdraw_order_status[]" value="wc-completed" <?php checked(in_array('wc-completed', $statuses)); ?>> <?php _e('Completed', 'vendorpro'); ?>
                    </label>
                    <label style="display: block; margin-bottom: 5px;">
                        <input type="checkbox" name="vendorpro_withdraw_order_status[]" value="wc-processing" <?php checked(in_array('wc-processing', $statuses)); ?>> <?php _e('Processing', 'vendorpro'); ?>
                    </label>
                    <label style="display: block;">
                        <input type="checkbox" name="vendorpro_withdraw_order_status[]" value="wc-on-hold" <?php checked(in_array('wc-on-hold', $statuses)); ?>> <?php _e('On-hold', 'vendorpro'); ?>
                    </label>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Exclude COD', 'vendorpro'); ?></th>
                <td>
                    <label class="vendorpro-toggle">
                        <input type="checkbox" name="vendorpro_exclude_cod_withdraw" value="yes" <?php checked(get_option('vendorpro_exclude_cod_withdraw', 'yes'), 'yes'); ?>>
                        <span class="slider round"></span>
                    </label>
                    <p class="description"><?php _e('Exclude COD payments from withdrawal balance', 'vendorpro'); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }

    private static function render_page_settings()
    {
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Vendor Dashboard', 'vendorpro'); ?></th>
                <td>
                    <?php wp_dropdown_pages(array('name' => 'vendorpro_vendor_dashboard_page_id', 'selected' => get_option('vendorpro_vendor_dashboard_page_id'), 'show_option_none' => __('Select Page', 'vendorpro'))); ?>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Vendor Registration', 'vendorpro'); ?></th>
                <td>
                    <?php wp_dropdown_pages(array('name' => 'vendorpro_vendor_registration_page_id', 'selected' => get_option('vendorpro_vendor_registration_page_id'), 'show_option_none' => __('Select Page', 'vendorpro'))); ?>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Vendors List', 'vendorpro'); ?></th>
                <td>
                    <?php wp_dropdown_pages(array('name' => 'vendorpro_vendors_page_id', 'selected' => get_option('vendorpro_vendors_page_id'), 'show_option_none' => __('Select Page', 'vendorpro'))); ?>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Appearance Settings (Matching Screenshot 3)
     */
    private static function render_appearance_settings()
    {
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Map API Source', 'vendorpro'); ?></th>
                <td>
                    <div class="vendorpro-radio-group">
                        <label>
                            <input type="radio" name="vendorpro_map_api_source" value="google" <?php checked(get_option('vendorpro_map_api_source', 'google'), 'google'); ?>>
                            <?php _e('Google Maps', 'vendorpro'); ?>
                        </label>
                        <label style="margin-left: 15px;">
                            <input type="radio" name="vendorpro_map_api_source" value="mapbox" <?php checked(get_option('vendorpro_map_api_source'), 'mapbox'); ?>>
                            <?php _e('Mapbox', 'vendorpro'); ?>
                        </label>
                    </div>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Google Map API Key', 'vendorpro'); ?></th>
                <td>
                    <input type="text" name="vendorpro_google_maps_api_key"
                        value="<?php echo esc_attr(get_option('vendorpro_google_maps_api_key')); ?>" class="regular-text">
                    <p class="description"><?php _e('Required to display maps on store pages.', 'vendorpro'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Store Header Template', 'vendorpro'); ?></th>
                <td>
                    <div class="vendorpro-template-select">
                        <label>
                            <input type="radio" name="vendorpro_store_header_template" value="default" <?php checked(get_option('vendorpro_store_header_template', 'default'), 'default'); ?>>
                            <?php _e('Default Layout', 'vendorpro'); ?>
                        </label><br>
                        <label>
                            <input type="radio" name="vendorpro_store_header_template" value="modern" <?php checked(get_option('vendorpro_store_header_template'), 'modern'); ?>>
                            <?php _e('Modern Layout', 'vendorpro'); ?>
                        </label>
                    </div>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Show Contact Form', 'vendorpro'); ?></th>
                <td>
                    <label class="vendorpro-toggle">
                        <input type="checkbox" name="vendorpro_show_contact_form" value="yes" <?php checked(get_option('vendorpro_show_contact_form', 'yes'), 'yes'); ?>>
                        <span class="slider round"></span>
                    </label>
                    <p class="description"><?php _e('Display a contact form on the vendor store sidebar', 'vendorpro'); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * AI Assist Settings (Matching Screenshot 2)
     */
    private static function render_ai_settings()
    {
        ?>
        <div class="vendorpro-feature-intro">
            <h3><?php _e('AI Product Info Generator', 'vendorpro'); ?></h3>
            <p><?php _e('Let vendors generate product info by AI to save time and improve quality.', 'vendorpro'); ?></p>
        </div>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Engine', 'vendorpro'); ?></th>
                <td>
                    <select class="regular-text" disabled>
                        <option>OpenAI</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('OpenAI API Key', 'vendorpro'); ?></th>
                <td>
                    <input type="password" name="vendorpro_openai_api_key"
                        value="<?php echo esc_attr(get_option('vendorpro_openai_api_key')); ?>" class="regular-text">
                    <p class="description"><?php _e('You can get your API Keys in your OpenAI Account.', 'vendorpro'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Model', 'vendorpro'); ?></th>
                <td>
                    <select name="vendorpro_ai_model" class="regular-text">
                        <option value="gpt-3.5-turbo" <?php selected(get_option('vendorpro_ai_model', 'gpt-3.5-turbo'), 'gpt-3.5-turbo'); ?>>OpenAI GPT-3.5 Turbo</option>
                        <option value="gpt-4" <?php selected(get_option('vendorpro_ai_model'), 'gpt-4'); ?>>OpenAI GPT-4
                        </option>
                    </select>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Reverse Withdrawal (Placeholder)
     */
    private static function render_reverse_withdraw_settings()
    {
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Enable Reverse Withdrawal', 'vendorpro'); ?></th>
                <td>
                    <label class="vendorpro-toggle">
                        <input type="checkbox" name="vendorpro_enable_reverse_withdrawal" value="yes" <?php checked(get_option('vendorpro_enable_reverse_withdrawal'), 'yes'); ?>>
                        <span class="slider round"></span>
                    </label>
                    <p class="description"><?php _e('Check this to enable reverse withdrawal for COD orders.', 'vendorpro'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Balance Threshold', 'vendorpro'); ?></th>
                <td>
                    <input type="number" step="0.01" name="vendorpro_reverse_threshold"
                        value="<?php echo esc_attr(get_option('vendorpro_reverse_threshold', 150)); ?>" class="regular-text">
                    <p class="description"><?php _e('Set reverse withdrawal threshold limit.', 'vendorpro'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Grace Period (Days)', 'vendorpro'); ?></th>
                <td>
                    <input type="number" name="vendorpro_reverse_grace_period"
                        value="<?php echo esc_attr(get_option('vendorpro_reverse_grace_period', 7)); ?>" class="regular-text">
                    <p class="description">
                        <?php _e('Maximum payment due period in days before actions are taken.', 'vendorpro'); ?>
                    </p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Actions After Grace Period', 'vendorpro'); ?></th>
                <td>
                    <label style="display: block; margin-bottom: 5px;">
                        <input type="checkbox" name="vendorpro_reverse_disable_atc" value="yes" <?php checked(get_option('vendorpro_reverse_disable_atc'), 'yes'); ?>>
                        <?php _e('Disable Add to Cart Button', 'vendorpro'); ?>
                    </label>
                    <label style="display: block; margin-bottom: 5px;">
                        <input type="checkbox" name="vendorpro_reverse_hide_withdraw" value="yes" <?php checked(get_option('vendorpro_reverse_hide_withdraw'), 'yes'); ?>>
                        <?php _e('Hide Withdraw Menu', 'vendorpro'); ?>
                    </label>
                    <label style="display: block;">
                        <input type="checkbox" name="vendorpro_reverse_inactive_vendor" value="yes" <?php checked(get_option('vendorpro_reverse_inactive_vendor'), 'yes'); ?>>
                        <?php _e('Make Vendor Inactive', 'vendorpro'); ?>
                    </label>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Privacy Settings (Placeholder)
     */
    private static function render_privacy_settings()
    {
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><?php _e('Enable Privacy Policy', 'vendorpro'); ?></th>
                <td>
                    <label class="vendorpro-toggle">
                        <input type="checkbox" name="vendorpro_enable_privacy_policy" value="yes" <?php checked(get_option('vendorpro_enable_privacy_policy', 'yes'), 'yes'); ?>>
                        <span class="slider round"></span>
                    </label>
                </td>
            </tr>
        </table>
        <?php
    }

}
