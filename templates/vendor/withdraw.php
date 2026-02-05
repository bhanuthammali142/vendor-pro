<?php
/**
 * Vendor Dashboard - Withdraw Page
 */

if (!defined('ABSPATH')) {
    exit;
}

$vendor = vendorpro_get_current_vendor();
if (!$vendor) {
    return;
}

$balance = vendorpro_get_vendor_balance($vendor->id);
$min_withdraw = floatval(get_option('vendorpro_min_withdraw_amount', 50));
$pending_withdrawals = vendorpro_get_vendor_pending_withdrawals($vendor->id);
$payment_methods = vendorpro_get_vendor_payment_methods($vendor->id);

// Handle withdrawal request
if (isset($_POST['vendorpro_request_withdrawal']) && wp_verify_nonce($_POST['_wpnonce'], 'vendorpro_request_withdrawal')) {
    $amount = floatval($_POST['amount']);
    $method = sanitize_text_field($_POST['method']);

    if ($amount < $min_withdraw) {
        echo '<div class="vendorpro-message error">' . sprintf(__('Minimum withdrawal amount is %s', 'vendorpro'), wc_price($min_withdraw)) . '</div>';
    } elseif ($amount > $balance) {
        echo '<div class="vendorpro-message error">' . __('Insufficient balance', 'vendorpro') . '</div>';
    } else {
        $result = VendorPro_Withdrawal::instance()->request_withdrawal($vendor->id, $amount, $method);
        if ($result) {
            echo '<div class="vendorpro-message success">' . __('Withdrawal request submitted successfully!', 'vendorpro') . '</div>';
        } else {
            echo '<div class="vendorpro-message error">' . __('Failed to submit withdrawal request', 'vendorpro') . '</div>';
        }
    }
}
?>

