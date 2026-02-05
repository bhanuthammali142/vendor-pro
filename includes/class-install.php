<?php
/**
 * Installation and database setup
 */

if (!defined('ABSPATH')) {
    exit;
}

class VendorPro_Install
{

    /**
     * Install VendorPro
     */
    public static function activate()
    {
        self::create_tables();
        self::create_pages();
        self::create_roles();
        self::set_default_options();

        // Flush rewrite rules
        flush_rewrite_rules();

        // Set activation flag
        update_option('vendorpro_activated', time());
    }

    /**
     * Create custom database tables
     */
    private static function create_tables()
    {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $sql = array();

        // Vendors table
        $sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}vendorpro_vendors (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            store_name varchar(200) NOT NULL,
            store_slug varchar(200) NOT NULL,
            store_description text,
            store_logo varchar(255),
            store_banner varchar(255),
            phone varchar(50),
            email varchar(100),
            address text,
            city varchar(100),
            state varchar(100),
            country varchar(100),
            postcode varchar(20),
            commission_rate decimal(5,2) DEFAULT NULL,
            commission_type varchar(20) DEFAULT 'percentage',
            enabled tinyint(1) DEFAULT 1,
            featured tinyint(1) DEFAULT 0,
            status varchar(20) DEFAULT 'pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY store_slug (store_slug),
            KEY user_id (user_id),
            KEY status (status)
        ) $charset_collate;";

        // Commission table
        $sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}vendorpro_commissions (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            vendor_id bigint(20) NOT NULL,
            order_id bigint(20) NOT NULL,
            order_item_id bigint(20) NOT NULL,
            product_id bigint(20) NOT NULL,
            order_total decimal(20,4) NOT NULL,
            vendor_earning decimal(20,4) NOT NULL,
            admin_commission decimal(20,4) NOT NULL,
            commission_rate decimal(5,2) NOT NULL,
            commission_type varchar(20) NOT NULL,
            status varchar(20) DEFAULT 'unpaid',
            paid_date datetime DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY vendor_id (vendor_id),
            KEY order_id (order_id),
            KEY status (status)
        ) $charset_collate;";

        // Withdrawals table
        $sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}vendorpro_withdrawals (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            vendor_id bigint(20) NOT NULL,
            amount decimal(20,4) NOT NULL,
            method varchar(50) NOT NULL,
            payment_details text,
            note text,
            status varchar(20) DEFAULT 'pending',
            requested_at datetime DEFAULT CURRENT_TIMESTAMP,
            processed_at datetime DEFAULT NULL,
            processed_by bigint(20) DEFAULT NULL,
            ip_address varchar(100),
            PRIMARY KEY  (id),
            KEY vendor_id (vendor_id),
            KEY status (status)
        ) $charset_collate;";

        // Vendor balance table
        $sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}vendorpro_vendor_balance (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            vendor_id bigint(20) NOT NULL,
            debit decimal(20,4) DEFAULT 0,
            credit decimal(20,4) DEFAULT 0,
            balance decimal(20,4) DEFAULT 0,
            trn_id bigint(20) DEFAULT NULL,
            trn_type varchar(50) DEFAULT NULL,
            particulars varchar(255),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY vendor_id (vendor_id)
        ) $charset_collate;";

        // Vendor reviews table
        $sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}vendorpro_vendor_reviews (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            vendor_id bigint(20) NOT NULL,
            customer_id bigint(20) NOT NULL,
            order_id bigint(20) DEFAULT NULL,
            rating int(1) NOT NULL,
            review text,
            status varchar(20) DEFAULT 'approved',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY vendor_id (vendor_id),
            KEY customer_id (customer_id)
        ) $charset_collate;";

        // Vendor followers table
        $sql[] = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}vendorpro_vendor_followers (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            vendor_id bigint(20) NOT NULL,
            user_id bigint(20) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY vendor_user (vendor_id, user_id),
            KEY vendor_id (vendor_id),
            KEY user_id (user_id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        foreach ($sql as $query) {
            dbDelta($query);
        }

        update_option('vendorpro_db_version', VENDORPRO_VERSION);
    }

    /**
     * Create required pages
     */
    private static function create_pages()
    {
        $pages = array(
            'vendor_dashboard' => array(
                'title' => __('Vendor Dashboard', 'vendorpro'),
                'content' => '[vendorpro_dashboard]',
                'slug' => 'vendor-dashboard'
            ),
            'vendor_registration' => array(
                'title' => __('Become a Vendor', 'vendorpro'),
                'content' => '[vendorpro_vendor_registration]',
                'slug' => 'become-a-vendor'
            ),
            'vendors' => array(
                'title' => __('All Vendors', 'vendorpro'),
                'content' => '[vendorpro_vendors]',
                'slug' => 'vendors'
            )
        );

        foreach ($pages as $key => $page) {
            $page_id = get_option('vendorpro_' . $key . '_page_id');

            if (!$page_id || !get_post($page_id)) {
                $page_data = array(
                    'post_title' => $page['title'],
                    'post_content' => $page['content'],
                    'post_name' => $page['slug'],
                    'post_status' => 'publish',
                    'post_type' => 'page',
                    'post_author' => 1,
                    'comment_status' => 'closed'
                );

                $page_id = wp_insert_post($page_data);
                update_option('vendorpro_' . $key . '_page_id', $page_id);
            }
        }
    }

    /**
     * Create vendor role
     */
    private static function create_roles()
    {
        global $wp_roles;

        if (!class_exists('WP_Roles')) {
            return;
        }

        if (!isset($wp_roles)) {
            $wp_roles = new WP_Roles();
        }

        // Add vendor role
        add_role(
            'vendor',
            __('Vendor', 'vendorpro'),
            array(
                'read' => true,
                'edit_posts' => false,
                'delete_posts' => false,
                'upload_files' => true,
            )
        );

        // Add capabilities to vendor role
        $vendor_role = get_role('vendor');
        if ($vendor_role) {
            $capabilities = array(
                'read_product' => true,
                'edit_product' => true,
                'delete_product' => true,
                'edit_products' => true,
                'edit_published_products' => true,
                'publish_products' => true,
                'delete_published_products' => true,
                'edit_shop_orders' => true,
                'read_shop_orders' => true,
                'manage_product_terms' => true,
                'edit_product_terms' => true,
                'delete_product_terms' => true,
                'assign_product_terms' => true,
            );

            foreach ($capabilities as $cap => $grant) {
                $vendor_role->add_cap($cap, $grant);
            }
        }
    }

    /**
     * Set default options
     */
    private static function set_default_options()
    {
        $defaults = array(
            'vendorpro_commission_rate' => 10,
            'vendorpro_commission_type' => 'percentage',
            'vendorpro_vendor_registration' => 'yes',
            'vendorpro_vendor_approval' => 'yes',
            'vendorpro_min_withdraw_amount' => 50,
            'vendorpro_withdraw_methods' => array('paypal', 'bank'),
            'vendorpro_product_approval' => 'no',
            'vendorpro_vendor_per_page' => 12,
        );

        foreach ($defaults as $key => $value) {
            if (get_option($key) === false) {
                update_option($key, $value);
            }
        }
    }
}
