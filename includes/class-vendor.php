<?php
/**
 * Vendor class
 */

if (!defined('ABSPATH')) {
    exit;
}

class VendorPro_Vendor
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
        add_action('woocommerce_product_options_general_product_data', array($this, 'add_vendor_field_to_product'));
        add_action('woocommerce_process_product_meta', array($this, 'save_vendor_field'));
        add_filter('woocommerce_product_data_tabs', array($this, 'add_vendor_product_tab'));
    }

    /**
     * Check if user is vendor
     */
    public function is_vendor($user_id = null)
    {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }

        if (!$user_id) {
            return false;
        }

        $user = get_userdata($user_id);

        if (!$user) {
            return false;
        }

        return in_array('vendor', $user->roles);
    }

    /**
     * Get vendor info
     */
    public function get_vendor_info($vendor_id = null)
    {
        if (!$vendor_id) {
            $user_id = get_current_user_id();
            $vendor = VendorPro_Database::instance()->get_vendor_by_user($user_id);
        } else {
            $vendor = VendorPro_Database::instance()->get_vendor($vendor_id);
        }

        return $vendor;
    }

    /**
     * Create vendor
     */
    public function create_vendor($user_id, $data)
    {
        // Check if vendor already exists
        $existing = VendorPro_Database::instance()->get_vendor_by_user($user_id);

        if ($existing) {
            return new WP_Error('vendor_exists', __('Vendor already exists for this user.', 'vendorpro'));
        }

        // Generate unique store slug
        $store_slug = $this->generate_store_slug($data['store_name']);

        // Get commission settings
        $commission_rate = get_option('vendorpro_commission_rate', 10);
        $commission_type = get_option('vendorpro_commission_type', 'percentage');

        // Determine status based on approval setting
        $status = get_option('vendorpro_vendor_approval') === 'yes' ? 'pending' : 'approved';

        $vendor_data = array(
            'user_id' => $user_id,
            'store_name' => sanitize_text_field($data['store_name']),
            'store_slug' => $store_slug,
            'store_description' => isset($data['store_description']) ? wp_kses_post($data['store_description']) : '',
            'phone' => isset($data['phone']) ? sanitize_text_field($data['phone']) : '',
            'email' => isset($data['email']) ? sanitize_email($data['email']) : '',
            'address' => isset($data['address']) ? sanitize_textarea_field($data['address']) : '',
            'city' => isset($data['city']) ? sanitize_text_field($data['city']) : '',
            'state' => isset($data['state']) ? sanitize_text_field($data['state']) : '',
            'country' => isset($data['country']) ? sanitize_text_field($data['country']) : '',
            'postcode' => isset($data['postcode']) ? sanitize_text_field($data['postcode']) : '',
            'commission_rate' => $commission_rate,
            'commission_type' => $commission_type,
            'status' => $status,
            'enabled' => 1,
            'featured' => 0
        );

        $vendor_id = VendorPro_Database::instance()->insert_vendor($vendor_data);

        if ($vendor_id) {
            // Update user role
            $user = new WP_User($user_id);
            $user->set_role('vendor');

            // Send email notification
            do_action('vendorpro_vendor_created', $vendor_id, $user_id);

            return $vendor_id;
        }

        return new WP_Error('vendor_create_failed', __('Failed to create vendor.', 'vendorpro'));
    }

    /**
     * Generate unique store slug
     */
    private function generate_store_slug($store_name)
    {
        $slug = sanitize_title($store_name);
        $original_slug = $slug;
        $counter = 1;

        while ($this->slug_exists($slug)) {
            $slug = $original_slug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Check if slug exists
     */
    private function slug_exists($slug)
    {
        $vendor = VendorPro_Database::instance()->get_vendor_by_slug($slug);
        return !empty($vendor);
    }

    /**
     * Update vendor
     */
    public function update_vendor($vendor_id, $data)
    {
        $vendor_data = array();

        if (isset($data['store_name'])) {
            $vendor_data['store_name'] = sanitize_text_field($data['store_name']);
        }

        if (isset($data['store_description'])) {
            $vendor_data['store_description'] = wp_kses_post($data['store_description']);
        }

        if (isset($data['phone'])) {
            $vendor_data['phone'] = sanitize_text_field($data['phone']);
        }

        if (isset($data['email'])) {
            $vendor_data['email'] = sanitize_email($data['email']);
        }

        if (isset($data['address'])) {
            $vendor_data['address'] = sanitize_textarea_field($data['address']);
        }

        if (isset($data['city'])) {
            $vendor_data['city'] = sanitize_text_field($data['city']);
        }

        if (isset($data['state'])) {
            $vendor_data['state'] = sanitize_text_field($data['state']);
        }

        if (isset($data['country'])) {
            $vendor_data['country'] = sanitize_text_field($data['country']);
        }

        if (isset($data['postcode'])) {
            $vendor_data['postcode'] = sanitize_text_field($data['postcode']);
        }

        if (isset($data['commission_rate'])) {
            $vendor_data['commission_rate'] = floatval($data['commission_rate']);
        }

        if (isset($data['commission_type'])) {
            $vendor_data['commission_type'] = sanitize_text_field($data['commission_type']);
        }

        if (isset($data['status'])) {
            $vendor_data['status'] = sanitize_text_field($data['status']);
        }

        if (isset($data['enabled'])) {
            $vendor_data['enabled'] = intval($data['enabled']);
        }

        if (isset($data['featured'])) {
            $vendor_data['featured'] = intval($data['featured']);
        }

        $result = VendorPro_Database::instance()->update_vendor($vendor_id, $vendor_data);

        if ($result !== false) {
            do_action('vendorpro_vendor_updated', $vendor_id, $vendor_data);
            return true;
        }

        return false;
    }

    /**
     * Get vendor products
     */
    public function get_vendor_products($vendor_id, $args = array())
    {
        $vendor = VendorPro_Database::instance()->get_vendor($vendor_id);

        if (!$vendor) {
            return array();
        }

        $defaults = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'author' => $vendor->user_id,
            'post_status' => 'any'
        );

        $args = wp_parse_args($args, $defaults);

        $products = get_posts($args);

        return $products;
    }

    /**
     * Get vendor orders
     */
    public function get_vendor_orders($vendor_id, $args = array())
    {
        $vendor = VendorPro_Database::instance()->get_vendor($vendor_id);

        if (!$vendor) {
            return array();
        }

        // Get all products by vendor
        $product_ids = $this->get_vendor_product_ids($vendor->user_id);

        if (empty($product_ids)) {
            return array();
        }

        $defaults = array(
            'limit' => -1,
            'return' => 'ids'
        );

        $args = wp_parse_args($args, $defaults);

        // Get orders containing vendor products
        $orders = wc_get_orders($args);
        $vendor_orders = array();

        foreach ($orders as $order_id) {
            $order = wc_get_order($order_id);

            if (!$order) {
                continue;
            }

            foreach ($order->get_items() as $item) {
                $product_id = $item->get_product_id();

                if (in_array($product_id, $product_ids)) {
                    $vendor_orders[] = $order;
                    break;
                }
            }
        }

        return $vendor_orders;
    }

    /**
     * Get vendor product IDs
     */
    private function get_vendor_product_ids($user_id)
    {
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'author' => $user_id,
            'post_status' => 'any',
            'fields' => 'ids'
        );

        return get_posts($args);
    }

    /**
     * Add vendor field to product
     */
    public function add_vendor_field_to_product()
    {
        global $post;

        $vendor_id = get_post_meta($post->ID, '_vendor_id', true);

        echo '<div class="options_group">';

        woocommerce_wp_text_input(array(
            'id' => '_vendor_id',
            'label' => __('Vendor ID', 'vendorpro'),
            'desc_tip' => true,
            'description' => __('Enter the vendor ID for this product.', 'vendorpro'),
            'value' => $vendor_id
        ));

        echo '</div>';
    }

    /**
     * Save vendor field
     */
    public function save_vendor_field($post_id)
    {
        if (isset($_POST['_vendor_id'])) {
            update_post_meta($post_id, '_vendor_id', sanitize_text_field($_POST['_vendor_id']));
        }
    }

    /**
     * Add vendor product tab
     */
    public function add_vendor_product_tab($tabs)
    {
        // This can be extended for vendor-specific product settings
        return $tabs;
    }

    /**
     * Get vendor stats
     */
    public function get_vendor_stats($vendor_id)
    {
        $db = VendorPro_Database::instance();

        $stats = array(
            'total_products' => 0,
            'total_orders' => 0,
            'total_sales' => 0,
            'total_earnings' => 0,
            'pending_earnings' => 0,
            'withdrawn' => 0,
            'balance' => 0
        );

        // Get products count
        $products = $this->get_vendor_products($vendor_id);
        $stats['total_products'] = count($products);

        // Get orders count
        $orders = $this->get_vendor_orders($vendor_id);
        $stats['total_orders'] = count($orders);

        // Get earnings
        $stats['total_earnings'] = $db->get_vendor_earnings($vendor_id, 'paid');
        $stats['pending_earnings'] = $db->get_vendor_earnings($vendor_id, 'unpaid');
        $stats['withdrawn'] = $db->get_vendor_withdrawn_amount($vendor_id);
        $stats['balance'] = $db->get_vendor_balance($vendor_id);

        return $stats;
    }
}
