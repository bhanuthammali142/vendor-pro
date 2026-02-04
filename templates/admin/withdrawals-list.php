<?php
/**
 * Admin: Withdrawals List Template
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap vendorpro-admin-wrap">
    <h1 class="wp-heading-inline">
        <?php _e('Withdrawals', 'vendorpro'); ?>
    </h1>
    <hr class="wp-header-end">

    <?php if (isset($_GET['message'])): ?>
        <div class="notice notice-success is-dismissible">
            <p>
                <?php
                switch ($_GET['message']) {
                    case 'approved':
                        _e('Withdrawal request approved.', 'vendorpro');
                        break;
                    case 'rejected':
                        _e('Withdrawal request rejected.', 'vendorpro');
                        break;
                    case 'error':
                        _e('An error occurred.', 'vendorpro');
                        break;
                }
                ?>
            </p>
        </div>
    <?php endif; ?>

    <ul class="subsubsub">
        <li class="all"><a href="<?php echo admin_url('admin.php?page=vendorpro-withdrawals'); ?>"
                class="<?php echo !isset($_GET['status']) ? 'current' : ''; ?>">
                <?php _e('All', 'vendorpro'); ?>
            </a> |</li>
        <li class="pending"><a href="<?php echo admin_url('admin.php?page=vendorpro-withdrawals&status=pending'); ?>"
                class="<?php echo isset($_GET['status']) && $_GET['status'] == 'pending' ? 'current' : ''; ?>">
                <?php _e('Pending', 'vendorpro'); ?>
            </a> |</li>
        <li class="approved"><a href="<?php echo admin_url('admin.php?page=vendorpro-withdrawals&status=approved'); ?>"
                class="<?php echo isset($_GET['status']) && $_GET['status'] == 'approved' ? 'current' : ''; ?>">
                <?php _e('Approved', 'vendorpro'); ?>
            </a> |</li>
        <li class="rejected"><a href="<?php echo admin_url('admin.php?page=vendorpro-withdrawals&status=rejected'); ?>"
                class="<?php echo isset($_GET['status']) && $_GET['status'] == 'rejected' ? 'current' : ''; ?>">
                <?php _e('Rejected', 'vendorpro'); ?>
            </a></li>
    </ul>

    <form method="get">
        <input type="hidden" name="page" value="vendorpro-withdrawals" />

        <div class="tablenav top">
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

        <table class="wp-list-table widefat fixed striped table-view-list withdrawals">
            <thead>
                <tr>
                    <th scope="col" class="manage-column column-primary">
                        <?php _e('Amount', 'vendorpro'); ?>
                    </th>
                    <th scope="col" class="manage-column">
                        <?php _e('Vendor', 'vendorpro'); ?>
                    </th>
                    <th scope="col" class="manage-column">
                        <?php _e('Method', 'vendorpro'); ?>
                    </th>
                    <th scope="col" class="manage-column">
                        <?php _e('Payment Details', 'vendorpro'); ?>
                    </th>
                    <th scope="col" class="manage-column">
                        <?php _e('Status', 'vendorpro'); ?>
                    </th>
                    <th scope="col" class="manage-column">
                        <?php _e('Date', 'vendorpro'); ?>
                    </th>
                    <th scope="col" class="manage-column">
                        <?php _e('Actions', 'vendorpro'); ?>
                    </th>
                </tr>
            </thead>

            <tbody id="the-list">
                <?php if (!empty($withdrawals)): ?>
                    <?php foreach ($withdrawals as $withdrawal):
                        $vendor = vendorpro_get_vendor($withdrawal->vendor_id);
                        $approve_url = wp_nonce_url(admin_url('admin.php?page=vendorpro-withdrawals&withdrawal_action=approve&withdrawal_id=' . $withdrawal->id), 'vendorpro-withdrawal-action');
                        $reject_url = wp_nonce_url(admin_url('admin.php?page=vendorpro-withdrawals&withdrawal_action=reject&withdrawal_id=' . $withdrawal->id), 'vendorpro-withdrawal-action');
                        ?>
                        <tr>
                            <td class="column-primary">
                                <strong>
                                    <?php echo wc_price($withdrawal->amount); ?>
                                </strong>
                            </td>
                            <td>
                                <?php if ($vendor): ?>
                                    <a
                                        href="<?php echo admin_url('admin.php?page=vendorpro-vendors&action=edit&vendor_id=' . $vendor->id); ?>">
                                        <?php echo esc_html($vendor->store_name); ?>
                                    </a>
                                <?php else: ?>
                                    <span class="description">
                                        <?php _e('Vendor Deleted', 'vendorpro'); ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo ucfirst($withdrawal->method); ?>
                            </td>
                            <td>
                                <?php echo nl2br(esc_html($withdrawal->payment_details)); ?>
                                <?php if ($withdrawal->note): ?>
                                    <div class="description" style="margin-top: 5px;"><em>
                                            <?php echo nl2br(esc_html($withdrawal->note)); ?>
                                        </em></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo vendorpro_format_status($withdrawal->status); ?>
                            </td>
                            <td>
                                <?php echo vendorpro_date_format($withdrawal->requested_at); ?>
                            </td>
                            <td>
                                <?php if ($withdrawal->status === 'pending'): ?>
                                    <a href="<?php echo $approve_url; ?>" class="button button-small button-primary"
                                        onclick="return confirm('<?php _e('Approve this withdrawal?', 'vendorpro'); ?>');">
                                        <?php _e('Approve', 'vendorpro'); ?>
                                    </a>
                                    <a href="<?php echo $reject_url; ?>" class="button button-small"
                                        onclick="return confirm('<?php _e('Reject this withdrawal?', 'vendorpro'); ?>');"
                                        style="color: #a00; border-color: #d00;">
                                        <?php _e('Reject', 'vendorpro'); ?>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">
                            <?php _e('No withdrawal requests found.', 'vendorpro'); ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </form>
</div>