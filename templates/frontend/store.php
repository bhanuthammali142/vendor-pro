<?php
/**
 * Vendor Store Template
 */

get_header();

$vendor_slug = get_query_var('vendor_store');
$vendor = VendorPro_Database::instance()->get_vendor_by_slug($vendor_slug);

if (!$vendor) {
    echo 'Vendor not found';
    get_footer();
    exit;
}

$user_data = get_userdata($vendor->user_id);
?>

<div class="vendorpro-store-wrap">

    <!-- Header -->
    <div class="vendorpro-store-header <?php echo get_option('vendorpro_store_header_template', 'default'); ?>">
        <div class="vendorpro-store-banner"
            style="background-image: url('<?php echo esc_url($vendor->store_banner); ?>');">
            <?php if (!$vendor->store_banner): ?>
                <div class="vendorpro-banner-placeholder"></div>
            <?php endif; ?>
        </div>

        <div class="vendorpro-store-info">
            <div class="vendorpro-store-logo">
                <img src="<?php echo esc_url($vendor->store_logo ? $vendor->store_logo : get_avatar_url($vendor->user_id)); ?>"
                    alt="<?php echo esc_attr($vendor->store_name); ?>">
            </div>
            <div class="vendorpro-store-details">
                <h1>
                    <?php echo esc_html($vendor->store_name); ?>
                </h1>

                <?php if (get_option('vendorpro_show_vendor_info', 'yes') === 'yes'): ?>
                    <p class="vendorpro-store-meta">
                        <?php if ($vendor->address)
                            echo esc_html($vendor->address) . '<br>'; ?>
                        <?php if ($vendor->phone)
                            echo esc_html($vendor->phone) . '<br>'; ?>
                        <?php if ($vendor->email)
                            echo '<a href="mailto:' . esc_attr($vendor->email) . '">' . esc_html($vendor->email) . '</a>'; ?>
                    </p>
                <?php endif; ?>

                <div class="vendorpro-store-rating">
                    <!-- Rating logic here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Content logic -->
    <div class="vendorpro-store-content">
        <div class="vendorpro-store-sidebar">
            <?php if (get_option('vendorpro_show_contact_form', 'yes') === 'yes'): ?>
                <div class="vendorpro-widget">
                    <h3>
                        <?php _e('Contact Vendor', 'vendorpro'); ?>
                    </h3>
                    <!-- Form placeholder -->
                    <form>
                        <input type="text" placeholder="Name" style="width:100%; margin-bottom:10px;">
                        <input type="email" placeholder="Email" style="width:100%; margin-bottom:10px;">
                        <textarea placeholder="Message" style="width:100%; margin-bottom:10px;"></textarea>
                        <button class="button">
                            <?php _e('Send Message', 'vendorpro'); ?>
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </div>

        <div class="vendorpro-store-products">
            <h2>
                <?php _e('Products', 'vendorpro'); ?>
            </h2>
            <?php
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            $products_per_page = intval(get_option('vendorpro_store_products_per_page', 12));

            $args = array(
                'post_type' => 'product',
                'post_status' => 'publish',
                'author' => $vendor->user_id,
                'posts_per_page' => $products_per_page,
                'paged' => $paged
            );

            $loop = new WP_Query($args);

            if ($loop->have_posts()) {
                echo '<ul class="products columns-4">';
                while ($loop->have_posts()):
                    $loop->the_post();
                    wc_get_template_part('content', 'product');
                endwhile;
                echo '</ul>';

                // Pagination
                echo paginate_links(array(
                    'total' => $loop->max_num_pages
                ));
            } else {
                echo '<p>' . __('No products found.', 'vendorpro') . '</p>';
            }

            wp_reset_postdata();
            ?>
        </div>
    </div>

</div>

<?php get_footer(); ?>