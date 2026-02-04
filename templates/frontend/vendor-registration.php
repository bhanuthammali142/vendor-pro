<?php
/**
 * Vendor Registration Form Template
 */

if (!defined('ABSPATH')) {
    exit;
}

// Check if registration is enabled
if (!vendorpro_is_registration_enabled()) {
    echo '<p>' . __('Vendor registration is currently disabled.', 'vendorpro') . '</p>';
    return;
}

// Check if user is already a vendor
if (is_user_logged_in() && vendorpro_is_vendor()) {
    echo '<p>' . __('You are already a vendor.', 'vendorpro') . ' <a href="' . vendorpro_get_dashboard_url() . '">' . __('Go to dashboard', 'vendorpro') . '</a></p>';
    return;
}

// Handle form submission
if (isset($_POST['vendorpro_register']) && check_admin_referer('vendorpro-registration')) {
    $errors = array();

    // Validate required fields
    if (empty($_POST['store_name'])) {
        $errors[] = __('Store name is required.', 'vendorpro');
    }

    if (empty($_POST['store_email'])) {
        $errors[] = __('Email is required.', 'vendorpro');
    } elseif (!is_email($_POST['store_email'])) {
        $errors[] = __('Please enter a valid email address.', 'vendorpro');
    }

    if (empty($_POST['store_phone'])) {
        $errors[] = __('Phone number is required.', 'vendorpro');
    }

    // If user is not logged in, require account credentials
    if (!is_user_logged_in()) {
        if (empty($_POST['username'])) {
            $errors[] = __('Username is required.', 'vendorpro');
        }

        if (empty($_POST['password'])) {
            $errors[] = __('Password is required.', 'vendorpro');
        }

        if (empty($_POST['confirm_password']) || $_POST['password'] !== $_POST['confirm_password']) {
            $errors[] = __('Passwords do not match.', 'vendorpro');
        }
    }

    if (empty($errors)) {
        // Create user account if not logged in
        if (!is_user_logged_in()) {
            $user_id = wp_create_user(
                sanitize_user($_POST['username']),
                $_POST['password'],
                sanitize_email($_POST['store_email'])
            );

            if (is_wp_error($user_id)) {
                $errors[] = $user_id->get_error_message();
            } else {
                // Log in the user
                wp_set_current_user($user_id);
                wp_set_auth_cookie($user_id);
            }
        } else {
            $user_id = get_current_user_id();
        }

        if (empty($errors)) {
            // Create vendor
            $vendor_data = array(
                'store_name' => sanitize_text_field($_POST['store_name']),
                'store_description' => wp_kses_post($_POST['store_description']),
                'email' => sanitize_email($_POST['store_email']),
                'phone' => sanitize_text_field($_POST['store_phone']),
                'address' => sanitize_textarea_field($_POST['store_address']),
                'city' => sanitize_text_field($_POST['store_city']),
                'state' => sanitize_text_field($_POST['store_state']),
                'country' => sanitize_text_field($_POST['store_country']),
                'postcode' => sanitize_text_field($_POST['store_postcode'])
            );

            $result = VendorPro_Vendor::instance()->create_vendor($user_id, $vendor_data);

            if (is_wp_error($result)) {
                $errors[] = $result->get_error_message();
            } else {
                $success_message = vendorpro_is_approval_required()
                    ? __('Your vendor registration has been submitted. You will be notified once your account has been reviewed.', 'vendorpro')
                    : __('Your vendor account has been created successfully!', 'vendorpro');

                echo '<div class="vendorpro-message success"><p>' . $success_message . '</p></div>';
                echo '<p><a href="' . vendorpro_get_dashboard_url() . '">' . __('Go to Dashboard', 'vendorpro') . '</a></p>';
                return;
            }
        }
    }

    // Display errors
    if (!empty($errors)) {
        echo '<div class="vendorpro-message error"><ul>';
        foreach ($errors as $error) {
            echo '<li>' . esc_html($error) . '</li>';
        }
        echo '</ul></div>';
    }
}
?>

