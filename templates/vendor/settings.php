<?php
/**
 * Vendor Dashboard - Settings/Store Page
 */

if (!defined('ABSPATH')) {
    exit;
}

$vendor = vendorpro_get_current_vendor();
if (!$vendor) {
    return;
}

// Handle form submission
if (isset($_POST['vendorpro_update_store']) && wp_verify_nonce($_POST['_wpnonce'], 'vendorpro_update_store')) {
    $update_data = array(
        'store_name' => sanitize_text_field($_POST['store_name']),
        'store_description' => sanitize_textarea_field($_POST['store_description']),
        'address' => sanitize_textarea_field($_POST['address']),
        'address_2' => sanitize_text_field($_POST['address_2']),
        'city' => sanitize_text_field($_POST['city']),
        'postcode' => sanitize_text_field($_POST['postcode']),
        'country' => sanitize_text_field($_POST['country']),
        'state' => sanitize_text_field($_POST['state']),
        'phone' => sanitize_text_field($_POST['phone']),
        'email' => sanitize_email($_POST['email'])
    );

    // Handle banner upload
    if (!empty($_FILES['store_banner']['name'])) {
        $banner = vendorpro_handle_file_upload($_FILES['store_banner'], 'banner');
        if (!is_wp_error($banner)) {
            $update_data['store_banner'] = $banner['url'];
        }
    }

    // Handle logo upload
    if (!empty($_FILES['store_logo']['name'])) {
        $logo = vendorpro_handle_file_upload($_FILES['store_logo'], 'logo');
        if (!is_wp_error($logo)) {
            $update_data['store_logo'] = $logo['url'];
        }
    }

    VendorPro_Database::instance()->update_vendor($vendor->id, $update_data);

    echo '<div class="vendorpro-message success">' . __('Store settings updated successfully!', 'vendorpro') . '</div>';

    // Refresh vendor data
    $vendor = vendorpro_get_current_vendor();
}
?>

