<?php
/**
 * Admin Dashboard Template
 */

if (!defined('ABSPATH')) {
    exit;
}

// Initialize variables with defaults
$total_vendors = isset($total_vendors) ? $total_vendors : 0;
$pending_vendors = isset($pending_vendors) ? $pending_vendors : 0;
$approved_vendors = isset($approved_vendors) ? $approved_vendors : 0;
$pending_withdrawals = isset($pending_withdrawals) ? $pending_withdrawals : 0;
$total_withdrawal_amount = isset($total_withdrawal_amount) ? $total_withdrawal_amount : 0;
$recent_vendors = isset($recent_vendors) ? $recent_vendors : array();
?>

<div class="vendorpro-admin-wrap">
    <div class="vendorpro-admin-header">
        <h1><?php _e('VendorPro Dashboard', 'vendorpro'); ?></h1>
    </div>

    <!-- Stats Grid -->
    <div class="vendorpro-stats-grid">
        <div class="vendorpro-stat-card">
            <h3><?php _e('Total Vendors', 'vendorpro'); ?></h3>
            <p class="stat-value"><?php echo esc_html($total_vendors); ?></p>
        </div>

        <div class="vendorpro-stat-card warning">
            <h3><?php _e('Pending Approval', 'vendorpro'); ?></h3>
            <p class="stat-value"><?php echo esc_html($pending_vendors); ?></p>
        </div>

        <div class="vendorpro-stat-card success">
            <h3><?php _e('Active Vendors', 'vendorpro'); ?></h3>
            <p class="stat-value"><?php echo esc_html($approved_vendors); ?></p>
        </div>

        <div class="vendorpro-stat-card danger">
            <h3><?php _e('Pending Withdrawals', 'vendorpro'); ?></h3>
            <p class="stat-value"><?php echo esc_html($pending_withdrawals); ?></p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="vendorpro-quick-actions" style="margin-bottom: 30px;">
        <h2><?php _e('Quick Actions', 'vendorpro'); ?></h2>
        <div style="display: flex; gap: 15px; flex-wrap: wrap;">
            <a href="<?php echo admin_url('admin.php?page=vendorpro-vendors&status=pending'); ?>"
                class="vendorpro-btn vendorpro-btn-primary">
                <?php _e('Review Pending Vendors', 'vendorpro'); ?>
            </a>
            <a href="<?php echo admin_url('admin.php?page=vendorpro-withdrawals&status=pending'); ?>"
                class="vendorpro-btn vendorpro-btn-success">
                <?php _e('Process Withdrawals', 'vendorpro'); ?>
            </a>
            <a href="<?php echo admin_url('admin.php?page=vendorpro-settings'); ?>"
                class="vendorpro-btn vendorpro-btn-secondary">
                <?php _e('Settings', 'vendorpro'); ?>
            </a>
        </div>
    </div>

    <!-- Recent Vendors -->
    <div class="vendorpro-table">
        <h2 style="padding: 20px 20px 0;"><?php _e('Recent Vendors', 'vendorpro'); ?></h2>

        <?php if (!empty($recent_vendors)): ?>
            <table>
                <thead>
                    <tr>
                        <th><?php _e('Store Name', 'vendorpro'); ?></th>
                        <th><?php _e('Email', 'vendorpro'); ?></th>
                        <th><?php _e('Status', 'vendorpro'); ?></th>
                        <th><?php _e('Registered', 'vendorpro'); ?></th>
                        <th><?php _e('Actions', 'vendorpro'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_vendors as $vendor): ?>
                        <tr>
                            <td><strong><?php echo esc_html($vendor->store_name); ?></strong></td>
                            <td><?php echo esc_html($vendor->email); ?></td>
                            <td><?php echo vendorpro_format_status($vendor->status); ?></td>
                            <td><?php echo vendorpro_date_format($vendor->created_at); ?></td>
                            <td>
                                <div class="vendorpro-actions">
                                    <a href="<?php echo admin_url('admin.php?page=vendorpro-vendors&action=view&vendor_id=' . $vendor->id); ?>"
                                        class="vendorpro-btn vendorpro-btn-secondary">
                                        <?php _e('View', 'vendorpro'); ?>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="padding: 20px;"><?php _e('No vendors found.', 'vendorpro'); ?></p>
        <?php endif; ?>
    </div>

    <!-- System Info -->
    <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
        <h3><?php _e('System Information', 'vendorpro'); ?></h3>
        <p><strong><?php _e('Plugin Version:', 'vendorpro'); ?></strong> <?php echo VENDORPRO_VERSION; ?></p>
        <p><strong><?php _e('Database Version:', 'vendorpro'); ?></strong>
            <?php echo get_option('vendorpro_db_version'); ?></p>
        <p><strong><?php _e('Total Withdrawal Requests:', 'vendorpro'); ?></strong> <?php echo $pending_withdrawals; ?>
            pending</p>
        <?php if ($total_withdrawal_amount): ?>
            <p><strong><?php _e('Pending Withdrawal Amount:', 'vendorpro'); ?></strong>
                <?php echo wc_price($total_withdrawal_amount); ?></p>
        <?php endif; ?>
    </div>
</div>