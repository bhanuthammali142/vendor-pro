<?php
/**
 * Admin: Vendor Edit Template
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap vendorpro-admin-wrap">
    <h1 class="wp-heading-inline">
        <?php _e('Edit Vendor', 'vendorpro'); ?>
    </h1>
    <a href="<?php echo admin_url('admin.php?page=vendorpro-vendors'); ?>" class="page-title-action">
        <?php _e('Back to List', 'vendorpro'); ?>
    </a>

    <hr class="wp-header-end">

    <div class="card" style="max-width: 800px; margin-top: 20px;">
        <form method="post">
            <?php wp_nonce_field('vendorpro-edit-vendor'); ?>

            <table class="form-table">
                <tr>
                    <th scope="row"><label for="store_name">
                            <?php _e('Store Name', 'vendorpro'); ?>
                        </label></th>
                    <td>
                        <input type="text" name="store_name" id="store_name"
                            value="<?php echo esc_attr($vendor->store_name); ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="store_slug">
                            <?php _e('Store Slug', 'vendorpro'); ?>
                        </label></th>
                    <td>
                        <input type="text" name="store_slug" id="store_slug"
                            value="<?php echo esc_attr($vendor->store_slug); ?>" class="regular-text">
                        <p class="description">
                            <?php echo home_url('/store/'); ?><strong>
                                <?php echo esc_html($vendor->store_slug); ?>
                            </strong>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="store_email">
                            <?php _e('Email', 'vendorpro'); ?>
                        </label></th>
                    <td>
                        <input type="email" name="email" id="store_email"
                            value="<?php echo esc_attr($vendor->email); ?>" class="regular-text">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="commission_rate">
                            <?php _e('Commission Rate (%)', 'vendorpro'); ?>
                        </label></th>
                    <td>
                        <input type="number" step="0.01" name="commission_rate" id="commission_rate"
                            value="<?php echo esc_attr($vendor->commission_rate); ?>" class="small-text"> %
                        <p class="description">
                            <?php _e('Leave empty to use global setting.', 'vendorpro'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="status">
                            <?php _e('Status', 'vendorpro'); ?>
                        </label></th>
                    <td>
                        <select name="status" id="status">
                            <option value="pending" <?php selected($vendor->status, 'pending'); ?>>
                                <?php _e('Pending', 'vendorpro'); ?>
                            </option>
                            <option value="approved" <?php selected($vendor->status, 'approved'); ?>>
                                <?php _e('Approved', 'vendorpro'); ?>
                            </option>
                            <option value="rejected" <?php selected($vendor->status, 'rejected'); ?>>
                                <?php _e('Rejected', 'vendorpro'); ?>
                            </option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php _e('Featured', 'vendorpro'); ?>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" name="featured" value="1" <?php checked($vendor->featured, 1); ?>>
                            <?php _e('Mark as featured vendor', 'vendorpro'); ?>
                        </label>
                    </td>
                </tr>
            </table>

            <p class="submit">
                <input type="submit" name="submit" id="submit" class="button button-primary"
                    value="<?php _e('Update Vendor', 'vendorpro'); ?>">
            </p>
        </form>
    </div>
</div>