<?php
// Frontend Controller
if (!defined('ABSPATH')) exit;
class VendorPro_Frontend {
    protected static $_instance = null;
    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }
    public function enqueue_scripts() {
        wp_enqueue_style('vendorpro', VENDORPRO_ASSETS_URL . 'css/frontend.css', array(), VENDORPRO_VERSION);
        wp_enqueue_script('vendorpro', VENDORPRO_ASSETS_URL . 'js/frontend.js', array('jquery'), VENDORPRO_VERSION, true);
    }
}