<div class="vendorpro-registration-form">
    <h2>
        <?php _e('Become a Vendor', 'vendorpro'); ?>
    </h2>
    <p>
        <?php _e('Join our marketplace and start selling your products today!', 'vendorpro'); ?>
    </p>

    <form method="post" id="vendorpro-registration-form">
        <?php wp_nonce_field('vendorpro-registration'); ?>

        <?php if (!is_user_logged_in()): ?>
            <h3>
                <?php _e('Account Information', 'vendorpro'); ?>
            </h3>

            <div class="vendorpro-form-row">
                <label for="username">
                    <?php _e('Username *', 'vendorpro'); ?>
                </label>
                <input type="text" id="username" name="username" required
                    value="<?php echo isset($_POST['username']) ? esc_attr($_POST['username']) : ''; ?>">
            </div>

            <div class="vendorpro-form-row">
                <label for="password">
                    <?php _e('Password *', 'vendorpro'); ?>
                </label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="vendorpro-form-row">
                <label for="confirm_password">
                    <?php _e('Confirm Password *', 'vendorpro'); ?>
                </label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
        <?php endif; ?>

        <h3>
            <?php _e('Store Information', 'vendorpro'); ?>
        </h3>

        <div class="vendorpro-form-row">
            <label for="store_name">
                <?php _e('Store Name *', 'vendorpro'); ?>
            </label>
            <input type="text" id="store_name" name="store_name" required
                value="<?php echo isset($_POST['store_name']) ? esc_attr($_POST['store_name']) : ''; ?>">
        </div>

        <div class="vendorpro-form-row">
            <label for="store_description">
                <?php _e('Store Description', 'vendorpro'); ?>
            </label>
            <textarea id="store_description" name="store_description"
                rows="4"><?php echo isset($_POST['store_description']) ? esc_textarea($_POST['store_description']) : ''; ?></textarea>
        </div>

        <div class="vendorpro-form-row">
            <label for="store_email">
                <?php _e('Email *', 'vendorpro'); ?>
            </label>
            <input type="email" id="store_email" name="store_email" required
                value="<?php echo isset($_POST['store_email']) ? esc_attr($_POST['store_email']) : ''; ?>">
        </div>

        <div class="vendorpro-form-row">
            <label for="store_phone">
                <?php _e('Phone *', 'vendorpro'); ?>
            </label>
            <input type="tel" id="store_phone" name="store_phone" required
                value="<?php echo isset($_POST['store_phone']) ? esc_attr($_POST['store_phone']) : ''; ?>">
        </div>

        <h3>
            <?php _e('Address Information', 'vendorpro'); ?>
        </h3>

        <div class="vendorpro-form-row">
            <label for="store_address">
                <?php _e('Street Address', 'vendorpro'); ?>
            </label>
            <textarea id="store_address" name="store_address"
                rows="3"><?php echo isset($_POST['store_address']) ? esc_textarea($_POST['store_address']) : ''; ?></textarea>
        </div>

        <div class="vendorpro-form-row">
            <label for="store_city">
                <?php _e('City', 'vendorpro'); ?>
            </label>
            <input type="text" id="store_city" name="store_city"
                value="<?php echo isset($_POST['store_city']) ? esc_attr($_POST['store_city']) : ''; ?>">
        </div>

        <div class="vendorpro-form-row">
            <label for="store_state">
                <?php _e('State/Province', 'vendorpro'); ?>
            </label>
            <input type="text" id="store_state" name="store_state"
                value="<?php echo isset($_POST['store_state']) ? esc_attr($_POST['store_state']) : ''; ?>">
        </div>

        <div class="vendorpro-form-row">
            <label for="store_country">
                <?php _e('Country', 'vendorpro'); ?>
            </label>
            <input type="text" id="store_country" name="store_country"
                value="<?php echo isset($_POST['store_country']) ? esc_attr($_POST['store_country']) : ''; ?>">
        </div>

        <div class="vendorpro-form-row">
            <label for="store_postcode">
                <?php _e('Postal Code', 'vendorpro'); ?>
            </label>
            <input type="text" id="store_postcode" name="store_postcode"
                value="<?php echo isset($_POST['store_postcode']) ? esc_attr($_POST['store_postcode']) : ''; ?>">
        </div>

        <div class="vendorpro-form-row">
            <label>
                <input type="checkbox" name="terms" required>
                <?php _e('I agree to the terms and conditions', 'vendorpro'); ?> *
            </label>
        </div>

        <button type="submit" name="vendorpro_register" class="vendorpro-submit-btn">
            <?php _e('Register as Vendor', 'vendorpro'); ?>
        </button>
    </form>
</div>