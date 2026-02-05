<?php
/**
 * Admin commissions management
 */

if (!defined('ABSPATH')) {
    exit;
}

class VendorPro_Admin_Commissions
{

    /**
     * Render page
     */
    public static function render_page()
    {
        // Handle actions
        if (isset($_GET['commission_action'])) {
            check_admin_referer('vendorpro-commission-action');
            self::handle_action();
        }

        $commission = VendorPro_Commission::instance();

        $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $per_page = 20;
        $offset = ($paged - 1) * $per_page;

        $args = array(
            'limit' => $per_page,
            'offset' => $offset,
            'orderby' => isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'created_at',
            'order' => isset($_GET['order']) ? sanitize_text_field($_GET['order']) : 'DESC'
        );

        if (isset($_GET['vendor_id']) && !empty($_GET['vendor_id'])) {
            $args['vendor_id'] = intval($_GET['vendor_id']);
        }

        if (isset($_GET['status']) && !empty($_GET['status'])) {
            $args['status'] = sanitize_text_field($_GET['status']);
        }

        $commissions = $commission->get_commissions($args);

        // Count total
        global $wpdb;
        $where = array('1=1');

        if (!empty($args['vendor_id'])) {
            $where[] = $wpdb->prepare("vendor_id = %d", $args['vendor_id']);
        }

        if (!empty($args['status'])) {
            $where[] = $wpdb->prepare("status = %s", $args['status']);
        }

        $where_clause = implode(' AND ', $where);
        $total_items = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}vendorpro_commissions WHERE {$where_clause}");

        include VENDORPRO_TEMPLATES_DIR . 'admin/commissions-list.php';
    }

    /**
     * Handle action
     */
    private static function handle_action()
    {
        $action = sanitize_text_field($_GET['commission_action']);
        $commission_id = intval($_GET['commission_id']);

        $commission = VendorPro_Commission::instance();

        switch ($action) {
            case 'mark_paid':
                $commission->mark_as_paid($commission_id);
                wp_redirect(add_query_arg(array('message' => 'marked_paid'), remove_query_arg(array('commission_action', 'commission_id', '_wpnonce'))));
                exit;
        }
    }
}
