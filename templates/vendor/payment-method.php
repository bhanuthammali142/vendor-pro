<?php
/**
 * Vendor Dashboard - Payment Method Setup
 */

if (!defined('ABSPATH')) {
    exit;
}

$vendor = vendorpro_get_current_vendor();
if (!$vendor) {
    return;
}

$method = isset($_GET['method']) ? sanitize_text_field($_GET['method']) : 'bank';

// Handle form submission
if (isset($_POST['vendorpro_save_payment_method']) && wp_verify_nonce($_POST['_wpnonce'], 'vendorpro_save_payment_method')) {
    $method_data = array();

    if ($method === 'bank') {
        $method_data = array(
            'account_name' => sanitize_text_field($_POST['account_name']),
            'account_number' => sanitize_text_field($_POST['account_number']),
            'bank_name' => sanitize_text_field($_POST['bank_name']),
            'bank_address' => sanitize_textarea_field($_POST['bank_address']),
            'routing_number' => sanitize_text_field($_POST['routing_number']),
            'iban' => sanitize_text_field($_POST['iban']),
            'swift' => sanitize_text_field($_POST['swift'])
        );
    } elseif ($method === 'paypal') {
        $method_data = array(
            'email' => sanitize_email($_POST['paypal_email'])
        );
    }

    update_user_meta($vendor->user_id, 'vendorpro_payment_method_' . $method, $method_data);

    echo '<div class="vendorpro-message success">' . __('Payment method saved successfully!', 'vendorpro') . '</div>';
}

// Get existing data
$saved_data = get_user_meta($vendor->user_id, 'vendorpro_payment_method_' . $method, true);
if (!is_array($saved_data)) {
    $saved_data = array();
}
?>

