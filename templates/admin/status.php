<?php
/**
 * Admin: Status Template
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap vendorpro-admin-wrap">
    <h1 class="wp-heading-inline"><?php _e('System Status', 'vendorpro'); ?></h1>
    <hr class="wp-header-end">

    <div class="card" style="max-width: 800px; margin-top: 20px;">
        <h2><?php _e('Environment', 'vendorpro'); ?></h2>
        <table class="widefat striped">
            <tbody>
                <tr>
                    <td><?php _e('VendorPro Version', 'vendorpro'); ?></td>
                    <td><?php echo VENDORPRO_VERSION; ?></td>
                </tr>
                <tr>
                    <td><?php _e('WordPress Version', 'vendorpro'); ?></td>
                    <td><?php echo get_bloginfo('version'); ?></td>
                </tr>
                <tr>
                    <td><?php _e('WooCommerce Version', 'vendorpro'); ?></td>
                    <td><?php echo class_exists('WooCommerce') ? WC()->version : __('Not Installed', 'vendorpro'); ?></td>
                </tr>
                <tr>
                    <td><?php _e('PHP Version', 'vendorpro'); ?></td>
                    <td><?php echo phpversion(); ?></td>
                </tr>
                <tr>
                    <td><?php _e('MySQL Version', 'vendorpro'); ?></td>
                    <td><?php global $wpdb; echo $wpdb->db_version(); ?></td>
                </tr>
                <tr>
                    <td><?php _e('WordPress Memory Limit', 'vendorpro'); ?></td>
                    <td><?php echo WP_MEMORY_LIMIT; ?></td>
                </tr>
            </tbody>
        </table>

        <h2><?php _e('Database Tables', 'vendorpro'); ?></h2>
        <table class="widefat striped">
            <tbody>
                <?php
                global $wpdb;
                $tables = array(
                    'vendorpro_vendors',
                    'vendorpro_commissions',
                    'vendorpro_withdrawals',
                    'vendorpro_vendor_balance'
                );

                foreach ($tables as $table) {
                    $exists = $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}{$table}'") === $wpdb->prefix . $table;
                    ?>
                    <tr>
                        <td><?php echo $table; ?></td>
                        <td>
                            <?php if ($exists): ?>
                                <span style="color: green;" class="dashicons dashicons-yes"></span> <?php _e('Exists', 'vendorpro'); ?>
                            <?php else: ?>
                                <span style="color: red;" class="dashicons dashicons-no"></span> <?php _e('Missing', 'vendorpro'); ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
