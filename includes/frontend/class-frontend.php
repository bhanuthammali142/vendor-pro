<?php
// Frontend Controller
if (!defined('ABSPATH'))
    exit;
class VendorPro_Frontend
{
    protected static $_instance = null;
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    public function __construct()
    {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

        // Rewrite Rules
        add_action('init', array($this, 'register_rewrite_rules'));
        add_filter('query_vars', array($this, 'register_query_vars'));
        add_filter('template_include', array($this, 'template_loader'));

        // WooCommerce Product Tabs
        add_filter('woocommerce_product_tabs', array($this, 'add_product_tabs'));
    }

    public function enqueue_scripts()
    {
        wp_enqueue_style('vendorpro', VENDORPRO_ASSETS_URL . 'css/frontend.css', array(), VENDORPRO_VERSION);
        wp_enqueue_script('vendorpro', VENDORPRO_ASSETS_URL . 'js/frontend.js', array('jquery'), VENDORPRO_VERSION, true);
    }

    /**
     * Add Product Tabs
     */
    public function add_product_tabs($tabs)
    {
        $product_id = get_the_ID();
        $vendor_id = get_post_field('post_author', $product_id);

        // Vendor Info Tab
        if (get_option('vendorpro_show_vendor_info', 'yes') === 'yes') {
            $tabs['vendor_info'] = array(
                'title' => __('Vendor Info', 'vendorpro'),
                'priority' => 20,
                'callback' => array($this, 'render_vendor_info_tab')
            );
        }

        // More Products Tab
        if (get_option('vendorpro_enable_more_products_tab', 'yes') === 'yes') {
            $tabs['more_products'] = array(
                'title' => __('More Products', 'vendorpro'),
                'priority' => 30,
                'callback' => array($this, 'render_more_products_tab')
            );
        }

        return $tabs;
    }

    /**
     * Render Vendor Info Tab
     */
    public function render_vendor_info_tab()
    {
        $product_id = get_the_ID();
        $vendor_id = get_post_field('post_author', $product_id);

        // Get Vendor Data
        $vendor = VendorPro_Database::instance()->get_vendor_by_user($vendor_id);

        if (!$vendor || $vendor->status !== 'approved') {
            return;
        }

        ?>
        <div class="vendorpro-tab-vendor-info">
            <div class="vendorpro-vendor-header">
                <img src="<?php echo esc_url($vendor->store_logo ? $vendor->store_logo : get_avatar_url($vendor_id)); ?>"
                    alt="<?php echo esc_attr($vendor->store_name); ?>" class="vendor-logo">
                <div class="vendor-details">
                    <h3><a
                            href="<?php echo esc_url(get_home_url() . '/' . get_option('vendorpro_store_url_slug', 'store') . '/' . $vendor->store_slug); ?>"><?php echo esc_html($vendor->store_name); ?></a>
                    </h3>

                    <div class="vendor-meta">
                        <?php if ($vendor->city || $vendor->country): ?>
                            <p class="vendor-location">
                                <span class="dashicons dashicons-location"></span>
                                <?php echo esc_html(implode(', ', array_filter(array($vendor->city, $vendor->country)))); ?>
                            </p>
                        <?php endif; ?>

                        <p class="vendor-rating">
                            <span class="dashicons dashicons-star-filled"></span>
                            <?php _e('5.0 (New)', 'vendorpro'); // Dynamic rating TODO ?>
                        </p>
                    </div>
                </div>
            </div>

            <?php if ($vendor->store_description): ?>
                <div class="vendor-description">
                    <?php echo wpautop(esc_html($vendor->store_description)); ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Render More Products Tab
     */
    public function render_more_products_tab()
    {
        $product_id = get_the_ID();
        $vendor_id = get_post_field('post_author', $product_id);

        $args = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => 4,
            'author' => $vendor_id,
            'post__not_in' => array($product_id)
        );

        $loop = new WP_Query($args);

        if ($loop->have_posts()) {
            echo '<div class="vendorpro-more-products">';
            echo '<ul class="products columns-4">';
            while ($loop->have_posts()):
                $loop->the_post();
                wc_get_template_part('content', 'product');
            endwhile;
            echo '</ul>';
            echo '</div>';
        } else {
            echo '<p>' . __('No other products found.', 'vendorpro') . '</p>';
        }

        wp_reset_postdata();
    }

    /**
     * Register Rewrite Rules
     */
    public function register_rewrite_rules()
    {
        $slug = get_option('vendorpro_store_url_slug', 'store');
        add_rewrite_rule(
            '^' . $slug . '/([^/]+)/?',
            'index.php?vendor_store=$matches[1]',
            'top'
        );

        // Flush if rules changed (simple check)
        if (get_option('vendorpro_flush_rewrite_rules')) {
            flush_rewrite_rules();
            delete_option('vendorpro_flush_rewrite_rules');
        }
    }

    /**
     * Query Vars
     */
    public function register_query_vars($vars)
    {
        $vars[] = 'vendor_store';
        return $vars;
    }

    /**
     * Template Loader
     */
    public function template_loader($template)
    {
        $vendor_slug = get_query_var('vendor_store');

        if ($vendor_slug) {
            // Check if vendor exists
            $vendor = VendorPro_Database::instance()->get_vendor_by_slug($vendor_slug);

            if ($vendor && $vendor->status === 'approved') {
                // Locate template
                $new_template = locate_template(array('vendorpro/store.php'));

                if (!$new_template) {
                    $new_template = VENDORPRO_TEMPLATES_DIR . 'frontend/store.php';
                }

                if (file_exists($new_template)) {
                    return $new_template;
                }
            } else {
                global $wp_query;
                $wp_query->set_404();
                status_header(404);
            }
        }

        return $template;
    }
}
