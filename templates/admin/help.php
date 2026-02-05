<?php
/**
 * Admin: Help Template
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap vendorpro-admin-wrap">
    <h1 class="wp-heading-inline">
        <?php _e('Help & Support', 'vendorpro'); ?>
    </h1>
    <hr class="wp-header-end">

    <div class="vendorpro-help-grid">
        <div class="card">
            <h2>
                <?php _e('Documentation', 'vendorpro'); ?>
            </h2>
            <p>
                <?php _e('Need help configuring the plugin? Check out our online documentation.', 'vendorpro'); ?>
            </p>
            <a href="https://vendorpro-marketplace.com/docs" target="_blank" class="button button-primary">
                <?php _e('Read Documentation', 'vendorpro'); ?>
            </a>
        </div>

        <div class="card">
            <h2>
                <?php _e('Support', 'vendorpro'); ?>
            </h2>
            <p>
                <?php _e('Having trouble? Contact our support team for assistance.', 'vendorpro'); ?>
            </p>
            <a href="https://vendorpro-marketplace.com/support" target="_blank" class="button">
                <?php _e('Contact Support', 'vendorpro'); ?>
            </a>
        </div>

        <div class="card">
            <h2>
                <?php _e('Shortcodes', 'vendorpro'); ?>
            </h2>
            <p><strong>[vendorpro_dashboard]</strong> -
                <?php _e('Displays the Vendor Dashboard.', 'vendorpro'); ?>
            </p>
            <p><strong>[vendorpro_vendor_registration]</strong> -
                <?php _e('Displays the Registration Form.', 'vendorpro'); ?>
            </p>
            <p><strong>[vendorpro_vendors]</strong> -
                <?php _e('Displays a list of verified vendors.', 'vendorpro'); ?>
            </p>
        </div>
    </div>
</div>

<style>
    .vendorpro-help-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }
</style>