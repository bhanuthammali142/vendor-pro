<?php
// AJAX Handler
if (!defined('ABSPATH')) exit;
class VendorPro_Ajax_Handler {
    protected static $_instance = null;
    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    public function __construct() {
        // AJAX handlers will be registered here
    }
}
