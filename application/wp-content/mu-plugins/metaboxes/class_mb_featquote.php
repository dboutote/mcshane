<?php

/**
 * Sub-Header Metabox
 *
 * Adds an optional sub header field to the Editor screen
 *
 */
class MetaBox_FeatQuote {

	private $meta_config_args;
	private $dont_show_in = array();

	/**
	 * The constructor
	 *
	 * @access  public
	 * @since   1.0
	 * @return  void
 	 */
	public function __construct()
	{
		add_action( 'add_meta_boxes', array($this,'create_metabox') );
		add_action( 'save_post',      array($this,'save_meta'), 0, 3 );
	}
	

	/**
	 * Configuration params for the Metabox
	 *
	 * @since 1.0
	 * @access protected
	 *
	 */
	protected static function _get_meta_box_args()
	{
		return self::_set_meta_box_args();
	}


	/**
	 * Configuration params for the Metabox
	 *
	 * @access protected
	 * @since 1.0
	 *
	 */
	protected static function _set_meta_box_args()
	{
		$basename = 'feat-quote';
		$post_type_name = 'post';

		$post_types = get_post_types();
		$post_type = get_post_type();

		if( $post_type ){
			$post_type_name =  get_post_type_object( $post_type )->labels->singular_name;
			$post_type_name_lower = strtolower($post_type_name);
		}

		$meta_fields = array(
			'feat_quote' => array(
				'name' => 'feat_quote',
				'type' => 'textarea',
				'default' => '',
				'title' => __('Featured Quote'),
				'description' => sprintf( __( 'Enter an optional testimonial/quote for this %s.', 'mcshane' ), $post_type_name_lower ),
			),
			'feat_quote_author' => array(
				'name' => 'feat_quote_author',
				'type' => 'text',
				'default' => '',
				'title' => __('Quote Citation'),
				'description' => __( 'Enter the name of the quote author.', 'mcshane' ),
			),
			'feat_quote_author_title' => array(
				'name' => 'feat_quote_author_title',
				'type' => 'text',
				'default' => '',
				'title' => __('Quote Author Title'),
				'description' => __( 'Enter the quote author&#8217;s title.', 'mcshane' ),
			)
		);

		$args = array(
			'meta_box_id' => $basename . 'div',
			'meta_box_name' => $basename . 'info',
			'meta_box_title' => __( 'Featured Quote' ),
			'meta_box_default' => '',
			'meta_box_description' => sprintf( __( 'Enter an optional testimonial/quote for this %s.', 'mcshane' ), $post_type_name_lower ),
			'content_types' => $post_types,
			'meta_box_position' => 'side',
			'meta_box_priority' => 'high',
			'meta_fields' => $meta_fields
		);

		return $args;
	}


	/**
	 * Create the metabox
	 *
	 * @access public
	 * @since 1.0
	 *
	 * @uses add_meta_box()
	 */
	public function create_metabox()
	{
	
		if( false === $this->show_in_posttype(get_post_type()) ){
			return;
		};

		$args = $this->_get_meta_box_args();
		extract($args);

		if ( function_exists('add_meta_box') ) {
			foreach ($content_types as $content_type) {
				add_meta_box($meta_box_id, $meta_box_title, array($this, 'inner_metabox'), $content_type, $meta_box_position );
			}
		}
	}


	/**
	 * Determine if the current post type should show this meta box
	 *
	 * @access public
	 * @since 1.0
	 *
	 */
	protected function show_in_posttype( $post_type )
	{
		if( !$post_type || '' === $post_type ){
			return false;
		}

		if ( in_array( $post_type, apply_filters( 'include_featquote_dont_show_list', $this->dont_show_in, $post_type ) ) ){
			return false;
		}

		return true;
	}


