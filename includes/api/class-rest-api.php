<?php
// REST API Handler
if (!defined('ABSPATH')) exit;
class VendorPro_REST_API {
    protected static $_instance = null;
    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    public function __construct() {
        add_action('rest_api_init', array($this, 'register_routes'));
    }
    public function register_routes() {
        // REST API routes will be registered here
    }
}
