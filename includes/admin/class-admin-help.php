<?php
/**
 * Admin Help Page
 */

if (!defined('ABSPATH')) {
    exit;
}

class VendorPro_Admin_Help
{
    /**
     * Render page
     */
    public static function render_page()
    {
        include VENDORPRO_TEMPLATES_DIR . 'admin/help.php';
    }
}
