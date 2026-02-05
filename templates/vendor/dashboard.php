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
            <!-- Sidebar Header -->
            <div class="vendorpro-sidebar-header">
                <h3><?php echo esc_html($vendor->store_name); ?></h3>
                <p><?php _e('Vendor Dashboard', 'vendorpro'); ?></p>
            </div>

            <ul class="vendorpro-dashboard-nav">
                <li>
                    <a href="<?php echo vendorpro_get_dashboard_url(); ?>"
                        class="<?php echo $current_page === 'overview' || $current_page === '' ? 'active' : ''; ?>">
                        <span class="dashicons dashicons-dashboard"></span>
                        <?php _e('Dashboard', 'vendorpro'); ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo vendorpro_get_dashboard_url('products'); ?>"
                        class="<?php echo $current_page === 'products' ? 'active' : ''; ?>">
                        <span class="dashicons dashicons-products"></span>
                        <?php _e('Products', 'vendorpro'); ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo vendorpro_get_dashboard_url('orders'); ?>"
                        class="<?php echo $current_page === 'orders' ? 'active' : ''; ?>">
                        <span class="dashicons dashicons-cart"></span>
                        <?php _e('Orders', 'vendorpro'); ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo vendorpro_get_dashboard_url('reports'); ?>"
                        class="<?php echo $current_page === 'reports' ? 'active' : ''; ?>">
                        <span class="dashicons dashicons-chart-area"></span>
                        <?php _e('Reports', 'vendorpro'); ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo vendorpro_get_dashboard_url('withdraw'); ?>"
                        class="<?php echo $current_page === 'withdraw' || $current_page === 'payment-method' ? 'active' : ''; ?>">
                        <span class="dashicons dashicons-money"></span>
                        <?php _e('Withdraw', 'vendorpro'); ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo vendorpro_get_dashboard_url('settings'); ?>"
                        class="<?php echo $current_page === 'settings' ? 'active' : ''; ?>">
                        <span class="dashicons dashicons-admin-settings"></span>
                        <?php _e('Settings', 'vendorpro'); ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo vendorpro_get_store_url($vendor->store_slug); ?>" target="_blank">
                        <span class="dashicons dashicons-external"></span>
                        <?php _e('Visit Store', 'vendorpro'); ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo wp_logout_url(home_url()); ?>" class="logout-link">
                        <span class="dashicons dashicons-migrate"></span>
                        <?php _e('Log Out', 'vendorpro'); ?>
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

            <?php
            switch ($current_page) {
                case 'reports':
                    vendorpro_get_template('vendor/reports.php');
                    break;
                case 'orders':
                    vendorpro_get_template('vendor/orders.php');
                    break;
                case 'settings':
                    vendorpro_get_template('vendor/settings.php');
                    break;
                case 'withdraw':
                    vendorpro_get_template('vendor/withdraw.php');
                    break;
                case 'payment-method':
                    vendorpro_get_template('vendor/payment-method.php');
                    break;
                case 'products':
                    vendorpro_get_template('vendor/products.php');
                    break;
                case 'overview':
                default:
                    ?>
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
                            <a href="<?php echo vendorpro_get_dashboard_url('withdraw'); ?>" class="vendorpro-btn-add">
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
                <?php
            }
            ?>

        </div>
    </div>
</div>