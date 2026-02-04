<?php
/**
 * Admin vendors management
 */

if (!defined('ABSPATH')) {
    exit;
}

class VendorPro_Admin_Vendors
{

    /**
     * Render page
     */
    public static function render_page()
    {
        $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : 'list';

        switch ($action) {
            case 'edit':
                self::edit_vendor();
                break;
            case 'view':
                self::view_vendor();
                break;
            default:
                self::list_vendors();
                break;
        }
    }

    /**
     * List vendors
     */
    private static function list_vendors()
    {
        // Handle bulk actions
        if (isset($_POST['action']) && $_POST['action'] !== '-1') {
            check_admin_referer('bulk-vendors');
            self::handle_bulk_action();
        }

        // Handle individual actions
        if (isset($_GET['vendor_action'])) {
            check_admin_referer('vendorpro-vendor-action');
            self::handle_vendor_action();
        }

        $db = VendorPro_Database::instance();

        $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $per_page = 20;
        $offset = ($paged - 1) * $per_page;

        $args = array(
            'limit' => $per_page,
            'offset' => $offset,
            'orderby' => isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'created_at',
            'order' => isset($_GET['order']) ? sanitize_text_field($_GET['order']) : 'DESC'
        );

        if (isset($_GET['status']) && !empty($_GET['status'])) {
            $args['status'] = sanitize_text_field($_GET['status']);
        }

        if (isset($_GET['s']) && !empty($_GET['s'])) {
            $args['search'] = sanitize_text_field($_GET['s']);
        }

        $vendors = $db->get_vendors($args);
        $total_items = $db->count_vendors($args);

        include VENDORPRO_TEMPLATES_DIR . 'admin/vendors-list.php';
    }

    /**
     * Edit vendor
     */
    private static function edit_vendor()
    {
        if (!isset($_GET['vendor_id'])) {
            wp_die(__('Invalid vendor ID.', 'vendorpro'));
        }

        $vendor_id = intval($_GET['vendor_id']);
        $vendor = VendorPro_Database::instance()->get_vendor($vendor_id);

        if (!$vendor) {
            wp_die(__('Vendor not found.', 'vendorpro'));
        }

        // Handle form submission
        if (isset($_POST['submit']) && check_admin_referer('vendorpro-edit-vendor')) {
            $result = VendorPro_Vendor::instance()->update_vendor($vendor_id, $_POST);

            if ($result) {
                echo '<div class="notice notice-success"><p>' . __('Vendor updated successfully.', 'vendorpro') . '</p></div>';
                $vendor = VendorPro_Database::instance()->get_vendor($vendor_id);
            }
        }

        include VENDORPRO_TEMPLATES_DIR . 'admin/vendor-edit.php';
    }

    /**
     * View vendor
     */
    private static function view_vendor()
    {
        if (!isset($_GET['vendor_id'])) {
            wp_die(__('Invalid vendor ID.', 'vendorpro'));
        }

        $vendor_id = intval($_GET['vendor_id']);
        $vendor = VendorPro_Database::instance()->get_vendor($vendor_id);

        if (!$vendor) {
            wp_die(__('Vendor not found.', 'vendorpro'));
        }

        $stats = VendorPro_Vendor::instance()->get_vendor_stats($vendor_id);

        include VENDORPRO_TEMPLATES_DIR . 'admin/vendor-view.php';
    }

    /**
     * Handle vendor action
     */
    private static function handle_vendor_action()
    {
        $action = sanitize_text_field($_GET['vendor_action']);
        $vendor_id = intval($_GET['vendor_id']);

        $db = VendorPro_Database::instance();

        switch ($action) {
            case 'approve':
                $db->update_vendor($vendor_id, array('status' => 'approved'));
                wp_redirect(add_query_arg(array('message' => 'approved'), remove_query_arg(array('vendor_action', 'vendor_id', '_wpnonce'))));
                exit;

            case 'reject':
                $db->update_vendor($vendor_id, array('status' => 'rejected'));
                wp_redirect(add_query_arg(array('message' => 'rejected'), remove_query_arg(array('vendor_action', 'vendor_id', '_wpnonce'))));
                exit;

            case 'enable':
                $db->update_vendor($vendor_id, array('enabled' => 1));
                wp_redirect(add_query_arg(array('message' => 'enabled'), remove_query_arg(array('vendor_action', 'vendor_id', '_wpnonce'))));
                exit;

            case 'disable':
                $db->update_vendor($vendor_id, array('enabled' => 0));
                wp_redirect(add_query_arg(array('message' => 'disabled'), remove_query_arg(array('vendor_action', 'vendor_id', '_wpnonce'))));
                exit;

            case 'delete':
                $db->delete_vendor($vendor_id);
                wp_redirect(add_query_arg(array('message' => 'deleted'), remove_query_arg(array('vendor_action', 'vendor_id', '_wpnonce'))));
                exit;
        }
    }

    /**
     * Handle bulk action
     */
    private static function handle_bulk_action()
    {
        if (!isset($_POST['vendor_ids']) || !is_array($_POST['vendor_ids'])) {
            return;
        }

        $action = sanitize_text_field($_POST['action']);
        $vendor_ids = array_map('intval', $_POST['vendor_ids']);

        $db = VendorPro_Database::instance();

        foreach ($vendor_ids as $vendor_id) {
            switch ($action) {
                case 'approve':
                    $db->update_vendor($vendor_id, array('status' => 'approved'));
                    break;

                case 'reject':
                    $db->update_vendor($vendor_id, array('status' => 'rejected'));
                    break;

                case 'enable':
                    $db->update_vendor($vendor_id, array('enabled' => 1));
                    break;

                case 'disable':
                    $db->update_vendor($vendor_id, array('enabled' => 0));
                    break;

                case 'delete':
                    $db->delete_vendor($vendor_id);
                    break;
            }
        }

        wp_redirect(add_query_arg(array('message' => 'bulk_updated'), remove_query_arg('action')));
        exit;
    }
}
