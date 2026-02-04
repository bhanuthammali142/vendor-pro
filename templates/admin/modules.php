<?php
/**
 * Admin: Modules Template
 */

if (!defined('ABSPATH')) {
    exit;
}

$modules = VendorPro_Admin_Modules::get_modules();
?>

<div class="wrap vendorpro-admin-wrap">
    <h1 class="wp-heading-inline">
        <?php _e('Modules', 'vendorpro'); ?>
    </h1>
    <p class="description">
        <?php _e('Enable or disable features for your marketplace.', 'vendorpro'); ?>
    </p>
    <hr class="wp-header-end">

    <div class="vendorpro-modules-grid">
        <?php foreach ($modules as $key => $module): ?>
            <div class="vendorpro-module-card <?php echo $module['active'] ? 'active' : ''; ?>">
                <div class="module-icon">
                    <span class="dashicons <?php echo esc_attr($module['icon']); ?>"></span>
                </div>
                <div class="module-content">
                    <h3>
                        <?php echo esc_html($module['title']); ?>
                    </h3>
                    <p>
                        <?php echo esc_html($module['description']); ?>
                    </p>
                </div>
                <div class="module-actions">
                    <?php
                    $toggle_action = $module['active'] ? 'disable' : 'enable';
                    $toggle_url = wp_nonce_url(add_query_arg(array('action' => $toggle_action, 'module' => $key)), 'vendorpro-toggle-module');
                    ?>
                    <a href="<?php echo $toggle_url; ?>"
                        class="button <?php echo $module['active'] ? '' : 'button-primary'; ?>">
                        <?php echo $module['active'] ? __('Deactivate', 'vendorpro') : __('Activate', 'vendorpro'); ?>
                    </a>

                    <?php if ($module['settings_slug']): ?>
                        <a href="<?php echo admin_url('admin.php?page=vendorpro-settings&tab=' . $module['settings_slug']); ?>"
                            class="button button-secondary settings-link" title="<?php _e('Settings', 'vendorpro'); ?>">
                            <span class="dashicons dashicons-admin-settings"></span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
    .vendorpro-modules-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .vendorpro-module-card {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        transition: all 0.2s ease;
    }

    .vendorpro-module-card:hover {
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        border-color: #bbb;
    }

    .vendorpro-module-card.active {
        border-left: 4px solid #007cba;
    }

    .module-icon {
        font-size: 32px;
        margin-bottom: 15px;
        color: #555;
    }

    .module-icon .dashicons {
        font-size: 32px;
        width: 32px;
        height: 32px;
    }

    .module-content h3 {
        margin: 0 0 10px 0;
        font-size: 16px;
    }

    .module-content p {
        color: #666;
        margin: 0 0 20px 0;
        line-height: 1.5;
        flex-grow: 1;
    }

    .module-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: auto;
    }

    .module-actions .settings-link {
        padding: 5px 10px;
    }

    .module-actions .settings-link .dashicons {
        line-height: 1.3;
    }
</style>