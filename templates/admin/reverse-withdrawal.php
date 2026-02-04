<?php
/**
 * Admin: Reverse Withdrawal Template
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap vendorpro-admin-wrap">
    <h1 class="wp-heading-inline"><?php _e('Reverse Withdrawal', 'vendorpro'); ?></h1>
    <p class="description"><?php _e('Vendors who owe commission fees (Negative Balance) from COD orders.', 'vendorpro'); ?></p>
    <hr class="wp-header-end">

    <div class="card" style="margin-top: 20px;">
        <h2 class="title"><?php _e('Balance Overview', 'vendorpro'); ?></h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('Vendor', 'vendorpro'); ?></th>
                    <th><?php _e('Store Name', 'vendorpro'); ?></th>
                    <th><?php _e('Email', 'vendorpro'); ?></th>
                    <th><?php _e('Balance', 'vendorpro'); ?></th>
                    <th><?php _e('Status', 'vendorpro'); ?></th>
                    <th><?php _e('Actions', 'vendorpro'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($reverse_vendors)): ?>
                    <?php foreach ($reverse_vendors as $vendor): 
                        $threshold = get_option('vendorpro_reverse_threshold', 150);
                        $is_over_limit = abs($vendor->balance) > $threshold;
                    ?>
                    <tr>
                        <td>
                             <a href="<?php echo admin_url('admin.php?page=vendorpro-vendors&action=edit&vendor_id=' . $vendor->id); ?>">
                                <strong>#<?php echo $vendor->id; ?></strong>
                             </a>
                        </td>
                        <td><?php echo esc_html($vendor->store_name); ?></td>
                        <td><?php echo esc_html($vendor->email); ?></td>
                        <td>
                            <strong style="color: #d63638;"><?php echo wc_price($vendor->balance); ?></strong>
                            <?php if ($is_over_limit): ?>
                                <span class="dashicons dashicons-warning" style="color: red;" title="<?php _e('Exceeds Threshold', 'vendorpro'); ?>"></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($is_over_limit): ?>
                                <span class="badge badge-danger"><?php _e('Over Limit', 'vendorpro'); ?></span>
                            <?php else: ?>
                                <span class="badge"><?php _e('Owing', 'vendorpro'); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=vendorpro-commissions&vendor_id=' . $vendor->id . '&status=reverse_withdrawal'); ?>" class="button button-small"><?php _e('View Transactions', 'vendorpro'); ?></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6"><?php _e('No vendors currently owe reverse withdrawal fees.', 'vendorpro'); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <div style="margin-top: 20px;">
        <a href="<?php echo admin_url('admin.php?page=vendorpro-settings&tab=reverse_withdraw'); ?>" class="button"><?php _e('Configure Reverse Withdrawal Settings', 'vendorpro'); ?></a>
    </div>
</div>