<div class="vendorpro-payment-method-page">
    <div class="vendorpro-page-header">
        <h1>
            <?php _e('Payment Method', 'vendorpro'); ?> <span class="arrow">→</span> <a
                href="<?php echo vendorpro_get_store_url($vendor->store_slug); ?>" target="_blank">
                <?php _e('Visit Store', 'vendorpro'); ?>
            </a>
        </h1>
    </div>

    <div class="payment-method-info">
        <p>
            <?php _e('These are the withdraw methods available for you. Please update your payment information below to submit withdraw requests and get your store payments seamlessly.', 'vendorpro'); ?>
        </p>
    </div>

    <div class="payment-method-form-section">
        <h2>
            <?php _e('Payment Methods', 'vendorpro'); ?>
        </h2>

        <div class="payment-method-selector">
            <button type="button" class="method-select-btn <?php echo $method === 'bank' ? 'active' : ''; ?>"
                onclick="window.location.href='<?php echo add_query_arg('method', 'bank', vendorpro_get_dashboard_url('payment-method')); ?>'">
                <span class="dashicons dashicons-building"></span>
                <?php _e('Direct to Bank Transfer', 'vendorpro'); ?>
            </button>
            <!-- Add more method buttons here -->
        </div>

        <?php if ($method === 'bank'): ?>
            <form method="post" class="vendorpro-payment-form">
                <?php wp_nonce_field('vendorpro_save_payment_method'); ?>
                <input type="hidden" name="vendorpro_save_payment_method" value="1">

                <div class="form-row">
                    <label>
                        <?php _e('Account Name', 'vendorpro'); ?> <span class="required">*</span>
                    </label>
                    <input type="text" name="account_name"
                        value="<?php echo esc_attr($saved_data['account_name'] ?? ''); ?>"
                        placeholder="<?php _e('Your bank account name', 'vendorpro'); ?>" class="vendorpro-form-input"
                        required>
                </div>

                <div class="form-row">
                    <label>
                        <?php _e('Account Number', 'vendorpro'); ?> <span class="required">*</span>
                    </label>
                    <input type="text" name="account_number"
                        value="<?php echo esc_attr($saved_data['account_number'] ?? ''); ?>"
                        placeholder="<?php _e('Your bank account number', 'vendorpro'); ?>" class="vendorpro-form-input"
                        required>
                </div>

                <div class="form-row">
                    <label>
                        <?php _e('Bank Name', 'vendorpro'); ?> <span class="required">*</span>
                    </label>
                    <input type="text" name="bank_name" value="<?php echo esc_attr($saved_data['bank_name'] ?? ''); ?>"
                        placeholder="<?php _e('Name of bank', 'vendorpro'); ?>" class="vendorpro-form-input" required>
                </div>

                <div class="form-row">
                    <label>
                        <?php _e('Bank Address', 'vendorpro'); ?>
                    </label>
                    <textarea name="bank_address" rows="3" placeholder="<?php _e('Address of your bank', 'vendorpro'); ?>"
                        class="vendorpro-form-textarea"><?php echo esc_textarea($saved_data['bank_address'] ?? ''); ?></textarea>
                </div>

                <div class="form-row">
                    <label>
                        <?php _e('Routing Number', 'vendorpro'); ?>
                    </label>
                    <input type="text" name="routing_number"
                        value="<?php echo esc_attr($saved_data['routing_number'] ?? ''); ?>"
                        placeholder="<?php _e('Routing number', 'vendorpro'); ?>" class="vendorpro-form-input">
                </div>

                <div class="form-row">
                    <label>
                        <?php _e('IBAN', 'vendorpro'); ?>
                    </label>
                    <input type="text" name="iban" value="<?php echo esc_attr($saved_data['iban'] ?? ''); ?>"
                        placeholder="<?php _e('IBAN', 'vendorpro'); ?>" class="vendorpro-form-input">
                </div>

                <div class="form-row">
                    <label>
                        <?php _e('SWIFT Code', 'vendorpro'); ?>
                    </label>
                    <input type="text" name="swift" value="<?php echo esc_attr($saved_data['swift'] ?? ''); ?>"
                        placeholder="<?php _e('SWIFT code', 'vendorpro'); ?>" class="vendorpro-form-input">
                </div>

                <div class="form-actions">
                    <button type="submit" class="vendorpro-btn-primary">
                        <?php _e('Update Settings', 'vendorpro'); ?>
                    </button>
                    <a href="<?php echo vendorpro_get_dashboard_url('withdraw'); ?>" class="vendorpro-btn-secondary">
                        <?php _e('Cancel', 'vendorpro'); ?>
                    </a>
                </div>
            </form>

        <?php elseif ($method === 'paypal'): ?>
            <form method="post" class="vendorpro-payment-form">
                <?php wp_nonce_field('vendorpro_save_payment_method'); ?>
                <input type="hidden" name="vendorpro_save_payment_method" value="1">

                <div class="form-row">
                    <label>
                        <?php _e('PayPal Email', 'vendorpro'); ?> <span class="required">*</span>
                    </label>
                    <input type="email" name="paypal_email" value="<?php echo esc_attr($saved_data['email'] ?? ''); ?>"
                        placeholder="<?php _e('you@example.com', 'vendorpro'); ?>" class="vendorpro-form-input" required>
                </div>

                <div class="form-actions">
                    <button type="submit" class="vendorpro-btn-primary">
                        <?php _e('Update Settings', 'vendorpro'); ?>
                    </button>
                    <a href="<?php echo vendorpro_get_dashboard_url('withdraw'); ?>" class="vendorpro-btn-secondary">
                        <?php _e('Cancel', 'vendorpro'); ?>
                    </a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<style>
    .payment-method-info {
        background: #f9f9f9;
        padding: 20px;
        border-left: 4px solid #0071DC;
        margin: 20px 0;
    }

    .payment-method-selector {
        display: flex;
        gap: 15px;
        margin: 20px 0;
    }

    .method-select-btn {
        flex: 1;
        padding: 20px;
        border: 2px solid #ddd;
        background: white;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: all 0.3s;
    }

    .method-select-btn:hover {
        border-color: #0071DC;
        background: #f0f8ff;
    }

    .method-select-btn.active {
        border-color: #0071DC;
        background: #0071DC;
        color: white;
    }

    .method-select-btn .dashicons {
        font-size: 24px;
    }

    .vendorpro-payment-form {
        background: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .form-actions {
        display: flex;
        gap: 15px;
        margin-top: 30px;
    }

    .vendorpro-btn-secondary {
        padding: 12px 24px;
        background: #6c757d;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }
</style>