<?php
/*
  Plugin Name: WooCommerce Product Hover Carousel
  Plugin URI: http://milentijevic.com/product-hover-carousel/
  Version: 0.1.0
  Description: Adds a carousel with woocommerce gallerry on product archives that is revealed on hover.
  Author: Mladjo
  Author URI: http://milentijevic.com
  Text Domain: woocommerce-product-hover-carousel
  Domain Path: /languages/

  License: GNU General Public License v3.0
  License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

/**
 * Check if WooCommerce is active
 * */
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {

    /**
     * Localisation (with WPML support)
     * */
    add_action('init', 'plugin_init');

    function plugin_init() {
        load_plugin_textdomain('woocommerce-product-hover-carousel', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    /**
     * New Badge class
     * */
    if (!class_exists('WC_phc')) {

        class WC_phc {

            public function __construct() {
                add_action('wp_enqueue_scripts', array($this, 'phc_scripts'));              // Enqueue the styles
                add_action('woocommerce_before_shop_loop_item_title', array($this, 'woocommerce_template_loop_second_product_thumbnail'), 9);
            }

            /* ----------------------------------------------------------------------------------- */
            /* Class Functions */
            /* ----------------------------------------------------------------------------------- */

            // Setup styles
            function phc_scripts() {
                if (apply_filters('woocommerce_product_image_flipper_styles', true)) {
                    wp_enqueue_style('phc-styles', plugins_url('/assets/css/style.css', __FILE__));
                }
                wp_enqueue_script('phc-script', plugins_url('/assets/js/jquery.jcarousellite.js', __FILE__), array('jquery'));
                wp_enqueue_script('phc-init', plugins_url('/assets/js/init.js', __FILE__), array('jquery'), true);
            }

            /* ----------------------------------------------------------------------------------- */
            /* Frontend Functions */
            /* ----------------------------------------------------------------------------------- */

            // Display the carousel
            function woocommerce_template_loop_second_product_thumbnail() {
                global $post, $product, $woocommerce;

                $attachment_ids = $product->get_gallery_attachment_ids();


                if ($attachment_ids) {
                    ?>
                    <div class="thumbnails previewslider">
                        <ul><?php
                            
                            foreach ($attachment_ids as $attachment_id) {

                                $classes = array('zoom');


                                $image = wp_get_attachment_image($attachment_id, apply_filters('single_product_small_thumbnail_size', 'shop_thumbnail'));
                                list( $magnifier_url, $magnifier_width, $magnifier_height ) = wp_get_attachment_image_src($attachment_id, "shop_catalog");
                                $image_class = esc_attr(implode(' ', $classes));
                                $image_title = esc_attr(get_the_title($attachment_id));

                                echo apply_filters('woocommerce_single_product_image_thumbnail_html', sprintf('<li data-src="%s"> %s</li>', $magnifier_url, $image), $attachment_id, $post->ID, $image_class);

                            }
                            ?></ul>
                    </div>

                    <?php
                }
            }

        }

        $WC_phc = new WC_phc();
    }
}