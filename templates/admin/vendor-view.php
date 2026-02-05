<?php
/**
 * Admin: Vendor View Template
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap vendorpro-admin-wrap">
    <h1 class="wp-heading-inline">
        <?php echo esc_html($vendor->store_name); ?>
    </h1>
    <a href="<?php echo admin_url('admin.php?page=vendorpro-vendors&action=edit&vendor_id=' . $vendor->id); ?>"
        class="page-title-action">
        <?php _e('Edit', 'vendorpro'); ?>
    </a>

    <hr class="wp-header-end">

    <div class="vendorpro-vendor-view-grid">
        <!-- Stats Card -->
        <div class="card">
            <h2>
                <?php _e('Performance', 'vendorpro'); ?>
            </h2>
            <p><strong>
                    <?php _e('Total Sales:', 'vendorpro'); ?>
                </strong>
                <?php echo wc_price($stats['total_sales']); ?>
            </p>
            <p><strong>
                    <?php _e('Total Orders:', 'vendorpro'); ?>
                </strong>
                <?php echo number_format($stats['orders']); ?>
            </p>
            <p><strong>
                    <?php _e('Commission Paid:', 'vendorpro'); ?>
                </strong>
                <?php echo wc_price($stats['commission']); ?>
            </p>
            <p><strong>
                    <?php _e('Current Balance:', 'vendorpro'); ?>
                </strong>
                <?php echo wc_price(vendorpro_get_vendor_balance($vendor->id)); ?>
            </p>
        </div>

        <!-- Info Card -->
        <div class="card">
            <h2>
                <?php _e('Store Information', 'vendorpro'); ?>
            </h2>
            <p><strong>
                    <?php _e('Email:', 'vendorpro'); ?>
                </strong> <a href="mailto:<?php echo esc_attr($vendor->email); ?>">
                    <?php echo esc_html($vendor->email); ?>
                </a></p>
            <p><strong>
                    <?php _e('Phone:', 'vendorpro'); ?>
                </strong>
                <?php echo esc_html($vendor->phone); ?>
            </p>
            <p><strong>
                    <?php _e('Address:', 'vendorpro'); ?>
                </strong><br>
                <?php echo nl2br(esc_html($vendor->address)); ?><br>
                <?php echo esc_html($vendor->city . ', ' . $vendor->state . ' ' . $vendor->postcode . ', ' . $vendor->country); ?>
            </p>
        </div>
    </div>
</div>