	/**
	 * Print the inner HTML of the metabox
	 *
	 * @access public
	 * @since 1.0
	 */
	public static function inner_metabox()
	{
		global $post;

		// get configuration args
		$args = self::_get_meta_box_args();
		extract($args);

		$output = '';

		foreach( $meta_fields as $meta_field ) {

			$meta_field_value = get_post_meta($post->ID, '_'.$meta_field['name'], true);

			if( '' === $meta_field_value ) {
				$meta_field_value = $meta_field['default'];
			}

			wp_nonce_field( plugin_basename(__CLASS__), $meta_field['name'].'_noncename' );
			
			if ( 'feat_quote' === $meta_field['name']) {
				$output .= '<p><label for="'.$meta_field['name'].'">'.$meta_field['description'].'</label><br />';
				$output .= '<textarea class="wp-editor-area" id="'.$meta_field['name'].'" name="'.$meta_field['name'].'" cols="10" rows="6" style="border:1px solid #ddd; width: 99%;">'.$meta_field_value.'</textarea></p>';
			}			

			if ( 'feat_quote_author' === $meta_field['name']) {
				$output .= '<p><b>'.$meta_field['title'].'</b></p>';
				$output .= '<p><label for="'.$meta_field['name'].'">'.$meta_field['description'].'</label><br />';
				$output .= '<input class="reg-text" type="text" id="'.$meta_field['name'].'" name="'.$meta_field['name'].'" value="'.$meta_field_value.'" size="16" style="width: 99%;" /></p>';
			}	

			if ( 'feat_quote_author_title' === $meta_field['name']) {
				$output .= '<p><label for="'.$meta_field['name'].'">'.$meta_field['description'].'</label><br />';
				$output .= '<input class="reg-text" type="text" id="'.$meta_field['name'].'" name="'.$meta_field['name'].'" value="'.$meta_field_value.'" size="16" style="width: 99%;" /></p>';
			}			

		}

		echo $output;

		return;

	}


	/**
	 * Process saving the metadata
	 *
	 * @access public
	 * @since 1.0
	 *
	 */
	 public function save_meta($post_id, $post, $update)
	 {

		// if there's no $post object it's a new post
		if( !$post && $post_id > 0 ) {
			$post = get_post($post_id);
		}

		if(!$post) {
			return $post_id;
		}

		if( false === $this->show_in_posttype( $post->post_type ) ){
			return $post_id;
		};

		if( 'auto-draft' === $post->post_status ){
			return $post_id;
		}

		// skip auto-running jobs
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
		if ( defined('DOING_AJAX') && DOING_AJAX ) return;
		if ( defined('DOING_CRON') && DOING_CRON ) return;

		// Don't save if the post is only an auto-revision.
		if ( 'revision' == $post->post_type ) {
			return $post_id;
		}

		// Get the post type object & check if the current user has permission to edit the entry.
		$post_type = get_post_type_object( $post->post_type );

		if ( $post_type && !current_user_can( $post_type->cap->edit_post, $post_id ) ) {
			return $post_id;
		}

		// get configuration args
		$args = $this->_get_meta_box_args();

		extract($args);

		foreach($meta_fields as $meta_field) {

			// verify this came from the our screen and with proper authorization, (b/c save_post can be triggered at other times)
			if( !isset($_POST[$meta_field['name'].'_noncename']) || !wp_verify_nonce( $_POST[$meta_field['name'].'_noncename'], __CLASS__ ) ) {
				return $post_id;
			}

			// Ok, we're authenticated: we need to find and save the data
			$data = ( isset($_POST[$meta_field['name']]) ) ? $_POST[$meta_field['name']] : '';
			$data = ( is_array($data) ) ? array_filter($data) : trim($data);

			if ( '' != $data && '-1' != $data  ) {
				update_post_meta( $post->ID, '_'.$meta_field['name'], $data );
			} else {
				delete_post_meta( $post->ID, '_'.$meta_field['name'] );
			}

		}

		return $post_id;

	 }

}






/**
 * Check if post has Sub Header
 *
 * @since 1.0
 *
 * @param int $post_id Optional. Post ID.
 * @return bool
 */

function has_featquote( $post_id = null ) {
	$post_id = ( null === $post_id ) ? get_the_ID() : $post_id;
	return (bool) get_post_meta( $post_id, '_feat_quote', true );
}

/*
function display_featquote( $post_id = null ){
	$post_id = ( null === $post_id ) ? get_the_ID() : $post_id;
	$content = get_post_meta( $post_id, '_sub_header', true );

	echo $content;
}

*/

$MetaBox_FeatQuote = new MetaBox_FeatQuote();