<?php

/**
 * Master functions for the site
 *
 * This file contains helper functions that act as custom template tags. Others are attached to 
 * action and filter hooks in WordPress to change core functionality.
 * 
 * @link http://codex.wordpress.org/Template_Tags
 * @link http://codex.wordpress.org/Function_Reference/add_action
 * @link http://codex.wordpress.org/Function_Reference/add_filter 
 * 
 * @package WordPress
 * @subpackage McShane
 * @since McShane 1.0
 */

 
/**
 * Utility function for debugging variables
 */
if ( ! function_exists( 'debug' ) ) {
	function debug($var){
		echo "\n<pre style=\"text-align:left;\">";
		if( is_array($var) || is_object($var)){
			print_r($var);
		} else {
			var_dump($var);
		}
		echo "</pre>\n";
	}
}



/**
 * Utility function to chunk a string at XX characters
 */
if ( ! function_exists( 'abbreviate' ) ) {
	function abbreviate($text, $max = '95') {
		if (strlen($text)<=$max) {
			return $text;
		}
		return substr($text, 0, $max-3) . '&#8230;';
	}
}


/**
 * Allow all post-types for post_tag taxonomy queries
 *
 * Without this, WP will only look for post_tag taxonomies on the "post" post-type *
 */
function post_type_tags_fix($request) {
    if ( isset($request['tag']) && !isset($request['post_type']) ) {
		$request['post_type'] = 'any';
	}

    return $request;
};
add_filter('request', 'post_type_tags_fix');



/**
 * allow all post-types for category taxonomy queries
 *
 * Without this, WP will only look for category taxonomies on the "post" post-type
 */
function post_type_category_fix($request) {
    if ( isset($request['category_name']) && !isset($request['post_type']) ) {
		$request['post_type'] = 'any';
	}

    return $request;
};
add_filter('request', 'post_type_category_fix');



/**
 * Remove the html filtering from Term descriptions
 */
remove_filter( 'pre_term_description', 'wp_filter_kses' );
remove_filter( 'term_description', 'wp_kses_data' );



/**
 * Add help tab displaying the Post ID to the Editor Screen
 */
function network_admin_add_help_tab() {
	global $pagenow, $post;

	if ('post.php' == $pagenow  && (isset($_GET['action'])&& 'edit' == $_GET['action']) ) {
		$postid = ($post && '' != $post->ID ) ? $post->ID : $_GET['post'];
		$post_id_text = '<p>' . __('Your post id is: <strong>' . $postid . '</strong>  <br /><br />This number should match the number in the URL above where it says &#8220;<code>post=###</code>&#8221;.') . '</p>';
		get_current_screen()->add_help_tab( array(
			'id'      => 'your-post-id',
			'title'   => __('Post ID'),
			'content' => $post_id_text,
		) );
	}
}
add_action('load-post.php', 'network_admin_add_help_tab');



/**
 * Load a template file on singe-post-type page (if applicable)
 *
 * Works for all post-types
 */
function show_me_the_template($template) {
	if( !is_archive() ) {
		$id = get_queried_object_id();
		$template_name = get_post_meta($id, '_wp_page_template', true);
		$new_template = locate_template($template_name);

		if('' != $new_template)
			$template = $new_template;
	}
    return $template;
}
#add_filter( 'template_include', 'show_me_the_template' );



/**
 * Remove the inline style for the Recent Comments Widget
 */
add_filter( 'show_recent_comments_widget_style', '__return_false' );


/**
 * Hide certain links from edit.php
 */
function mcsh_remove_row_actions( $actions ) {
	
	#if( isset($actions['view']) ) {
		#unset( $actions['view'] );
	#}
	if( isset($actions['trash']) ) {
		unset( $actions['trash'] );
	}
	if( isset($actions['inline hide-if-no-js']) ) {
		unset( $actions['inline hide-if-no-js'] );
	}		
	
	return $actions;
}
add_action('post_row_actions', 'mcsh_remove_row_actions', 10, 1 );
add_action('page_row_actions', 'mcsh_remove_row_actions', 10, 1 );

/**
 * Don't show the Featured Quote Metabox on Page post types
 */
function mcsh_not_feat_quote($dont_show, $post_type){
	$dont_show[] = 'page';
	return $dont_show;
}

add_filter('include_featquote_dont_show_list', 'mcsh_not_feat_quote', 0, 2 );