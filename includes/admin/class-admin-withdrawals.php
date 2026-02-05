<?php
/**
 * Admin withdrawals management
 */

if (!defined('ABSPATH')) {
    exit;
}

class VendorPro_Admin_Withdrawals
{

    /**
     * Render page
     */
    public static function render_page()
    {
        // Handle actions
        if (isset($_GET['withdrawal_action'])) {
            check_admin_referer('vendorpro-withdrawal-action');
            self::handle_action();
        }

        $withdrawal = VendorPro_Withdrawal::instance();

        $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $per_page = 20;
        $offset = ($paged - 1) * $per_page;

        $args = array(
            'limit' => $per_page,
            'offset' => $offset,
            'orderby' => isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'requested_at',
            'order' => isset($_GET['order']) ? sanitize_text_field($_GET['order']) : 'DESC'
        );

        if (isset($_GET['vendor_id']) && !empty($_GET['vendor_id'])) {
            $args['vendor_id'] = intval($_GET['vendor_id']);
        }

        if (isset($_GET['status']) && !empty($_GET['status'])) {
            $args['status'] = sanitize_text_field($_GET['status']);
        }

        $withdrawals = $withdrawal->get_withdrawals($args);

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
        $total_items = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}vendorpro_withdrawals WHERE {$where_clause}");

        include VENDORPRO_TEMPLATES_DIR . 'admin/withdrawals-list.php';
    }

    /**
     * Handle action
     */
    private static function handle_action()
    {
        $action = sanitize_text_field($_GET['withdrawal_action']);
        $withdrawal_id = intval($_GET['withdrawal_id']);

        $withdrawal = VendorPro_Withdrawal::instance();

        switch ($action) {
            case 'approve':
                $result = $withdrawal->approve_withdrawal($withdrawal_id);
                $message = is_wp_error($result) ? 'error' : 'approved';
                wp_redirect(add_query_arg(array('message' => $message), remove_query_arg(array('withdrawal_action', 'withdrawal_id', '_wpnonce'))));
                exit;

            case 'reject':
                $result = $withdrawal->reject_withdrawal($withdrawal_id);
                $message = is_wp_error($result) ? 'error' : 'rejected';
                wp_redirect(add_query_arg(array('message' => $message), remove_query_arg(array('withdrawal_action', 'withdrawal_id', '_wpnonce'))));
                exit;
        }
    }
}
