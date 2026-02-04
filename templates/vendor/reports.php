<?php
/**
 * Vendor Dashboard - Reports Page
 */

if (!defined('ABSPATH')) {
    exit;
}

$vendor = vendorpro_get_current_vendor();
if (!$vendor) {
    return;
}

// Get date range
$date_from = isset($_GET['from']) ? sanitize_text_field($_GET['from']) : date('Y-m-01');
$date_to = isset($_GET['to']) ? sanitize_text_field($_GET['to']) : date('Y-m-d');

// Get stats
$stats = vendorpro_get_vendor_report_stats($vendor->id, $date_from, $date_to);
?>

<div class="vendorpro-reports-page">
    <div class="vendorpro-page-header">
        <h1>
            <?php _e('Reports', 'vendorpro'); ?>
        </h1>
        <div class="vendorpro-balance-display">
            <?php _e('Balance:', 'vendorpro'); ?>
            <strong>
                <?php echo wc_price(vendorpro_get_vendor_balance($vendor->id)); ?>
            </strong>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="vendorpro-date-filter">
        <label>
            <?php _e('Date range:', 'vendorpro'); ?>
        </label>
        <select id="vendorpro-date-range" class="vendorpro-select">
            <option value="today">
                <?php _e('Today', 'vendorpro'); ?>
            </option>
            <option value="yesterday">
                <?php _e('Yesterday', 'vendorpro'); ?>
            </option>
            <option value="week">
                <?php _e('This Week', 'vendorpro'); ?>
            </option>
            <option value="month" selected>
                <?php _e('This Month', 'vendorpro'); ?>
            </option>
            <option value="year">
                <?php _e('This Year', 'vendorpro'); ?>
            </option>
            <option value="custom">
                <?php _e('Custom Range', 'vendorpro'); ?>
            </option>
        </select>

        <div id="custom-date-range" style="display:none; margin-left: 15px;">
            <input type="date" id="date-from" value="<?php echo esc_attr($date_from); ?>">
            <span>to</span>
            <input type="date" id="date-to" value="<?php echo esc_attr($date_to); ?>">
            <button class="button">
                <?php _e('Apply', 'vendorpro'); ?>
            </button>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="vendorpro-performance-section">
        <h2>
            <?php _e('Performance', 'vendorpro'); ?>
        </h2>

        <div class="vendorpro-metrics-grid">
            <!-- Row 1 -->
            <div class="vendorpro-metric-card">
                <div class="metric-label">
                    <?php _e('Total sales', 'vendorpro'); ?>
                </div>
                <div class="metric-value">
                    <?php echo wc_price($stats['total_sales']); ?>
                </div>
                <div class="metric-change <?php echo $stats['sales_change'] >= 0 ? 'positive' : 'negative'; ?>">
                    <?php echo abs($stats['sales_change']); ?>%
                </div>
            </div>

            <div class="vendorpro-metric-card">
                <div class="metric-label">
                    <?php _e('Marketplace Commission', 'vendorpro'); ?>
                </div>
                <div class="metric-value">
                    <?php echo wc_price($stats['commission']); ?>
                </div>
                <div class="metric-change <?php echo $stats['commission_change'] >= 0 ? 'negative' : 'positive'; ?>">
                    <?php echo abs($stats['commission_change']); ?>%
                </div>
            </div>

            <div class="vendorpro-metric-card">
                <div class="metric-label">
                    <?php _e('Net sales', 'vendorpro'); ?>
                </div>
                <div class="metric-value">
                    <?php echo wc_price($stats['net_sales']); ?>
                </div>
                <div class="metric-change <?php echo $stats['net_change'] >= 0 ? 'positive' : 'negative'; ?>">
                    <?php echo abs($stats['net_change']); ?>%
                </div>
            </div>

            <div class="vendorpro-metric-card">
                <div class="metric-label">
                    <?php _e('Orders', 'vendorpro'); ?>
                </div>
                <div class="metric-value">
                    <?php echo number_format($stats['orders']); ?>
                </div>
                <div class="metric-change <?php echo $stats['orders_change'] >= 0 ? 'positive' : 'negative'; ?>">
                    <?php echo abs($stats['orders_change']); ?>%
                </div>
            </div>

            <div class="vendorpro-metric-card">
                <div class="metric-label">
                    <?php _e('Products sold', 'vendorpro'); ?>
                </div>
                <div class="metric-value">
                    <?php echo number_format($stats['products_sold']); ?>
                </div>
                <div class="metric-change <?php echo $stats['products_change'] >= 0 ? 'positive' : 'negative'; ?>">
                    <?php echo abs($stats['products_change']); ?>%
                </div>
            </div>

            <!-- Row 2 -->
            <div class="vendorpro-metric-card">
                <div class="metric-label">
                    <?php _e('Total Earning', 'vendorpro'); ?>
                </div>
                <div class="metric-value">
                    <?php echo wc_price($stats['total_earning']); ?>
                </div>
                <div class="metric-change <?php echo $stats['earning_change'] >= 0 ? 'positive' : 'negative'; ?>">
                    <?php echo abs($stats['earning_change']); ?>%
                </div>
            </div>

            <div class="vendorpro-metric-card">
                <div class="metric-label">
                    <?php _e('Marketplace Discount', 'vendorpro'); ?>
                </div>
                <div class="metric-value">
                    <?php echo wc_price($stats['marketplace_discount']); ?>
                </div>
                <div class="metric-change">
                    <?php echo abs($stats['discount_change']); ?>%
                </div>
            </div>

            <div class="vendorpro-metric-card">
                <div class="metric-label">
                    <?php _e('Store Discount', 'vendorpro'); ?>
                </div>
                <div class="metric-value">
                    <?php echo wc_price($stats['store_discount']); ?>
                </div>
                <div class="metric-change">
                    <?php echo abs($stats['store_discount_change']); ?>%
                </div>
            </div>

            <div class="vendorpro-metric-card">
                <div class="metric-label">
                    <?php _e('Variations Sold', 'vendorpro'); ?>
                </div>
                <div class="metric-value">
                    <?php echo number_format($stats['variations_sold']); ?>
                </div>
                <div class="metric-change">
                    <?php echo abs($stats['variations_change']); ?>%
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="vendorpro-charts-section">
        <h2>
            <?php _e('Charts', 'vendorpro'); ?>
        </h2>

        <div class="vendorpro-chart-controls">
            <select id="chart-period" class="vendorpro-select">
                <option value="day">
                    <?php _e('By day', 'vendorpro'); ?>
                </option>
                <option value="week">
                    <?php _e('By week', 'vendorpro'); ?>
                </option>
                <option value="month">
                    <?php _e('By month', 'vendorpro'); ?>
                </option>
            </select>
        </div>

        <div class="vendorpro-charts-grid">
            <div class="vendorpro-chart-card">
                <h3>
                    <?php _e('Net sales', 'vendorpro'); ?>
                </h3>
                <canvas id="net-sales-chart"></canvas>
                <?php if (empty($stats['chart_data'])): ?>
                    <div class="no-data-message">
                        <?php _e('No data for the selected date range', 'vendorpro'); ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="vendorpro-chart-card">
                <h3>
                    <?php _e('Orders', 'vendorpro'); ?>
                </h3>
                <canvas id="orders-chart"></canvas>
                <?php if (empty($stats['chart_data'])): ?>
                    <div class="no-data-message">
                        <?php _e('No data for the selected date range', 'vendorpro'); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function ($) {
        // Date range selector
        $('#vendorpro-date-range').on('change', function () {
            if ($(this).val() === 'custom') {
                $('#custom-date-range').show();
            } else {
                $('#custom-date-range').hide();
                // Auto-apply preset ranges
                var url = '<?php echo vendorpro_get_dashboard_url('reports'); ?>';
                var separator = url.indexOf('?') !== -1 ? '&' : '?';
                window.location.href = url + separator + 'range=' + $(this).val();
            }
        });

        // Chart.js initialization (if data exists)
        <?php if (!empty($stats['chart_data'])): ?>
            const chartData = <?php echo json_encode($stats['chart_data']); ?>;

            // Net Sales Chart
            new Chart(document.getElementById('net-sales-chart'), {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: '<?php _e('Net Sales', 'vendorpro'); ?>',
                        data: chartData.net_sales,
                        borderColor: '#0071DC',
                        backgroundColor: 'rgba(0, 113, 220, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // Orders Chart
            new Chart(document.getElementById('orders-chart'), {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: '<?php _e('Orders', 'vendorpro'); ?>',
                        data: chartData.orders,
                        backgroundColor: '#28a745'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        <?php endif; ?>
    });
</script>