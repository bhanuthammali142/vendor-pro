<?php
/**
 * Vendor Dashboard - Products Page
 */

if (!defined('ABSPATH')) {
    exit;
}

$vendor = vendorpro_get_current_vendor();
if (!$vendor) {
    return;
}

// Handle bulk actions or deletion if needed (simplified for now)
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['product_id'])) {
    // Check nonce...
    // Delete product logic
}

$paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
$products = vendorpro_get_vendor_products($vendor->id, array(
    'posts_per_page' => 10,
    'paged' => $paged,
    'post_status' => 'any'
));

$product_count = count($products); // Total count needed for pagination logic properly
?>

<div class="vendorpro-products-page">
    <div class="vendorpro-page-header">
        <h1>
            <?php _e('Products', 'vendorpro'); ?>
        </h1>
        <a href="<?php echo admin_url('post-new.php?post_type=product'); ?>" class="vendorpro-btn-primary">
            <?php _e('Add New Product', 'vendorpro'); ?>
        </a>
    </div>

    <!-- Products List -->
    <div class="vendorpro-dashboard-card">
        <?php if (!empty($products)): ?>
            <table class="vendorpro-dashboard-table">
                <thead>
                    <tr>
                        <th>
                            <?php _e('Image', 'vendorpro'); ?>
                        </th>
                        <th>
                            <?php _e('Name', 'vendorpro'); ?>
                        </th>
                        <th>
                            <?php _e('Status', 'vendorpro'); ?>
                        </th>
                        <th>
                            <?php _e('Price', 'vendorpro'); ?>
                        </th>
                        <th>
                            <?php _e('Stock', 'vendorpro'); ?>
                        </th>
                        <th>
                            <?php _e('Earning', 'vendorpro'); ?>
                        </th>
                        <th>
                            <?php _e('Actions', 'vendorpro'); ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product_post):
                        $product = wc_get_product($product_post->ID);
                        ?>
                        <tr>
                            <td>
                                <?php echo $product->get_image(array(50, 50)); ?>
                            </td>
                            <td>
                                <a href="<?php echo get_permalink($product->get_id()); ?>" target="_blank">
                                    <strong>
                                        <?php echo esc_html($product->get_name()); ?>
                                    </strong>
                                </a>
                            </td>
                            <td>
                                <?php
                                $status = $product->get_status();
                                $status_label = ucfirst($status);
                                $status_class = $status == 'publish' ? 'success' : 'warning';
                                echo '<span class="status-badge ' . $status_class . '">' . $status_label . '</span>';
                                ?>
                            </td>
                            <td>
                                <?php echo $product->get_price_html(); ?>
                            </td>
                            <td>
                                <?php
                                if ($product->is_in_stock()) {
                                    echo '<span class="stock-status in-stock">' . __('In Stock', 'vendorpro') . '</span>';
                                    if ($product->managing_stock()) {
                                        echo ' (' . $product->get_stock_quantity() . ')';
                                    }
                                } else {
                                    echo '<span class="stock-status out-of-stock">' . __('Out of Stock', 'vendorpro') . '</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                // Simplified earning calc
                                $commission_rate = vendorpro_get_commission_rate(); // Global for now
                                // Real calc is per order, but estimated here:
                                // Price - (Price * Rate / 100)
                                echo wc_price($product->get_price() * (1 - ($commission_rate / 100)));
                                ?>
                            </td>
                            <td>
                                <a href="<?php echo get_edit_post_link($product->get_id()); ?>" class="action-btn edit"
                                    title="<?php _e('Edit', 'vendorpro'); ?>">
                                    <span class="dashicons dashicons-edit"></span>
                                </a>
                                <a href="<?php echo get_permalink($product->get_id()); ?>" class="action-btn view"
                                    target="_blank" title="<?php _e('View', 'vendorpro'); ?>">
                                    <span class="dashicons dashicons-visibility"></span>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Pagination (Simplified) -->
            <div class="vendorpro-pagination">
                <?php
                // Standard WP pagination logic needed here using paginate_links()
                // For MVP, just simple Next/Prev if needed or standard WP function
                // echo paginate_links(...);
                ?>
            </div>

        <?php else: ?>
            <div class="vendorpro-empty-state">
                <div class="vendorpro-empty-state-icon">📦</div>
                <h3>
                    <?php _e('No Products Found', 'vendorpro'); ?>
                </h3>
                <p>
                    <?php _e('Ready to start selling? Add your first product!', 'vendorpro'); ?>
                </p>
                <a href="<?php echo admin_url('post-new.php?post_type=product'); ?>" class="vendorpro-btn-primary">
                    <?php _e('Add Product', 'vendorpro'); ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .status-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-badge.success {
        background: #d4edda;
        color: #155724;
    }

    .status-badge.warning {
        background: #fff3cd;
        color: #856404;
    }

    .action-btn {
        text-decoration: none;
        margin-right: 5px;
        color: #666;
    }

    .action-btn:hover {
        color: #0071DC;
    }
</style>