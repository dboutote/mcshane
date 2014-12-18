<?php

/**
 * Plugin Name: Master Configurations
 * Description: Must-have functions and settings for the site
 * Version: 1.0
 * License: GPL
 * Author Darrin Boutote
 */

// define some constants to be used by other must-use plugins
define('MU_DIR', dirname( __FILE__ ) . '/');		// defines a path to the root mu-plugins directory
define('MU_URL', plugin_dir_url( __FILE__ ) );		// defines a url path to the root mu-plugins directory
define('MU_CSS_URL', MU_URL . 'css');				// defines a url path  to a css directory in the mu-plugins directory
define('MU_JS_URL', MU_URL . 'js');					// defines a url path  to a js directory in the mu-plugins directory
define('MU_LIB_DIR', MU_DIR . 'lib');				// defines a url path  to a library directory in the mu-plugins directory
define('MU_MEDIA_URL', MU_URL . 'media');			// defines a url path  to a media directory in the mu-plugins directory
define('MU_CPT_DIR', MU_DIR . 'custom-post-types');	// defines a url path  to the custom post-types directory in the mu-plugins directory
define('MU_TAX_DIR', MU_DIR . 'taxonomies');		// defines a url path  to the custom taxonomies directory in the mu-plugins directory
define('MU_METABOX_DIR', MU_DIR . 'metaboxes');		// defines a url path  to the metabox directory in the mu-plugins directory
define('MU_WIDGETS_DIR', MU_DIR . 'widgets');		// defines a url path  to the widgets directory in the mu-plugins directory
define('MU_INC_DIR', MU_DIR . 'inc');		        // defines a url path  to the includes directory in the mu-plugins directory




/**
 * File Loader
 */
function get_files($dir_path) {
	$base = trailingslashit($dir_path);
	if (!is_dir($base)) {
		return;
	}

	 if ($dh = opendir($base)) {
		while (($file=readdir($dh)) !== false) {
			if ($file === '.' || $file === '..' || preg_match("/\/\./", $base.'/'.$file)) {
				continue;
			}
			include( $base . $file );
		}
	 }
}

// include needed functions
#include(MU_INC_DIR . '/mstr-functions.php');

// include any custom widgets
include( MU_WIDGETS_DIR . '/widgets-init.php');

// include any custom meta boxes
include( MU_METABOX_DIR . '/meta-init.php');

// include any custom post types
include( MU_CPT_DIR . '/cpt-init.php');



/**
 * Removes Adjacent post links from the header
 */
remove_action('wp_head', 'start_post_rel_link', 10, 0 ); 
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);

//move wpautop filter to AFTER shortcode is processed
remove_filter( 'the_content', 'wpautop' );
#add_filter( 'the_content', 'wpautop' , 999);
#add_filter( 'the_content', 'shortcode_unautop',9999 );
















