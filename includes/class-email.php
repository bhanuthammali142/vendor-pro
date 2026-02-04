<?php
/**
 * Email notifications
 */

if (!defined('ABSPATH')) {
    exit;
}

class VendorPro_Email
{

    /**
     * Instance
     */
    protected static $_instance = null;

    /**
     * Get instance
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        add_action('vendorpro_vendor_created', array($this, 'send_vendor_registration_email'), 10, 2);
        add_action('vendorpro_withdrawal_requested', array($this, 'send_withdrawal_request_email'), 10, 2);
        add_action('vendorpro_withdrawal_approved', array($this, 'send_withdrawal_approved_email'), 10, 2);
        add_action('vendorpro_withdrawal_rejected', array($this, 'send_withdrawal_rejected_email'), 10, 2);
    }

    /**
     * Send vendor registration email
     */
    public function send_vendor_registration_email($vendor_id, $user_id)
    {
        $vendor = VendorPro_Database::instance()->get_vendor($vendor_id);
        $user = get_userdata($user_id);

        if (!$vendor || !$user) {
            return;
        }

        $to = $user->user_email;
        $subject = sprintf(__('Welcome to %s - Vendor Registration', 'vendorpro'), get_bloginfo('name'));

        $message = $this->get_email_header();
        $message .= '<h2>' . __('Welcome!', 'vendorpro') . '</h2>';
        $message .= '<p>' . sprintf(__('Thank you for registering as a vendor on %s.', 'vendorpro'), get_bloginfo('name')) . '</p>';

        if ($vendor->status === 'pending') {
            $message .= '<p>' . __('Your vendor account is pending approval. We will notify you once your account has been reviewed.', 'vendorpro') . '</p>';
        } else {
            $message .= '<p>' . __('Your vendor account has been approved! You can now start selling on our platform.', 'vendorpro') . '</p>';
            $message .= '<p><a href="' . $this->get_dashboard_url() . '" style="background: #0071DC; color: #fff; padding: 12px 24px; text-decoration: none; border-radius: 4px; display: inline-block;">' . __('Go to Dashboard', 'vendorpro') . '</a></p>';
        }

        $message .= '<p><strong>' . __('Store Name:', 'vendorpro') . '</strong> ' . $vendor->store_name . '</p>';
        $message .= '<p><strong>' . __('Store URL:', 'vendorpro') . '</strong> <a href="' . $this->get_store_url($vendor->store_slug) . '">' . $this->get_store_url($vendor->store_slug) . '</a></p>';

        $message .= $this->get_email_footer();

        $this->send_email($to, $subject, $message);

        // Send notification to admin
        $admin_email = get_option('admin_email');
        $admin_subject = sprintf(__('New Vendor Registration - %s', 'vendorpro'), $vendor->store_name);

        $admin_message = $this->get_email_header();
        $admin_message .= '<h2>' . __('New Vendor Registration', 'vendorpro') . '</h2>';
        $admin_message .= '<p>' . sprintf(__('A new vendor has registered on %s.', 'vendorpro'), get_bloginfo('name')) . '</p>';
        $admin_message .= '<p><strong>' . __('Store Name:', 'vendorpro') . '</strong> ' . $vendor->store_name . '</p>';
        $admin_message .= '<p><strong>' . __('Email:', 'vendorpro') . '</strong> ' . $vendor->email . '</p>';
        $admin_message .= '<p><a href="' . admin_url('admin.php?page=vendorpro-vendors') . '">' . __('View All Vendors', 'vendorpro') . '</a></p>';
        $admin_message .= $this->get_email_footer();

        $this->send_email($admin_email, $admin_subject, $admin_message);
    }

    /**
     * Send withdrawal request email
     */
    public function send_withdrawal_request_email($withdrawal_id, $vendor_id)
    {
        $withdrawal = VendorPro_Withdrawal::instance()->get_withdrawal($withdrawal_id);
        $vendor = VendorPro_Database::instance()->get_vendor($vendor_id);

        if (!$withdrawal || !$vendor) {
            return;
        }

        $user = get_userdata($vendor->user_id);

        if (!$user) {
            return;
        }

        // Email to vendor
        $to = $user->user_email;
        $subject = __('Withdrawal Request Received', 'vendorpro');

        $message = $this->get_email_header();
        $message .= '<h2>' . __('Withdrawal Request Received', 'vendorpro') . '</h2>';
        $message .= '<p>' . __('Your withdrawal request has been received and is being processed.', 'vendorpro') . '</p>';
        $message .= '<p><strong>' . __('Amount:', 'vendorpro') . '</strong> ' . wc_price($withdrawal->amount) . '</p>';
        $message .= '<p><strong>' . __('Method:', 'vendorpro') . '</strong> ' . $withdrawal->method . '</p>';
        $message .= '<p><strong>' . __('Status:', 'vendorpro') . '</strong> ' . ucfirst($withdrawal->status) . '</p>';
        $message .= $this->get_email_footer();

        $this->send_email($to, $subject, $message);

        // Email to admin
        $admin_email = get_option('admin_email');
        $admin_subject = sprintf(__('New Withdrawal Request - %s', 'vendorpro'), $vendor->store_name);

        $admin_message = $this->get_email_header();
        $admin_message .= '<h2>' . __('New Withdrawal Request', 'vendorpro') . '</h2>';
        $admin_message .= '<p><strong>' . __('Vendor:', 'vendorpro') . '</strong> ' . $vendor->store_name . '</p>';
        $admin_message .= '<p><strong>' . __('Amount:', 'vendorpro') . '</strong> ' . wc_price($withdrawal->amount) . '</p>';
        $admin_message .= '<p><strong>' . __('Method:', 'vendorpro') . '</strong> ' . $withdrawal->method . '</p>';
        $admin_message .= '<p><a href="' . admin_url('admin.php?page=vendorpro-withdrawals') . '">' . __('View Withdrawal Requests', 'vendorpro') . '</a></p>';
        $admin_message .= $this->get_email_footer();

        $this->send_email($admin_email, $admin_subject, $admin_message);
    }

