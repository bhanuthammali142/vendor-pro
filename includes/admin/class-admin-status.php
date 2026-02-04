<?php
/**
 * Admin Status Page
 */

if (!defined('ABSPATH')) {
    exit;
}

class VendorPro_Admin_Status
{
    /**
     * Render page
     */
    public static function render_page()
    {
        include VENDORPRO_TEMPLATES_DIR . 'admin/status.php';
    }
}
