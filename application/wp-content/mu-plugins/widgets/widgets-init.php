<?php


add_action( 'widgets_init', 'mcsh_widgets_init');


/**
 * Instantiate our widgets
 *
 * @package WordPress
 * @subpackage McShane
 * @since McShane 1.0
 */
function mcsh_widgets_init(){
	
	// Featured Content Widget
	include_once( dirname( __FILE__ ) . '/widget-feat-content/widget-featured-content.php' );
	register_widget( 'Widget_Featured_Content' );
	
}

