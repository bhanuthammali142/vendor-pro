<?php
/**
 * Vendor Dashboard - Orders Page
 */

if (!defined('ABSPATH')) {
    exit;
}

$vendor = vendorpro_get_current_vendor();
if (!$vendor) {
    return;
}

// Get filter parameters
$status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : 'all';
$customer = isset($_GET['customer']) ? sanitize_text_field($_GET['customer']) : '';
$search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
$date_from = isset($_GET['from']) ? sanitize_text_field($_GET['from']) : '';
$date_to = isset($_GET['to']) ? sanitize_text_field($_GET['to']) : '';

// Get orders
$orders = vendorpro_get_vendor_orders($vendor->id, array(
    'status' => $status,
    'customer' => $customer,
    'search' => $search,
    'date_from' => $date_from,
    'date_to' => $date_to
));

// Get status counts
$status_counts = vendorpro_get_vendor_order_status_counts($vendor->id);
?>

<div class="vendorpro-orders-page">
    <div class="vendorpro-page-header">
        <h1>
            <?php _e('Orders', 'vendorpro'); ?>
        </h1>
    </div>

    <!-- Status Tabs -->
    <div class="vendorpro-status-tabs">
        <a href="<?php echo vendorpro_get_dashboard_url('orders'); ?>"
            class="<?php echo $status === 'all' ? 'active' : ''; ?>">
            <?php printf(__('All (%d)', 'vendorpro'), $status_counts['all']); ?>
        </a>
        <a href="<?php echo add_query_arg('status', 'pending', vendorpro_get_dashboard_url('orders')); ?>"
            class="<?php echo $status === 'pending' ? 'active' : ''; ?>">
            <?php printf(__('Pending payment (%d)', 'vendorpro'), $status_counts['pending']); ?>
        </a>
        <a href="<?php echo add_query_arg('status', 'processing', vendorpro_get_dashboard_url('orders')); ?>"
            class="<?php echo $status === 'processing' ? 'active' : ''; ?>">
            <?php printf(__('Processing (%d)', 'vendorpro'), $status_counts['processing']); ?>
        </a>
        <a href="<?php echo add_query_arg('status', 'on-hold', vendorpro_get_dashboard_url('orders')); ?>"
            class="<?php echo $status === 'on-hold' ? 'active' : ''; ?>">
            <?php printf(__('On hold (%d)', 'vendorpro'), $status_counts['on-hold']); ?>
        </a>
        <a href="<?php echo add_query_arg('status', 'completed', vendorpro_get_dashboard_url('orders')); ?>"
            class="<?php echo $status === 'completed' ? 'active' : ''; ?>">
            <?php printf(__('Completed (%d)', 'vendorpro'), $status_counts['completed']); ?>
        </a>
        <a href="<?php echo add_query_arg('status', 'cancelled', vendorpro_get_dashboard_url('orders')); ?>"
            class="<?php echo $status === 'cancelled' ? 'active' : ''; ?>">
            <?php printf(__('Cancelled (%d)', 'vendorpro'), $status_counts['cancelled']); ?>
        </a>
        <a href="<?php echo add_query_arg('status', 'refunded', vendorpro_get_dashboard_url('orders')); ?>"
            class="<?php echo $status === 'refunded' ? 'active' : ''; ?>">
            <?php printf(__('Refunded (%d)', 'vendorpro'), $status_counts['refunded']); ?>
        </a>
        <a href="<?php echo add_query_arg('status', 'failed', vendorpro_get_dashboard_url('orders')); ?>"
            class="<?php echo $status === 'failed' ? 'active' : ''; ?>">
            <?php printf(__('Failed (%d)', 'vendorpro'), $status_counts['failed']); ?>
        </a>
    </div>

    <!-- Filters -->
    <div class="vendorpro-orders-filters">
        <form method="get" class="vendorpro-filter-form">
            <input type="hidden" name="page" value="orders">

            <select name="customer" class="vendorpro-select">
                <option value="">
                    <?php _e('Filter by registered customer', 'vendorpro'); ?>
                </option>
                <?php
                $customers = vendorpro_get_vendor_customers($vendor->id);
                foreach ($customers as $cust) {
                    echo '<option value="' . esc_attr($cust->ID) . '" ' . selected($customer, $cust->ID, false) . '>';
                    echo esc_html($cust->display_name . ' (' . $cust->user_email . ')');
                    echo '</option>';
                }
                ?>
            </select>

            <input type="text" name="s" placeholder="<?php _e('Search Orders', 'vendorpro'); ?>"
                value="<?php echo esc_attr($search); ?>" class="vendorpro-search-input">

            <input type="date" name="from" placeholder="<?php _e('From', 'vendorpro'); ?>"
                value="<?php echo esc_attr($date_from); ?>" class="vendorpro-date-input">

            <input type="date" name="to" placeholder="<?php _e('To', 'vendorpro'); ?>"
                value="<?php echo esc_attr($date_to); ?>" class="vendorpro-date-input">

            <button type="submit" class="vendorpro-btn-filter">
                <?php _e('FILTER', 'vendorpro'); ?>
            </button>

            <button type="button" class="vendorpro-btn-export" id="export-all">
                <?php _e('EXPORT ALL', 'vendorpro'); ?>
            </button>

            <button type="button" class="vendorpro-btn-export-filtered" id="export-filtered">
                <?php _e('EXPORT FILTERED', 'vendorpro'); ?>
            </button>
        </form>

        <button type="button" class="vendorpro-btn-reset" id="reset-filters">
            <span class="dashicons dashicons-update"></span>
            <?php _e('Reset', 'vendorpro'); ?>
        </button>
    </div>

    <!-- Orders Table -->
    <div class="vendorpro-orders-table-wrap">
        <?php if (!empty($orders)): ?>
            <table class="vendorpro-orders-table">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all-orders"></th>
                        <th>
                            <?php _e('Order', 'vendorpro'); ?>
                        </th>
                        <th>
                            <?php _e('Order Total', 'vendorpro'); ?>
                        </th>
                        <th>
                            <?php _e('Earning', 'vendorpro'); ?>
                        </th>
                        <th>
                            <?php _e('Status', 'vendorpro'); ?>
                        </th>
                        <th>
                            <?php _e('Customer', 'vendorpro'); ?>
                        </th>
                        <th>
                            <?php _e('Date', 'vendorpro'); ?>
                        </th>
                        <th>
                            <?php _e('Actions', 'vendorpro'); ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order):
                        $order_obj = wc_get_order($order->order_id);
                        if (!$order_obj)
                            continue;

                        $commission = vendorpro_get_order_vendor_commission($order->order_id, $vendor->id);
                        ?>
                        <tr>
                            <td><input type="checkbox" class="order-checkbox" value="<?php echo $order->order_id; ?>"></td>
                            <td>
                                <strong>#
                                    <?php echo $order->order_id; ?>
                                </strong>
                            </td>
                            <td>
                                <?php echo $order_obj->get_formatted_order_total(); ?>
                            </td>
                            <td>
                                <?php echo wc_price($commission); ?>
                            </td>
                            <td>
                                <span class="order-status status-<?php echo esc_attr($order_obj->get_status()); ?>">
                                    <?php echo esc_html(wc_get_order_status_name($order_obj->get_status())); ?>
                                </span>
                            </td>
                            <td>
                                <?php
                                $customer = $order_obj->get_user();
                                echo $customer ? esc_html($customer->display_name) : __('Guest', 'vendorpro');
                                ?>
                            </td>
                            <td>
                                <?php echo $order_obj->get_date_created()->date('Y-m-d H:i'); ?>
                            </td>
                            <td>
                                <a href="<?php echo vendorpro_get_dashboard_url('orders', array('view' => $order->order_id)); ?>"
                                    class="vendorpro-btn-view">
                                    <?php _e('View', 'vendorpro'); ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="vendorpro-no-orders">
                <div class="no-orders-icon">📦</div>
                <h3>
                    <?php _e('No orders found', 'vendorpro'); ?>
                </h3>
                <p>
                    <?php _e('Orders matching your filters will appear here.', 'vendorpro'); ?>
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    jQuery(document).ready(function ($) {
        // Select all checkbox
        $('#select-all-orders').on('change', function () {
            $('.order-checkbox').prop('checked', $(this).prop('checked'));
        });

        // Reset filters
        $('#reset-filters').on('click', function () {
            window.location.href = '<?php echo vendorpro_get_dashboard_url('orders'); ?>';
        });

        // Export functionality
        $('#export-all, #export-filtered').on('click', function () {
            const isFiltered = $(this).attr('id') === 'export-filtered';
            const url = '<?php echo admin_url('admin-ajax.php'); ?>';

            const data = {
                action: 'vendorpro_export_orders',
                vendor_id: <?php echo $vendor->id; ?>,
                    filtered: isFiltered,
                        status: '<?php echo esc_js($status); ?>',
                            customer: '<?php echo esc_js($customer); ?>',
                                search: '<?php echo esc_js($search); ?>',
                                    from: '<?php echo esc_js($date_from); ?>',
                                        to: '<?php echo esc_js($date_to); ?>',
                                            nonce: '<?php echo wp_create_nonce('vendorpro_export_orders'); ?>'
        };

        window.location.href = url + '?' + $.param(data);
    });
});
</script>