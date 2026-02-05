<?php
/**
 * Helper functions
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get current vendor
 */
function vendorpro_get_current_vendor()
{
    $user_id = get_current_user_id();

    if (!$user_id) {
        return null;
    }

    return VendorPro_Database::instance()->get_vendor_by_user($user_id);
}

/**
 * Check if current user is vendor
 */
function vendorpro_is_vendor()
{
    return VendorPro_Vendor::instance()->is_vendor();
}

/**
 * Get vendor by ID
 */
function vendorpro_get_vendor($vendor_id)
{
    return VendorPro_Database::instance()->get_vendor($vendor_id);
}

/**
 * Get vendor by user ID
 */
function vendorpro_get_vendor_by_user($user_id)
{
    return VendorPro_Database::instance()->get_vendor_by_user($user_id);
}

/**
 * Get vendor by slug
 */
function vendorpro_get_vendor_by_slug($slug)
{
    return VendorPro_Database::instance()->get_vendor_by_slug($slug);
}

/**
 * Get vendor products
 */
function vendorpro_get_vendor_products($vendor_id, $args = array())
{
    return VendorPro_Vendor::instance()->get_vendor_products($vendor_id, $args);
}

/**
 * Get vendor orders
 */
function vendorpro_get_vendor_orders($vendor_id, $args = array())
{
    return VendorPro_Vendor::instance()->get_vendor_orders($vendor_id, $args);
}

/**
 * Get vendor stats
 */
function vendorpro_get_vendor_stats($vendor_id)
{
    return VendorPro_Vendor::instance()->get_vendor_stats($vendor_id);
}

/**
 * Get vendor balance
 */
function vendorpro_get_vendor_balance($vendor_id)
{
    return VendorPro_Database::instance()->get_vendor_balance($vendor_id);
}

/**
 * Get vendor earnings
 */
function vendorpro_get_vendor_earnings($vendor_id, $status = 'paid')
{
    return VendorPro_Database::instance()->get_vendor_earnings($vendor_id, $status);
}

/**
 * Get vendor commissions
 */
function vendorpro_get_vendor_commissions($vendor_id, $args = array())
{
    return VendorPro_Database::instance()->get_vendor_commissions($vendor_id, $args);
}

/**
 * Get vendor withdrawals
 */
function vendorpro_get_vendor_withdrawals($vendor_id, $args = array())
{
    return VendorPro_Database::instance()->get_vendor_withdrawals($vendor_id, $args);
}

/**
 * Format price
 */
function vendorpro_price($price)
{
    return wc_price($price);
}

/**
 * Get vendor dashboard URL
 */
function vendorpro_get_dashboard_url($page = '', $args = array())
{
    $page_id = get_option('vendorpro_vendor_dashboard_page_id');
    $url = $page_id ? get_permalink($page_id) : home_url('/vendor-dashboard');

    if ($page) {
        $url = trailingslashit($url) . $page;
    }

    if (!empty($args) && is_array($args)) {
        $url = add_query_arg($args, $url);
    }

    return $url;
}

/**
 * Get vendor store URL
 */
function vendorpro_get_store_url($vendor_slug)
{
    return home_url('/store/' . $vendor_slug);
}

/**
 * Get vendor registration URL
 */
function vendorpro_get_registration_url()
{
    $page_id = get_option('vendorpro_vendor_registration_page_id');
    return $page_id ? get_permalink($page_id) : home_url('/become-a-vendor');
}

/**
 * Get all vendors URL
 */
function vendorpro_get_vendors_url()
{
    $page_id = get_option('vendorpro_vendors_page_id');
    return $page_id ? get_permalink($page_id) : home_url('/vendors');
}

/**
 * Get template
 */
function vendorpro_get_template($template_name, $args = array())
{
    vendorpro()->get_template($template_name, $args);
}

/**
 * Check if vendor registration is enabled
 */
function vendorpro_is_registration_enabled()
{
    return get_option('vendorpro_vendor_registration', 'yes') === 'yes';
}

/**
 * Check if vendor approval is required
 */
function vendorpro_is_approval_required()
{
    return get_option('vendorpro_vendor_approval', 'yes') === 'yes';
}

/**
 * Get commission rate
 */
function vendorpro_get_commission_rate()
{
    return floatval(get_option('vendorpro_commission_rate', 10));
}

/**
 * Get commission type
 */
function vendorpro_get_commission_type()
{
    return get_option('vendorpro_commission_type', 'percentage');
}

/**
 * Get minimum withdrawal amount
 */
function vendorpro_get_min_withdrawal_amount()
{
    return floatval(get_option('vendorpro_min_withdraw_amount', 50));
}

/**
 * Get withdrawal methods
 */
function vendorpro_get_withdrawal_methods()
{
    return VendorPro_Withdrawal::instance()->get_withdrawal_methods();
}

/**
 * Format status
 */
function vendorpro_format_status($status)
{
    $statuses = array(
        'pending' => '<span class="status-badge status-pending">' . __('Pending', 'vendorpro') . '</span>',
        'approved' => '<span class="status-badge status-approved">' . __('Approved', 'vendorpro') . '</span>',
        'rejected' => '<span class="status-badge status-rejected">' . __('Rejected', 'vendorpro') . '</span>',
        'cancelled' => '<span class="status-badge status-cancelled">' . __('Cancelled', 'vendorpro') . '</span>',
        'completed' => '<span class="status-badge status-completed">' . __('Completed', 'vendorpro') . '</span>',
        'paid' => '<span class="status-badge status-paid">' . __('Paid', 'vendorpro') . '</span>',
        'unpaid' => '<span class="status-badge status-unpaid">' . __('Unpaid', 'vendorpro') . '</span>',
        'refunded' => '<span class="status-badge status-refunded">' . __('Refunded', 'vendorpro') . '</span>',
    );

    return isset($statuses[$status]) ? $statuses[$status] : ucfirst($status);
}