<div class="vendorpro-withdraw-page">
    <div class="vendorpro-page-header">
        <h1>
            <?php _e('Withdraw', 'vendorpro'); ?>
        </h1>
    </div>

    <!-- Balance Section -->
    <div class="vendorpro-balance-section">
        <h2>
            <?php _e('Balance', 'vendorpro'); ?>
        </h2>

        <div class="balance-card">
            <div class="balance-label">
                <?php _e('Your Balance:', 'vendorpro'); ?>
            </div>
            <div class="balance-amount">
                <?php echo wc_price($balance); ?>
            </div>
            <div class="balance-info">
                <?php printf(__('Minimum Withdraw Amount: %s', 'vendorpro'), '<strong>' . wc_price($min_withdraw) . '</strong>'); ?>
            </div>
        </div>
    </div>

    <!-- Request Withdrawal Form -->
    <?php if ($balance >= $min_withdraw): ?>
        <div class="vendorpro-withdraw-form-section">
            <button type="button" class="vendorpro-btn-primary" id="request-withdraw-btn">
                <?php _e('Request Withdraw', 'vendorpro'); ?>
            </button>

            <div id="withdraw-form-modal" class="vendorpro-modal" style="display:none;">
                <div class="modal-content">
                    <span class="modal-close">&times;</span>
                    <h3>
                        <?php _e('Request Withdrawal', 'vendorpro'); ?>
                    </h3>

                    <form method="post" class="vendorpro-withdraw-form">
                        <?php wp_nonce_field('vendorpro_request_withdrawal'); ?>
                        <input type="hidden" name="vendorpro_request_withdrawal" value="1">

                        <div class="form-row">
                            <label>
                                <?php _e('Amount', 'vendorpro'); ?> <span class="required">*</span>
                            </label>
                            <input type="number" name="amount" step="0.01" min="<?php echo $min_withdraw; ?>"
                                max="<?php echo $balance; ?>" placeholder="<?php echo $min_withdraw; ?>"
                                class="vendorpro-form-input" required>
                            <small>
                                <?php printf(__('Available: %s', 'vendorpro'), wc_price($balance)); ?>
                            </small>
                        </div>

                        <div class="form-row">
                            <label>
                                <?php _e('Withdrawal Method', 'vendorpro'); ?> <span class="required">*</span>
                            </label>
                            <select name="method" class="vendorpro-form-select" required>
                                <option value="">
                                    <?php _e('Select Method', 'vendorpro'); ?>
                                </option>
                                <?php
                                $methods = get_option('vendorpro_withdrawal_methods', array('paypal', 'bank'));
                                foreach ($methods as $method) {
                                    $method_name = ucfirst($method);
                                    $has_setup = !empty($payment_methods[$method]);
                                    echo '<option value="' . esc_attr($method) . '" ' . ($has_setup ? '' : 'disabled') . '>';
                                    echo esc_html($method_name);
                                    if (!$has_setup) {
                                        echo ' (' . __('Not configured', 'vendorpro') . ')';
                                    }
                                    echo '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <button type="submit" class="vendorpro-btn-primary">
                            <?php _e('Submit Request', 'vendorpro'); ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="vendorpro-message info">
            <?php printf(__('You need at least %s to request a withdrawal.', 'vendorpro'), wc_price($min_withdraw)); ?>
        </div>
    <?php endif; ?>

    <!-- Payment Details Section -->
    <div class="vendorpro-payment-details-section">
        <h2>
            <?php _e('Payment Details', 'vendorpro'); ?>
        </h2>

        <div class="payment-last-payment">
            <strong>
                <?php _e('Last Payment:', 'vendorpro'); ?>
            </strong>
            <?php
            $last_payment = vendorpro_get_vendor_last_payment($vendor->id);
            if ($last_payment) {
                echo sprintf(__('You do not have any approved withdraw yet.', 'vendorpro'));
            } else {
                echo __('You do not have any approved withdraw yet.', 'vendorpro');
            }
            ?>
        </div>

        <div class="payment-view-link">
            <a href="<?php echo vendorpro_get_dashboard_url('withdrawals'); ?>">
                <?php _e('View Payments', 'vendorpro'); ?>
            </a>
        </div>
    </div>

    <!-- Payment Methods Section -->
    <div class="vendorpro-payment-methods-section">
        <div class="section-header">
            <h2>
                <?php _e('Payment Methods', 'vendorpro'); ?>
            </h2>
            <button type="button" class="vendorpro-btn-add-method" id="add-payment-method">
                <?php _e('Add Payment Method', 'vendorpro'); ?> ▲
            </button>
        </div>

        <div id="payment-methods-dropdown" class="payment-methods-dropdown" style="display:none;">
            <button type="button" class="payment-method-option" data-method="bank">
                <span class="dashicons dashicons-building"></span>
                <?php _e('Direct to Bank Transfer', 'vendorpro'); ?>
            </button>
            <!-- Add more methods here -->
        </div>

        <?php if (empty($payment_methods)): ?>
            <div class="no-payment-methods">
                <p>
                    <?php _e('There is no payment method to show.', 'vendorpro'); ?>
                </p>
            </div>
        <?php else: ?>
            <div class="payment-methods-list">
                <?php foreach ($payment_methods as $method => $details): ?>
                    <div class="payment-method-card">
                        <div class="method-icon">
                            <span class="dashicons dashicons-<?php echo $method === 'bank' ? 'building' : 'money'; ?>"></span>
                        </div>
                        <div class="method-details">
                            <h4>
                                <?php echo esc_html(ucfirst($method)); ?>
                            </h4>
                            <p>
                                <?php echo esc_html($details['account']); ?>
                            </p>
                        </div>
                        <div class="method-actions">
                            <a href="<?php echo vendorpro_get_dashboard_url('payment-method', array('method' => $method)); ?>"
                                class="edit-method">
                                <?php _e('Edit', 'vendorpro'); ?>
                            </a>
                            <button type="button" class="delete-method" data-method="<?php echo esc_attr($method); ?>">
                                <?php _e('Delete', 'vendorpro'); ?>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .vendorpro-balance-section {
        margin: 30px 0;
    }

    .balance-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 40px;
        border-radius: 12px;
        text-align: center;
    }

    .balance-label {
        font-size: 18px;
        opacity: 0.9;
        margin-bottom: 10px;
    }

    .balance-amount {
        font-size: 48px;
        font-weight: bold;
        margin-bottom: 20px;
    }

    .balance-info {
        font-size: 14px;
        opacity: 0.8;
    }

    .vendorpro-modal {
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background-color: white;
        padding: 30px;
        border-radius: 8px;
        max-width: 500px;
        width: 90%;
        position: relative;
    }

    .modal-close {
        position: absolute;
        right: 15px;
        top: 15px;
        font-size: 28px;
        cursor: pointer;
    }

    .payment-methods-dropdown {
        background: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin-top: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .payment-method-option {
        width: 100%;
        padding: 15px;
        text-align: left;
        border: none;
        background: white;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .payment-method-option:hover {
        background: #f5f5f5;
    }

    .payment-method-card {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 8px;
        margin-bottom: 15px;
    }

    .method-icon {
        font-size: 32px;
        color: #0071DC;
    }

    .method-details {
        flex: 1;
    }

    .method-actions {
        display: flex;
        gap: 10px;
    }
</style>

<script>
    jQuery(document).ready(function ($) {
        // Open withdrawal modal
        $('#request-withdraw-btn').on('click', function () {
            $('#withdraw-form-modal').fadeIn();
        });

        // Close modal
        $('.modal-close').on('click', function () {
            $('#withdraw-form-modal').fadeOut();
        });

        // Toggle payment methods dropdown
        $('#add-payment-method').on('click', function () {
            $('#payment-methods-dropdown').slideToggle();
            $(this).find('▲, ▼').toggle();
        });

        // Handle payment method selection
        $('.payment-method-option').on('click', function () {
            const method = $(this).data('method');
            window.location.href = '<?php echo vendorpro_get_dashboard_url('payment-method'); ?>&method=' + method;
        });
    });
</script>