<div class="vendorpro-settings-page">
    <div class="vendorpro-page-header">
        <h1>
            <?php _e('Settings', 'vendorpro'); ?> <span class="arrow">→</span> <a
                href="<?php echo vendorpro_get_store_url($vendor->store_slug); ?>" target="_blank">
                <?php _e('Visit Store', 'vendorpro'); ?>
            </a>
        </h1>
        <button type="submit" form="store-settings-form" class="vendorpro-btn-primary">
            <?php _e('Update Settings', 'vendorpro'); ?>
        </button>
    </div>

    <form id="store-settings-form" method="post" enctype="multipart/form-data" class="vendorpro-settings-form">
        <?php wp_nonce_field('vendorpro_update_store'); ?>
        <input type="hidden" name="vendorpro_update_store" value="1">

        <!-- Banner Upload -->
        <div class="vendorpro-form-section">
            <div class="vendorpro-banner-upload">
                <div class="banner-preview"
                    style="background-image: url('<?php echo esc_url($vendor->store_banner ?: ''); ?>');">
                    <?php if (!$vendor->store_banner): ?>
                        <div class="banner-placeholder">
                            <div class="upload-icons">
                                <span class="dashicons dashicons-format-image"></span>
                                <span class="dashicons dashicons-video-alt2"></span>
                                <span class="dashicons dashicons-format-gallery"></span>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="banner-upload-controls">
                        <label for="store_banner" class="upload-btn">
                            <span class="dashicons dashicons-upload"></span>
                            <?php _e('Upload Banner', 'vendorpro'); ?>
                        </label>
                        <input type="file" id="store_banner" name="store_banner" accept="image/*" style="display:none;">
                        <?php if ($vendor->store_banner): ?>
                            <button type="button" class="remove-banner-btn">
                                <span class="dashicons dashicons-no"></span>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Picture -->
        <div class="vendorpro-form-section">
            <label class="vendorpro-form-label">
                <?php _e('Profile Picture', 'vendorpro'); ?>
            </label>
            <div class="vendorpro-profile-picture-upload">
                <div class="profile-picture-preview">
                    <?php if ($vendor->store_logo): ?>
                        <img src="<?php echo esc_url($vendor->store_logo); ?>" alt="Profile">
                    <?php else: ?>
                        <span class="dashicons dashicons-store"></span>
                    <?php endif; ?>
                </div>
                <label for="store_logo" class="upload-profile-btn">
                    <?php _e('Upload Picture', 'vendorpro'); ?>
                </label>
                <input type="file" id="store_logo" name="store_logo" accept="image/*" style="display:none;">
            </div>
        </div>

        <!-- Store Name -->
        <div class="vendorpro-form-section">
            <label class="vendorpro-form-label" for="store_name">
                <?php _e('Store Name', 'vendorpro'); ?> <span class="required">*</span>
            </label>
            <input type="text" id="store_name" name="store_name" value="<?php echo esc_attr($vendor->store_name); ?>"
                placeholder="<?php _e('store name', 'vendorpro'); ?>" class="vendorpro-form-input" required>
        </div>

        <!-- Address Section -->
        <div class="vendorpro-form-section">
            <label class="vendorpro-form-label">
                <?php _e('Address', 'vendorpro'); ?>
            </label>

            <div class="vendorpro-address-fields">
                <div class="form-row">
                    <label>
                        <?php _e('Street', 'vendorpro'); ?>
                    </label>
                    <input type="text" name="address" value="<?php echo esc_attr($vendor->address); ?>"
                        placeholder="<?php _e('Street address', 'vendorpro'); ?>" class="vendorpro-form-input">
                </div>

                <div class="form-row">
                    <label>
                        <?php _e('Street 2', 'vendorpro'); ?>
                    </label>
                    <input type="text" name="address_2" value="<?php echo esc_attr($vendor->address_2); ?>"
                        placeholder="<?php _e('Apartment, suite, unit etc. (optional)', 'vendorpro'); ?>"
                        class="vendorpro-form-input">
                </div>

                <div class="form-row-group">
                    <div class="form-row half">
                        <label>
                            <?php _e('City', 'vendorpro'); ?>
                        </label>
                        <input type="text" name="city" value="<?php echo esc_attr($vendor->city); ?>"
                            placeholder="<?php _e('Town / City', 'vendorpro'); ?>" class="vendorpro-form-input">
                    </div>

                    <div class="form-row half">
                        <label>
                            <?php _e('Post/Zip Code', 'vendorpro'); ?>
                        </label>
                        <input type="text" name="postcode" value="<?php echo esc_attr($vendor->postcode); ?>"
                            placeholder="<?php _e('Postcode / Zip', 'vendorpro'); ?>" class="vendorpro-form-input">
                    </div>
                </div>

                <div class="form-row">
                    <label>
                        <?php _e('Country', 'vendorpro'); ?> <span class="required">*</span>
                    </label>
                    <select name="country" id="country-select" class="vendorpro-form-select" required>
                        <option value="">
                            <?php _e('- Select a location -', 'vendorpro'); ?>
                        </option>
                        <?php
                        $countries = WC()->countries->get_countries();
                        foreach ($countries as $code => $name) {
                            echo '<option value="' . esc_attr($code) . '" ' . selected($vendor->country, $code, false) . '>';
                            echo esc_html($name);
                            echo '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="form-row">
                    <label>
                        <?php _e('State', 'vendorpro'); ?>
                    </label>
                    <input type="text" name="state" value="<?php echo esc_attr($vendor->state); ?>"
                        placeholder="<?php _e('State', 'vendorpro'); ?>" class="vendorpro-form-input">
                </div>
            </div>
        </div>

        <!-- Phone -->
        <div class="vendorpro-form-section">
            <label class="vendorpro-form-label" for="phone">
                <?php _e('Phone', 'vendorpro'); ?>
            </label>
            <input type="tel" id="phone" name="phone" value="<?php echo esc_attr($vendor->phone); ?>"
                placeholder="<?php _e('Phone number', 'vendorpro'); ?>" class="vendorpro-form-input">
        </div>

        <!-- Email -->
        <div class="vendorpro-form-section">
            <label class="vendorpro-form-label" for="email">
                <?php _e('Email', 'vendorpro'); ?> <span class="required">*</span>
            </label>
            <input type="email" id="email" name="email" value="<?php echo esc_attr($vendor->email); ?>"
                placeholder="<?php _e('Email address', 'vendorpro'); ?>" class="vendorpro-form-input" required>
        </div>

        <!-- Store Description -->
        <div class="vendorpro-form-section">
            <label class="vendorpro-form-label" for="store_description">
                <?php _e('Store Description', 'vendorpro'); ?>
            </label>
            <textarea id="store_description" name="store_description" rows="5"
                placeholder="<?php _e('Tell customers about your store...', 'vendorpro'); ?>"
                class="vendorpro-form-textarea"><?php echo esc_textarea($vendor->store_description); ?></textarea>
        </div>
    </form>
</div>

<style>
    .vendorpro-banner-upload {
        margin-bottom: 30px;
    }

    .banner-preview {
        width: 100%;
        height: 300px;
        background-size: cover;
        background-position: center;
        background-color: #f5f5f5;
        border: 2px dashed #ddd;
        border-radius: 8px;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .banner-placeholder {
        text-align: center;
    }

    .upload-icons {
        display: flex;
        gap: 15px;
        font-size: 48px;
        color: #ccc;
    }

    .banner-upload-controls {
        position: absolute;
        bottom: 20px;
        right: 20px;
        display: flex;
        gap: 10px;
    }

    .upload-btn,
    .remove-banner-btn {
        background: white;
        padding: 10px 20px;
        border-radius: 4px;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .vendorpro-profile-picture-upload {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .profile-picture-preview {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        border: 3px solid #ddd;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f5f5f5;
    }

    .profile-picture-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .profile-picture-preview .dashicons {
        font-size: 48px;
        color: #ccc;
    }

    .upload-profile-btn {
        padding: 10px 20px;
        background: #0071DC;
        color: white;
        border-radius: 4px;
        cursor: pointer;
    }

    .vendorpro-address-fields {
        background: #f9f9f9;
        padding: 20px;
        border-radius: 8px;
    }

    .form-row {
        margin-bottom: 15px;
    }

    .form-row label {
        display: block;
        margin-bottom: 5px;
        font-weight: 600;
    }

    .form-row-group {
        display: flex;
        gap: 15px;
    }

    .form-row.half {
        flex: 1;
    }

    .required {
        color: red;
    }
</style>

<script>
    jQuery(document).ready(function ($) {
        // Banner preview
        $('#store_banner').on('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    $('.banner-preview').css('background-image', 'url(' + e.target.result + ')');
                    $('.banner-placeholder').hide();
                };
                reader.readAsDataURL(file);
            }
        });

        // Logo preview
        $('#store_logo').on('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    $('.profile-picture-preview').html('<img src="' + e.target.result + '" alt="Profile">');
                };
                reader.readAsDataURL(file);
            }
        });

        // Remove banner
        $('.remove-banner-btn').on('click', function () {
            $('.banner-preview').css('background-image', 'none');
            $('.banner-placeholder').show();
            $('#store_banner').val('');
        });
    });
</script>