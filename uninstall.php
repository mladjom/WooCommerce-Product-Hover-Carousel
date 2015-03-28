<?php

/**
 * Uninstalling plugin options.
 */
// If uninstall is not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}

// Delete all the Plugin Options 
delete_option('phc_carousel_width');
delete_option('phc_carousel_height');
delete_option('phc_thumb_width');
delete_option('phc_thumb_height');
delete_option('phc_crop_mode');
delete_option('phc_custom_css');
