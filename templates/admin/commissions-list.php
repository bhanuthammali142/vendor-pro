<?php
/**
 * Admin: Commissions List Template
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap vendorpro-admin-wrap">
    <h1 class="wp-heading-inline">
        <?php _e('Commissions', 'vendorpro'); ?>
    </h1>
    <hr class="wp-header-end">

    <?php if (isset($_GET['message']) && $_GET['message'] === 'marked_paid'): ?>
        <div class="notice notice-success is-dismissible">
            <p>
                <?php _e('Commission marked as paid.', 'vendorpro'); ?>
            </p>
        </div>
    <?php endif; ?>

    <ul class="subsubsub">
        <li class="all"><a href="<?php echo admin_url('admin.php?page=vendorpro-commissions'); ?>"
                class="<?php echo !isset($_GET['status']) ? 'current' : ''; ?>">
                <?php _e('All', 'vendorpro'); ?>
            </a> |</li>
        <li class="unpaid"><a href="<?php echo admin_url('admin.php?page=vendorpro-commissions&status=unpaid'); ?>"
                class="<?php echo isset($_GET['status']) && $_GET['status'] == 'unpaid' ? 'current' : ''; ?>">
                <?php _e('Unpaid', 'vendorpro'); ?>
            </a> |</li>
        <li class="paid"><a href="<?php echo admin_url('admin.php?page=vendorpro-commissions&status=paid'); ?>"
                class="<?php echo isset($_GET['status']) && $_GET['status'] == 'paid' ? 'current' : ''; ?>">
                <?php _e('Paid', 'vendorpro'); ?>
            </a> |</li>
        <li class="refunded"><a href="<?php echo admin_url('admin.php?page=vendorpro-commissions&status=refunded'); ?>"
                class="<?php echo isset($_GET['status']) && $_GET['status'] == 'refunded' ? 'current' : ''; ?>">
                <?php _e('Refunded', 'vendorpro'); ?>
            </a></li>
    </ul>

    <form method="get">
        <input type="hidden" name="page" value="vendorpro-commissions" />

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

        <table class="wp-list-table widefat fixed striped table-view-list commissions">
            <thead>
                <tr>
                    <th scope="col" class="manage-column column-primary">
                        <?php _e('Reference ID', 'vendorpro'); ?>
                    </th>
                    <th scope="col" class="manage-column">
                        <?php _e('Order', 'vendorpro'); ?>
                    </th>
                    <th scope="col" class="manage-column">
                        <?php _e('Store Name', 'vendorpro'); ?>
                    </th>
                    <th scope="col" class="manage-column">
                        <?php _e('Rate', 'vendorpro'); ?>
                    </th>
                    <th scope="col" class="manage-column">
                        <?php _e('Amount', 'vendorpro'); ?>
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
                <?php if (!empty($commissions)): ?>
                    <?php foreach ($commissions as $commission):
                        $vendor = vendorpro_get_vendor($commission->vendor_id);
                        $order = wc_get_order($commission->order_id);
                        $mark_paid_url = wp_nonce_url(admin_url('admin.php?page=vendorpro-commissions&commission_action=mark_paid&commission_id=' . $commission->id), 'vendorpro-commission-action');
                        ?>
                        <tr>
                            <td class="column-primary">
                                <strong>#
                                    <?php echo $commission->id; ?>
                                </strong>
                                <?php if ($commission->product_id):
                                    $product = wc_get_product($commission->product_id);
                                    if ($product): ?>
                                        <div class="product-name"><small>
                                                <?php echo $product->get_name(); ?>
                                            </small></div>
                                    <?php endif;
                                endif; ?>
                            </td>
                            <td>
                                <?php if ($order): ?>
                                    <a href="<?php echo admin_url('post.php?post=' . $commission->order_id . '&action=edit'); ?>">#
                                        <?php echo $commission->order_id; ?>
                                    </a>
                                <?php else: ?>
                                    <span class="description">
                                        <?php _e('Order Deleted', 'vendorpro'); ?>
                                    </span>
                                <?php endif; ?>
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
                                <?php
                                if ($commission->commission_type == 'fixed') {
                                    echo wc_price($commission->commission_rate);
                                } else {
                                    echo $commission->commission_rate . '%';
                                }
                                ?>
                            </td>
                            <td>
                                <strong>
                                    <?php echo wc_price($commission->vendor_earning); ?>
                                </strong><br>
                                <small class="description">
                                    <?php printf(__('Order Total: %s', 'vendorpro'), wc_price($commission->order_total)); ?>
                                </small>
                            </td>
                            <td>
                                <?php echo vendorpro_format_status($commission->status); ?>
                            </td>
                            <td>
                                <?php echo vendorpro_date_format($commission->created_at); ?>
                            </td>
                            <td>
                                <?php if ($commission->status === 'unpaid'): ?>
                                    <a href="<?php echo $mark_paid_url; ?>" class="button button-small">
                                        <?php _e('Mark Paid', 'vendorpro'); ?>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">
                            <?php _e('No commissions found.', 'vendorpro'); ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </form>
</div>