    /**
     * Send withdrawal approved email
     */
    public function send_withdrawal_approved_email($withdrawal_id, $withdrawal)
    {
        $vendor = VendorPro_Database::instance()->get_vendor($withdrawal->vendor_id);

        if (!$vendor) {
            return;
        }

        $user = get_userdata($vendor->user_id);

        if (!$user) {
            return;
        }

        $to = $user->user_email;
        $subject = __('Withdrawal Request Approved', 'vendorpro');

        $message = $this->get_email_header();
        $message .= '<h2>' . __('Withdrawal Approved!', 'vendorpro') . '</h2>';
        $message .= '<p>' . __('Great news! Your withdrawal request has been approved.', 'vendorpro') . '</p>';
        $message .= '<p><strong>' . __('Amount:', 'vendorpro') . '</strong> ' . wc_price($withdrawal->amount) . '</p>';
        $message .= '<p><strong>' . __('Method:', 'vendorpro') . '</strong> ' . $withdrawal->method . '</p>';
        $message .= '<p>' . __('The payment will be processed shortly.', 'vendorpro') . '</p>';
        $message .= $this->get_email_footer();

        $this->send_email($to, $subject, $message);
    }

    /**
     * Send withdrawal rejected email
     */
    public function send_withdrawal_rejected_email($withdrawal_id, $withdrawal)
    {
        $vendor = VendorPro_Database::instance()->get_vendor($withdrawal->vendor_id);

        if (!$vendor) {
            return;
        }

        $user = get_userdata($vendor->user_id);

        if (!$user) {
            return;
        }

        $to = $user->user_email;
        $subject = __('Withdrawal Request Rejected', 'vendorpro');

        $message = $this->get_email_header();
        $message .= '<h2>' . __('Withdrawal Request Rejected', 'vendorpro') . '</h2>';
        $message .= '<p>' . __('Unfortunately, your withdrawal request has been rejected.', 'vendorpro') . '</p>';
        $message .= '<p><strong>' . __('Amount:', 'vendorpro') . '</strong> ' . wc_price($withdrawal->amount) . '</p>';

        if ($withdrawal->note) {
            $message .= '<p><strong>' . __('Reason:', 'vendorpro') . '</strong> ' . $withdrawal->note . '</p>';
        }

        $message .= '<p>' . __('The amount has been returned to your account balance.', 'vendorpro') . '</p>';
        $message .= $this->get_email_footer();

        $this->send_email($to, $subject, $message);
    }

    /**
     * Send email
     */
    private function send_email($to, $subject, $message)
    {
        $headers = array('Content-Type: text/html; charset=UTF-8');

        wp_mail($to, $subject, $message, $headers);
    }

    /**
     * Get email header
     */
    private function get_email_header()
    {
        $header = '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">';
        $header .= '<div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">';
        $header .= '<h1 style="margin: 0; color: #0071DC;">' . get_bloginfo('name') . '</h1>';
        $header .= '</div>';
        $header .= '<div style="background: #fff; padding: 20px; border-radius: 8px; border: 1px solid #e9ecef;">';

        return $header;
    }

    /**
     * Get email footer
     */
    private function get_email_footer()
    {
        $footer = '</div>';
        $footer .= '<div style="margin-top: 20px; padding: 20px; text-align: center; color: #6c757d; font-size: 12px;">';
        $footer .= '<p>&copy; ' . date('Y') . ' ' . get_bloginfo('name') . '. ' . __('All rights reserved.', 'vendorpro') . '</p>';
        $footer .= '<p><a href="' . home_url() . '" style="color: #0071DC;">' . home_url() . '</a></p>';
        $footer .= '</div>';
        $footer .= '</body></html>';

        return $footer;
    }

    /**
     * Get dashboard URL
     */
    private function get_dashboard_url()
    {
        $page_id = get_option('vendorpro_vendor_dashboard_page_id');
        return $page_id ? get_permalink($page_id) : home_url('/vendor-dashboard');
    }

    /**
     * Get store URL
     */
    private function get_store_url($slug)
    {
        return home_url('/store/' . $slug);
    }
}