/**
 * Get date format
 */
function vendorpro_date_format($date)
{
    return date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($date));
}

/**
 * Shortcode: Vendor Dashboard
 */
add_shortcode('vendorpro_dashboard', 'vendorpro_dashboard_shortcode');
function vendorpro_dashboard_shortcode()
{
    if (!is_user_logged_in()) {
        return '<p>' . __('Please login to access the vendor dashboard.', 'vendorpro') . '</p>';
    }

    if (!vendorpro_is_vendor()) {
        return '<p>' . __('You must be a vendor to access this page.', 'vendorpro') . ' <a href="' . vendorpro_get_registration_url() . '">' . __('Become a vendor', 'vendorpro') . '</a></p>';
    }

    ob_start();
    vendorpro_get_template('vendor/dashboard.php');
    return ob_get_clean();
}

/**
 * Shortcode: Vendor Registration
 */
add_shortcode('vendorpro_vendor_registration', 'vendorpro_registration_shortcode');
function vendorpro_registration_shortcode()
{
    if (!vendorpro_is_registration_enabled()) {
        return '<p>' . __('Vendor registration is currently disabled.', 'vendorpro') . '</p>';
    }

    if (is_user_logged_in() && vendorpro_is_vendor()) {
        return '<p>' . __('You are already a vendor.', 'vendorpro') . ' <a href="' . vendorpro_get_dashboard_url() . '">' . __('Go to dashboard', 'vendorpro') . '</a></p>';
    }

    ob_start();
    vendorpro_get_template('frontend/vendor-registration.php');
    return ob_get_clean();
}

/**
 * Shortcode: All Vendors
 */
add_shortcode('vendorpro_vendors', 'vendorpro_vendors_shortcode');
function vendorpro_vendors_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'per_page' => 12,
        'featured' => '',
        'orderby' => 'created_at',
        'order' => 'DESC'
    ), $atts);

    ob_start();
    vendorpro_get_template('frontend/vendors-list.php', $atts);
    return ob_get_clean();
}

/**
 * Shortcode: Vendor Store
 */
add_shortcode('vendorpro_vendor_store', 'vendorpro_store_shortcode');
function vendorpro_store_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'vendor_id' => 0,
        'vendor_slug' => ''
    ), $atts);

    ob_start();
    vendorpro_get_template('frontend/vendor-store.php', $atts);
    return ob_get_clean();
}

/**
 * Get vendor report stats
 */
function vendorpro_get_vendor_report_stats($vendor_id, $date_from, $date_to)
{
    return VendorPro_Vendor::instance()->get_vendor_report_stats($vendor_id, $date_from, $date_to);
}

/**
 * Get vendor order status counts
 */
function vendorpro_get_vendor_order_status_counts($vendor_id)
{
    return VendorPro_Vendor::instance()->get_vendor_order_status_counts($vendor_id);
}

/**
 * Get vendor customers
 */
function vendorpro_get_vendor_customers($vendor_id)
{
    return VendorPro_Vendor::instance()->get_vendor_customers($vendor_id);
}

/**
 * Get vendor pending withdrawals
 */
function vendorpro_get_vendor_pending_withdrawals($vendor_id)
{
    return VendorPro_Database::instance()->get_vendor_withdrawals($vendor_id, array('status' => 'pending'));
}

/**
 * Get vendor payment methods
 */
function vendorpro_get_vendor_payment_methods($vendor_id)
{
    $vendor = vendorpro_get_vendor($vendor_id);
    if (!$vendor)
        return array();

    $methods = array();

    // Check Bank
    $bank = get_user_meta($vendor->user_id, 'vendorpro_payment_method_bank', true);
    if (!empty($bank)) {
        $methods['bank'] = array(
            'account' => $bank['bank_name'] . ' - ' . substr($bank['account_number'], -4)
        );
    }

    // Check PayPal
    $paypal = get_user_meta($vendor->user_id, 'vendorpro_payment_method_paypal', true);
    if (!empty($paypal)) {
        $methods['paypal'] = array(
            'account' => $paypal['email']
        );
    }

    return $methods;
}

/**
 * Get vendor last payment
 */
function vendorpro_get_vendor_last_payment($vendor_id)
{
    $withdrawals = VendorPro_Database::instance()->get_vendor_withdrawals($vendor_id, array(
        'status' => 'approved',
        'limit' => 1,
        'orderby' => 'date_created',
        'order' => 'DESC'
    ));

    return !empty($withdrawals) ? $withdrawals[0] : null;
}

/**
 * Get order vendor commission
 */
function vendorpro_get_order_vendor_commission($order_id, $vendor_id)
{
    return VendorPro_Database::instance()->get_order_commission_amount($order_id, $vendor_id);
}

/**
 * Handle file upload
 */
function vendorpro_handle_file_upload($file, $type = 'image')
{
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');

    $attachment_id = media_handle_sideload($file, 0);

    if (is_wp_error($attachment_id)) {
        return $attachment_id;
    }

    return array(
        'id' => $attachment_id,
        'url' => wp_get_attachment_url($attachment_id)
    );
}
