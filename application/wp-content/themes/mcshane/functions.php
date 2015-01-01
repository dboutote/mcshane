<?php 
/**
 * McShane Parent Functions
 *
 * This file contains helper functions that act as custom template tags. Others are attached to 
 * action and filter hooks in WordPress to change core functionality.
 * 
 * @link http://codex.wordpress.org/Template_Tags
 * @link http://codex.wordpress.org/Function_Reference/add_action
 * @link http://codex.wordpress.org/Function_Reference/add_filter
 *
 * When using a child theme you can override certain functions (those wrapped
 * in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before
 * the parent theme's file, so the child theme functions would be used.
 *
 * @link http://codex.wordpress.org/Theme_Development
 * @link http://codex.wordpress.org/Child_Themes 
 * 
 * @package WordPress
 * @subpackage McShane
 * @since McShane 1.0
 */


 
  
/**
 * Include some theme functions
 * 
 * @since McShane 1.0
 */
if ( ! class_exists( '\McShane\Theme_Functions' ) && 'plugins.php' !== $GLOBALS['pagenow'] ) {
	require get_template_directory() . '/inc/class_theme-functions.php';
}

show_admin_bar(false);

/**
 * Custom Image Sizes
 */
add_image_size( 'mcsh-gallery-thumb', 311, 233, true );


/**
 * Theme Setup
 *
 * Set up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support post thumbnails.
 *
 * @since McShane 1.0
 */
if ( ! function_exists( 'mcshane_theme_setup' ) ) 
{
	function mcshane_theme_setup()
	{
			
		add_post_type_support( 'page', 'excerpt' );
		
		// Add RSS feed links to <head> for posts and comments.
		add_theme_support( 'automatic-feed-links' );
		
		// Enable support for Post Thumbnails, and declare two sizes.
		add_theme_support( 'post-thumbnails' );
		#set_post_thumbnail_size( 672, 372, true );
		#add_image_size( 'archive-thumb', 260, 160, true );
		
		// This theme uses wp_nav_menu() in two locations.
		register_nav_menus(
			array(
				'primary'   => __( 'Top primary menu', 'mcshane' ),
				'secondary' => __( 'Secondary menu in header', 'mcshane' ),
				'quicklinks' => __( 'Quick links on home page', 'mcshane' ),
			) 
		);
				
		 // Switch default core markup for search form, comment form, and comments to output valid HTML5.
		add_theme_support( 'html5', array(
			'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
		) );		
		
		// This theme uses its own gallery styles.
		add_filter( 'use_default_gallery_style', '__return_false' );	
	}

};
add_action( 'after_setup_theme', 'mcshane_theme_setup' );


/**
 * A lightweight nav menu for sibling pages
 */
function show_siblings($post_id = null){
	$post_id = ( null === $post_id ) ? get_the_ID() : $post_id;

	if( in_the_loop() ) {
		global $post;
		$current_post = $post;
	} else {
		$current_post = get_post($post_id);		
	}

	if( !$current_post || is_null($current_post) || !$current_post->post_parent ) {
		return;
	}

	$siblings = get_pages(array(
		'sort_order' => 'ASC',
		'sort_column' => 'menu_order',
		'parent' => $current_post->post_parent,
	));


	if( empty($siblings) ){
		return;
	}

	$out = '<ul id="left-menu" class="sibling-list">';
	
	foreach( $siblings as $sib ){
		$link = get_permalink($sib->ID);		
		if( $sib->ID === $post_id ){
			$out .= '<li><a href="'.$link.'" class="active">' . $sib->post_title . '</a></li>';
		} else {
			$out .= '<li><a href="'.$link.'">' . $sib->post_title . '</a></li>';
		}
	}
	
	$out .= '</ul>';

	echo $out;
}


/**
 * Wrap breadcrumb items in <li> yags
 */
add_filter('breadcrumb_trail_items', 'mcsh_bc_items');
function mcsh_bc_items($items){
	foreach( $items as $k => $item){
		$items[$k] = '<li>'. $item .'</li>';
	}
	return $items;
}


/**
 * Strip the breadcrumb class from the breadcrumb trail
 */
add_filter('breadcrumb_trail', 'mcsh_bc_trail');
function mcsh_bc_trail($html){
	$html = str_replace('breadcrumb-trail breadcrumbs', 'breadcrumb-trail', $html);
	return $html;
}
