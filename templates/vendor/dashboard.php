<?php
/**
 * Vendor Dashboard Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$vendor = vendorpro_get_current_vendor();

if (!$vendor) {
    echo '<p>' . __('Vendor account not found.', 'vendorpro') . '</p>';
    return;
}

$stats = vendorpro_get_vendor_stats($vendor->id);
$current_page = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : 'overview';
?>

<div class="vendorpro-container">
    <div class="vendorpro-dashboard">

        <!-- Sidebar -->
        <div class="vendorpro-dashboard-sidebar">
            <ul class="vendorpro-dashboard-nav">
                <li>
                    <a href="<?php echo vendorpro_get_dashboard_url(); ?>"
                        class="<?php echo $current_page === 'overview' ? 'active' : ''; ?>">
                        <?php _e('Overview', 'vendorpro'); ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo vendorpro_get_dashboard_url('products'); ?>"
                        class="<?php echo $current_page === 'products' ? 'active' : ''; ?>">
                        <?php _e('Products', 'vendorpro'); ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo vendorpro_get_dashboard_url('orders'); ?>"
                        class="<?php echo $current_page === 'orders' ? 'active' : ''; ?>">
                        <?php _e('Orders', 'vendorpro'); ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo vendorpro_get_dashboard_url('earnings'); ?>"
                        class="<?php echo $current_page === 'earnings' ? 'active' : ''; ?>">
                        <?php _e('Earnings', 'vendorpro'); ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo vendorpro_get_dashboard_url('withdrawals'); ?>"
                        class="<?php echo $current_page === 'withdrawals' ? 'active' : ''; ?>">
                        <?php _e('Withdrawals', 'vendorpro'); ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo vendorpro_get_dashboard_url('profile'); ?>"
                        class="<?php echo $current_page === 'profile' ? 'active' : ''; ?>">
                        <?php _e('Profile', 'vendorpro'); ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo vendorpro_get_store_url($vendor->store_slug); ?>" target="_blank">
                        <?php _e('View Store', 'vendorpro'); ?>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="vendorpro-dashboard-content">

            <?php if ($vendor->status === 'pending'): ?>
                <div class="vendorpro-message info">
                    <p>
                        <?php _e('Your vendor account is pending approval. You will be notified once your account has been reviewed.', 'vendorpro'); ?>
                    </p>
                </div>
            <?php endif; ?>

            <?php if ($vendor->enabled == 0): ?>
                <div class="vendorpro-message error">
                    <p>
                        <?php _e('Your vendor account is currently disabled. Please contact support for assistance.', 'vendorpro'); ?>
                    </p>
                </div>
            <?php endif; ?>

            <!-- Stats Overview -->
            <div class="vendorpro-dashboard-stats">
                <div class="vendorpro-stat-box">
                    <div class="vendorpro-stat-label">
                        <?php _e('Total Products', 'vendorpro'); ?>
                    </div>
                    <div class="vendorpro-stat-value">
                        <?php echo esc_html($stats['total_products']); ?>
                    </div>
                </div>

                <div class="vendorpro-stat-box green">
                    <div class="vendorpro-stat-label">
                        <?php _e('Total Orders', 'vendorpro'); ?>
                    </div>
                    <div class="vendorpro-stat-value">
                        <?php echo esc_html($stats['total_orders']); ?>
                    </div>
                </div>

                <div class="vendorpro-stat-box orange">
                    <div class="vendorpro-stat-label">
                        <?php _e('Total Earnings', 'vendorpro'); ?>
                    </div>
                    <div class="vendorpro-stat-value">
                        <?php echo wc_price($stats['total_earnings']); ?>
                    </div>
                </div>

                <div class="vendorpro-stat-box red">
                    <div class="vendorpro-stat-label">
                        <?php _e('Available Balance', 'vendorpro'); ?>
                    </div>
                    <div class="vendorpro-stat-value">
                        <?php echo wc_price($stats['balance']); ?>
                    </div>
                </div>
            </div>

            <!-- Welcome Message -->
            <div class="vendorpro-dashboard-card">
                <h2>
                    <?php printf(__('Welcome back, %s!', 'vendorpro'), esc_html($vendor->store_name)); ?>
                </h2>
                <p>
                    <?php _e('Manage your products, view orders, track your earnings, and request withdrawals from your dashboard.', 'vendorpro'); ?>
                </p>
            </div>

            <!-- Quick Actions -->
            <div class="vendorpro-dashboard-card">
                <h3>
                    <?php _e('Quick Actions', 'vendorpro'); ?>
                </h3>
                <div class="vendorpro-product-actions">
                    <a href="<?php echo admin_url('post-new.php?post_type=product'); ?>" class="vendorpro-btn-add">
                        <?php _e('Add New Product', 'vendorpro'); ?>
                    </a>
                    <a href="<?php echo vendorpro_get_dashboard_url('withdrawals'); ?>" class="vendorpro-btn-add">
                        <?php _e('Request Withdrawal', 'vendorpro'); ?>
                    </a>
                    <a href="<?php echo vendorpro_get_store_url($vendor->store_slug); ?>" class="vendorpro-btn-add"
                        target="_blank">
                        <?php _e('View My Store', 'vendorpro'); ?>
                    </a>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="vendorpro-dashboard-card">
                <h3>
                    <?php _e('Recent Orders', 'vendorpro'); ?>
                </h3>

                <?php
                $recent_orders = vendorpro_get_vendor_orders($vendor->id, array('limit' => 5));

                if (!empty($recent_orders)):
                    ?>
                    <table class="vendorpro-dashboard-table">
                        <thead>
                            <tr>
                                <th>
                                    <?php _e('Order', 'vendorpro'); ?>
                                </th>
                                <th>
                                    <?php _e('Date', 'vendorpro'); ?>
                                </th>
                                <th>
                                    <?php _e('Status', 'vendorpro'); ?>
                                </th>
                                <th>
                                    <?php _e('Total', 'vendorpro'); ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($recent_orders, 0, 5) as $order): ?>
                                <tr>
                                    <td>#
                                        <?php echo esc_html($order->get_id()); ?>
                                    </td>
                                    <td>
                                        <?php echo esc_html($order->get_date_created()->date('Y-m-d H:i')); ?>
                                    </td>
                                    <td>
                                        <?php echo esc_html(wc_get_order_status_name($order->get_status())); ?>
                                    </td>
                                    <td>
                                        <?php echo $order->get_formatted_order_total(); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <p style="margin-top: 20px;">
                        <a href="<?php echo vendorpro_get_dashboard_url('orders'); ?>">
                            <?php _e('View All Orders →', 'vendorpro'); ?>
                        </a>
                    </p>
                <?php else: ?>
                    <div class="vendorpro-empty-state">
                        <div class="vendorpro-empty-state-icon">📦</div>
                        <h3>
                            <?php _e('No Orders Yet', 'vendorpro'); ?>
                        </h3>
                        <p>
                            <?php _e('Your orders will appear here once customers start purchasing your products.', 'vendorpro'); ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>

        </div>

    </div>
</div>