<?php
/**
 * Admin: Vendors List Template
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap vendorpro-admin-wrap">
    <h1 class="wp-heading-inline">
        <?php _e('Vendors', 'vendorpro'); ?>
    </h1>

    <?php if (isset($_GET['s']) && !empty($_GET['s'])): ?>
        <span class="subtitle">
            <?php printf(__('Search results for: %s', 'vendorpro'), esc_html($_GET['s'])); ?>
        </span>
    <?php endif; ?>

    <hr class="wp-header-end">

    <?php if (isset($_GET['message'])): ?>
        <div class="notice notice-success is-dismissible">
            <p>
                <?php
                switch ($_GET['message']) {
                    case 'approved':
                        _e('Vendor approved successfully.', 'vendorpro');
                        break;
                    case 'rejected':
                        _e('Vendor rejected successfully.', 'vendorpro');
                        break;
                    case 'enabled':
                        _e('Vendor enabled successfully.', 'vendorpro');
                        break;
                    case 'disabled':
                        _e('Vendor disabled successfully.', 'vendorpro');
                        break;
                    case 'deleted':
                        _e('Vendor deleted successfully.', 'vendorpro');
                        break;
                    case 'bulk_updated':
                        _e('Bulk action completed successfully.', 'vendorpro');
                        break;
                }
                ?>
            </p>
        </div>
    <?php endif; ?>

    <ul class="subsubsub">
        <li class="all"><a href="<?php echo admin_url('admin.php?page=vendorpro-vendors'); ?>"
                class="<?php echo !isset($_GET['status']) ? 'current' : ''; ?>">
                <?php _e('All', 'vendorpro'); ?> <span class="count">(
                    <?php echo $db->count_vendors(); ?>)
                </span>
            </a> |</li>
        <li class="approved"><a href="<?php echo admin_url('admin.php?page=vendorpro-vendors&status=approved'); ?>"
                class="<?php echo isset($_GET['status']) && $_GET['status'] == 'approved' ? 'current' : ''; ?>">
                <?php _e('Approved', 'vendorpro'); ?> <span class="count">(
                    <?php echo $db->count_vendors(array('status' => 'approved')); ?>)
                </span>
            </a> |</li>
        <li class="pending"><a href="<?php echo admin_url('admin.php?page=vendorpro-vendors&status=pending'); ?>"
                class="<?php echo isset($_GET['status']) && $_GET['status'] == 'pending' ? 'current' : ''; ?>">
                <?php _e('Pending', 'vendorpro'); ?> <span class="count">(
                    <?php echo $db->count_vendors(array('status' => 'pending')); ?>)
                </span>
            </a> |</li>
        <li class="rejected"><a href="<?php echo admin_url('admin.php?page=vendorpro-vendors&status=rejected'); ?>"
                class="<?php echo isset($_GET['status']) && $_GET['status'] == 'rejected' ? 'current' : ''; ?>">
                <?php _e('Rejected', 'vendorpro'); ?> <span class="count">(
                    <?php echo $db->count_vendors(array('status' => 'rejected')); ?>)
                </span>
            </a></li>
    </ul>

    <form method="get">
        <input type="hidden" name="page" value="vendorpro-vendors" />
        <p class="search-box">
            <label class="screen-reader-text" for="vendor-search-input">
                <?php _e('Search Vendors:', 'vendorpro'); ?>
            </label>
            <input type="search" id="vendor-search-input" name="s"
                value="<?php echo isset($_GET['s']) ? esc_attr($_GET['s']) : ''; ?>">
            <input type="submit" id="search-submit" class="button" value="<?php _e('Search Vendors', 'vendorpro'); ?>">
        </p>
    </form>

    <form method="post">
        <?php wp_nonce_field('bulk-vendors'); ?>

        <div class="tablenav top">
            <div class="alignleft actions bulkactions">
                <label for="bulk-action-selector-top" class="screen-reader-text">
                    <?php _e('Select bulk action', 'vendorpro'); ?>
                </label>
                <select name="action" id="bulk-action-selector-top">
                    <option value="-1">
                        <?php _e('Bulk Actions', 'vendorpro'); ?>
                    </option>
                    <option value="approve">
                        <?php _e('Approve', 'vendorpro'); ?>
                    </option>
                    <option value="reject">
                        <?php _e('Reject', 'vendorpro'); ?>
                    </option>
                    <option value="enable">
                        <?php _e('Enable', 'vendorpro'); ?>
                    </option>
                    <option value="disable">
                        <?php _e('Disable', 'vendorpro'); ?>
                    </option>
                    <option value="delete">
                        <?php _e('Delete', 'vendorpro'); ?>
                    </option>
                </select>
                <input type="submit" id="doaction" class="button action" value="<?php _e('Apply', 'vendorpro'); ?>">
            </div>

            <div class="tablenav-pages">
                <span class="displaying-num">
                    <?php printf(_n('%s item', '%s items', $total_items, 'vendorpro'), number_format_i18n($total_items)); ?>
                </span>
                <?php
                if ($total_items > $per_page) {
                    $total_pages = ceil($total_items / $per_page);
                    $current_url = remove_query_arg('paged');
                    echo paginate_links(array(
                        'base' => add_query_arg('paged', '%#%', $current_url),
                        'format' => '',
                        'prev_text' => __('&laquo;'),
                        'next_text' => __('&raquo;'),
                        'total' => $total_pages,
                        'current' => $paged
                    ));
                }
                ?>
            </div>
        </div>

        <table class="wp-list-table widefat fixed striped table-view-list vendors">
            <thead>
                <tr>
                    <td id="cb" class="manage-column column-cb check-column">
                        <label class="screen-reader-text" for="cb-select-all-1">
                            <?php _e('Select All', 'vendorpro'); ?>
                        </label>
                        <input id="cb-select-all-1" type="checkbox">
                    </td>
                    <th scope="col" class="manage-column column-primary">
                        <?php _e('Store Name', 'vendorpro'); ?>
                    </th>
                    <th scope="col" class="manage-column">
                        <?php _e('Vendor', 'vendorpro'); ?>
                    </th>
                    <th scope="col" class="manage-column">
                        <?php _e('Status', 'vendorpro'); ?>
                    </th>
                    <th scope="col" class="manage-column">
                        <?php _e('Products', 'vendorpro'); ?>
                    </th>
                    <th scope="col" class="manage-column">
                        <?php _e('Balance', 'vendorpro'); ?>
                    </th>
                    <th scope="col" class="manage-column">
                        <?php _e('Date', 'vendorpro'); ?>
                    </th>
                </tr>
            </thead>

            <tbody id="the-list">
                <?php if (!empty($vendors)): ?>
                    <?php foreach ($vendors as $vendor):
                        $user = get_userdata($vendor->user_id);
                        $edit_url = admin_url('admin.php?page=vendorpro-vendors&action=edit&vendor_id=' . $vendor->id);
                        $approve_url = wp_nonce_url(admin_url('admin.php?page=vendorpro-vendors&vendor_action=approve&vendor_id=' . $vendor->id), 'vendorpro-vendor-action');
                        $reject_url = wp_nonce_url(admin_url('admin.php?page=vendorpro-vendors&vendor_action=reject&vendor_id=' . $vendor->id), 'vendorpro-vendor-action');
                        $delete_url = wp_nonce_url(admin_url('admin.php?page=vendorpro-vendors&vendor_action=delete&vendor_id=' . $vendor->id), 'vendorpro-vendor-action');
                        $balance = vendorpro_get_vendor_balance($vendor->id);
                        $product_count = count(vendorpro_get_vendor_products($vendor->id, array('limit' => -1)));
                        ?>
                        <tr>
                            <th scope="row" class="check-column">
                                <label class="screen-reader-text" for="cb-select-<?php echo $vendor->id; ?>">
                                    <?php _e('Select Vendor', 'vendorpro'); ?>
                                </label>
                                <input id="cb-select-<?php echo $vendor->id; ?>" type="checkbox" name="vendor_ids[]"
                                    value="<?php echo $vendor->id; ?>">
                            </th>
                            <td class="column-primary has-row-actions">
                                <strong><a href="<?php echo $edit_url; ?>" class="row-title">
                                        <?php echo esc_html($vendor->store_name); ?>
                                    </a></strong>
                                <div class="row-actions">
                                    <span class="edit"><a href="<?php echo $edit_url; ?>">
                                            <?php _e('Edit', 'vendorpro'); ?>
                                        </a> | </span>
                                    <?php if ($vendor->status === 'pending'): ?>
                                        <span class="approve"><a href="<?php echo $approve_url; ?>" class="approve-vendor">
                                                <?php _e('Approve', 'vendorpro'); ?>
                                            </a> | </span>
                                        <span class="reject"><a href="<?php echo $reject_url; ?>" class="reject-vendor"
                                                style="color: #a00;">
                                                <?php _e('Reject', 'vendorpro'); ?>
                                            </a> | </span>
                                    <?php endif; ?>
                                    <span class="delete"><a href="<?php echo $delete_url; ?>" class="delete-vendor"
                                            onclick="return confirm('<?php _e('Are you sure?', 'vendorpro'); ?>');">
                                            <?php _e('Delete', 'vendorpro'); ?>
                                        </a></span>
                                </div>
                            </td>
                            <td>
                                <?php if ($user): ?>
                                    <div style="display: flex; align-items: center;">
                                        <?php echo get_avatar($user->ID, 32); ?>
                                        <div style="margin-left: 10px;">
                                            <?php echo esc_html($user->display_name); ?><br>
                                            <a href="mailto:<?php echo esc_attr($user->user_email); ?>">
                                                <?php echo esc_html($user->user_email); ?>
                                            </a>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <span class="description">
                                        <?php _e('User deleted', 'vendorpro'); ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo vendorpro_format_status($vendor->status); ?>
                            </td>
                            <td>
                                <?php echo $product_count; ?>
                            </td>
                            <td>
                                <?php echo wc_price($balance); ?>
                            </td>
                            <td>
                                <?php echo vendorpro_date_format($vendor->created_at); ?><br>
                                <small>
                                    <?php echo human_time_diff(strtotime($vendor->created_at), current_time('timestamp')); ?>
                                    <?php _e('ago', 'vendorpro'); ?>
                                </small>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">
                            <?php _e('No vendors found.', 'vendorpro'); ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>

            <tfoot>
                <tr>
                    <td class="manage-column column-cb check-column"><input type="checkbox"></td>
                    <th scope="col" class="manage-column">
                        <?php _e('Store Name', 'vendorpro'); ?>
                    </th>
                    <th scope="col" class="manage-column">
                        <?php _e('Vendor', 'vendorpro'); ?>
                    </th>
                    <th scope="col" class="manage-column">
                        <?php _e('Status', 'vendorpro'); ?>
                    </th>
                    <th scope="col" class="manage-column">
                        <?php _e('Products', 'vendorpro'); ?>
                    </th>
                    <th scope="col" class="manage-column">
                        <?php _e('Balance', 'vendorpro'); ?>
                    </th>
                    <th scope="col" class="manage-column">
                        <?php _e('Date', 'vendorpro'); ?>
                    </th>
                </tr>
            </tfoot>
        </table>
    </form>
</div>