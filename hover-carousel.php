<?php
/*
  Plugin Name: WooCommerce Product Hover Carousel
  Plugin URI: http://milentijevic.com/wordpress/plugins
  Version: 0.2.0
  Description: Adds a carousel with woocommerce gallerry on product archives that is revealed on hover.
  Author: Mladjo
  Author URI: http://milentijevic.com
  Text Domain: woocommerce-product-hover-carousel
  Domain Path: /languages/

  License: GNU General Public License v3.0
  License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
// If this file is called directly, abort.
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/**
 * Check if WooCommerce is active
 * */
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {

    /**
     * New class
     * */
    if (!class_exists('WC_PHC')) {

        class WC_PHC {

            public static function getInstance() {
                static $_instance;
                if (!$_instance) {
                    $_instance = new WC_PHC();
                }
                return $_instance;
            }

            public function __construct() {
                add_action('init', array(&$this, 'init'));
                add_action('plugins_loaded', array(&$this, 'load_localisation'));
                //Register image sizes
                add_action('after_setup_theme', array($this, 'phc_register_image_sizes'));
            }

            /**
             * Initialize the plugin.
             */
            function init() {
                // Settings
                add_filter('woocommerce_get_sections_products', __CLASS__ . '::add_products_section');
                add_filter('woocommerce_get_settings_products', __CLASS__ . '::all_settings', 10, 2);
                // Enqueue the styles
                add_action('wp_enqueue_scripts', array($this, 'phc_scripts'));
                add_action('woocommerce_after_shop_loop_item', array($this, 'woocommerce_template_loop_previewslider'), 1);
                // Add custom css within head section using wp_head action. 
                add_action('wp_head', array($this, 'phc_hook_css'));
            }

            /* ----------------------------------------------------------------------------------- */
            /* Class Functions */
            /* ----------------------------------------------------------------------------------- */

            /**
             * Create the section beneath the products tab
             * */
            public static function add_products_section($sections) {
                $sections['phc'] = __('Hover Carousel', 'phc');
                return $sections;
            }

            /**
             * Add settings to the section
             */
            public static function all_settings($settings, $current_section) {

                /**
                 * Check the current section is what we want
                 * */
                if ($current_section == 'phc') {

                    $settings_phc = array();

                    // Add Title to the Settings
                    $settings_phc[] = array(
                        'name' => __('Hover Carousel', 'phc'),
                        'type' => 'title',
                        'desc' => __('The following options are used to configure Hover Carousel', 'phc'),
                        'id' => 'phc'
                    );

                    $settings_phc[] = array(
                        'name' => __('Carousel Width', 'phc'),
                        'desc_tip' => __('The carousel width in pixels.', 'phc'),
                        'id' => 'phc_carousel_width',
                        'type' => 'number',
                        'default' => 92
                    );

                    $settings_phc[] = array(
                        'name' => __('Carousel Height', 'phc'),
                        'desc_tip' => __('The carousel height in pixels.', 'phc'),
                        'id' => 'phc_carousel_height',
                        'type' => 'number',
                        'default' => 256
                    );

                    $settings_phc[] = array(
                        'name' => __('Carousel Items Width', 'phc'),
                        'desc_tip' => __('The carousel thumbnails width in pixels.', 'phc'),
                        'id' => 'phc_thumb_width',
                        'type' => 'number',
                        'default' => 80
                    );

                    $settings_phc[] = array(
                        'name' => __('Carousel Items Height', 'phc'),
                        'desc_tip' => __('The carousel thumbnails height in pixels.', 'phc'),
                        'id' => 'phc_thumb_height',
                        'type' => 'number',
                        'default' => 80
                    );

                    $settings_phc[] = array(
                        'name' => __('Crop Mode', 'text-domain'),
                        'desc_tip' => __('Soft Mode resizing the image proportionally and Hard Mode cropping the image.<br>if you want to rebuild thumbnails please install AJAX Thumbnail Rebuild.', 'phc'),
                        'id' => 'phc_crop_mode',
                        'type' => 'select',
                        'default' => true,
                        'options' => array(
                            false => 'Soft',
                            true => 'Hard'
                        )
                    );
                    $settings_phc[] = array(
                        'title' => __('Custom CSS', 'phc'),
                        'type' => 'textarea',
                        'id' => 'phc_custom_css',
                        'css' => 'width:50%; height: 75px;',
                        'desc_tip' => __('Apply your own custom CSS. CSS is automatically wrapped with style tags', 'phc')
                    );
                    $settings_phc[] = array(
                        'type' => 'sectionend',
                        'id' => 'phc_end'
                    );
                    $settings_phc[] = array('type' => 'sectionend', 'id' => 'phc');

                    return $settings_phc;
                    /**
                     * If not, return the standard settings
                     * */
                } else {
                    return $settings;
                }
            }

            // Register_image_sizes()
            public static function phc_register_image_sizes() {
                $thumb_width = get_option('phc_thumb_width');
                $thumb_height = get_option('phc_thumb_height');
                $crop_mode = get_option('phc_crop_mode');

                add_image_size('phc-thumbnail', $thumb_width, $thumb_height, $crop_mode);
            }

            /**
             * load_localisation function.
             *
             * @access public
             * @since 0.2.0
             * @return void
             */
            public function load_localisation() {
                load_plugin_textdomain('phc', false, dirname(plugin_basename(__FILE__)) . '/languages');
            }

            // Setup styles
            public function phc_scripts() {
                if (is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() || is_product()) {
                    wp_enqueue_style('phc-styles', plugins_url('/assets/css/style.css', __FILE__));
                    wp_enqueue_script('phc-script', plugins_url('/assets/js/thumbelina.js', __FILE__));
                    wp_enqueue_script('phc-init', plugins_url('/assets/js/init.js', __FILE__), array('jquery'));
                }
            }

            // Add custom css within head section using wp_head action. 
            public function phc_hook_css() {
                ?>
                <style>
                    .previewslider {
                        width:<?php echo get_option('phc_carousel_width'); ?>px!important;
                        height:<?php echo get_option('phc_carousel_height'); ?>px!important;
                    }         
                    <?php echo get_option('phc_custom_css'); ?>
                </style>

                <?php
            }

            /**
             * Display the carousel on shop and archive pages
             * 
             * @access public
             * @return void
             * @since 0.1.0
             */
            public function woocommerce_template_loop_previewslider() {

                global $post, $product, $woocommerce;

                $attachment_ids = $product->get_gallery_attachment_ids();
                if (!empty($attachment_ids))
                    array_unshift($attachment_ids, get_post_thumbnail_id());
                $md5 = substr(md5(rand()), 0, 7);

                if ($attachment_ids) {
                    ?>
                    <div class="previewslider" id="previewslider-<?php echo $md5; ?>">
                        <div class="thumbelina-but vert top">&#708;</div>
                        <ul>
                            <?php
                            $loop = 0;
                            $columns = apply_filters('woocommerce_product_thumbnails_columns', 3);

                            foreach ($attachment_ids as $attachment_id) {

                                $classes = array('zoom');

                                if ($loop == 0 || $loop % $columns == 0)
                                    $classes[] = 'first';

                                if (( $loop + 1 ) % $columns == 0)
                                    $classes[] = 'last';

                                list($image_link) = wp_get_attachment_image_src($attachment_id, 'shop_catalog');

                                if (!$image_link)
                                    continue;

                                $image = wp_get_attachment_image($attachment_id, 'phc-thumbnail');
                                $image_class = esc_attr(implode(' ', $classes));
                                $image_title = esc_attr(get_the_title($attachment_id));

                                echo apply_filters('woocommerce_single_product_image_thumbnail_html', sprintf('<li><a data-href="%s" class="%s" title="%s">%s</a></li>', $image_link, $image_class, $image_title, $image), $attachment_id, $post->ID, $image_class);

                                $loop++;
                            }
                            ?>
                        </ul>
                        <div class="thumbelina-but vert bottom">&#709;</div>

                    </div>
                    <script>
                        jQuery('#previewslider-<?php echo $md5; ?>').Thumbelina({
                            orientation: 'vertical', // Use vertical mode (default horizontal).
                            $bwdBut: jQuery('#previewslider-<?php echo $md5; ?> .top'), // Selector to top button.
                            $fwdBut: jQuery('#previewslider-<?php echo $md5; ?> .bottom')   // Selector to bottom button.
                        });
                    </script>

                    <?php
                }
            }

        }

        WC_PHC::getInstance();
    }
}