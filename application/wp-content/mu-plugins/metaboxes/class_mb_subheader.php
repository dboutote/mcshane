<?php

/**
 * PreFooter Metabox
 *
 * Adds an optional 'pre-footer' section available to all pages but the homepage.
 *
 */

class MetaBox_SubHeader {

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
		add_action( 'edit_form_after_title', array($this,'move_meta_box') );
	}
	
	
	/**
	 * This will move the meta box to under the Editor Title field 
	 */
	public static function move_meta_box()
	{
	
		global $wp_meta_boxes;
		#debug($wp_meta_boxes);
	
		#$post_type_check = 'cpt_guide';
		
		#if( $post_type_check === get_post_type() ){	
				
			global $post;			
						
			# Get the globals:
			global $post, $wp_meta_boxes;

			# Output the "advanced" meta boxes:
			do_meta_boxes(get_current_screen(), 'advancedtwo', $post);
			
			# Remove the initial "advanced" meta boxes:
			#unset($wp_meta_boxes[$post_type_check]['advanced']);
		
		#}
		
		return;
		
	}
	

	/**
	 * Configuration params for the Metabox
	 *
	 * @since 1.0
	 * @access protected
	 *
	 */
	protected function get_meta_box_args()
	{
		return $this->set_meta_box_args();
	}


	/**
	 * Configuration params for the Metabox
	 *
	 * @access protected
	 * @since 1.0
	 *
	 */
	protected function set_meta_box_args()
	{
		$basename = 'sub-header';
		$post_type_name = 'post';

		$post_types = get_post_types();
		$post_type = get_post_type();

		if( $post_type ){
			$post_type_name = strtolower( get_post_type_object( $post_type )->labels->singular_name );
		}

		$meta_fields = array(
			'sub_header' => array(
				'name' => 'sub_header',
				'type' => 'textarea',
				'default' => '',
				'title' => __('Sub Header'),
				'description' => sprintf( __( 'Enter an optional sub header for this %s.', 'rinsight' ), $post_type_name ),
			)
		);

		$args = array(
			'meta_box_id' => $basename . 'div',
			'meta_box_name' => $basename . 'info',
			'meta_box_title' => __( 'Sub Header' ),
			'meta_box_default' => '',
			'meta_box_description' => sprintf( __( 'Enter an optional sub header for this %s.', 'rinsight' ), $post_type_name ),
			'content_types' => $post_types,
			'meta_box_position' => 'advancedtwo',
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

		$args = $this->get_meta_box_args();
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

		if ( in_array( $post_type, apply_filters( 'include_subheader_dont_show_list', $this->dont_show_in, $post_type ) ) ){
			return false;
		}

		return true;
	}


	/**
	 * Print the inner HTML of the metabox
	 *
	 * @access public
	 * @since 1.0
	 *
	 */
	public function inner_metabox()
	{
	
	
		global $post;

		// get configuration args
		$args = $this->get_meta_box_args();
		extract($args);

		$output = '';
		
		$output .= '<style type="text/css" media="screen">#sub-headerdiv { margin-top: 24px; } #sub-headerdiv .handlediv, #sub-headerdiv .hndle {display:none;}</style>';
		
		foreach( $meta_fields as $meta_field ) {

			$meta_field_value = get_post_meta($post->ID, '_'.$meta_field['name'], true);

			if( '' === $meta_field_value ) {
				$meta_field_value = $meta_field['default'];
			}

			wp_nonce_field( plugin_basename(__CLASS__), $meta_field['name'].'_noncename' );

			if ( 'sub_header' === $meta_field['name']) {
				$output .= '<p><b><label for="'.$meta_field['name'].'">'.$meta_field['title'].'</label></b><br />';
				$output .= '<input class="reg-text" type="text" id="'.$meta_field['name'].'" name="'.$meta_field['name'].'" value="'.$meta_field_value.'" size="16" style="width: 98%; padding: 6px 8px; " /></p>';
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
		$args = $this->get_meta_box_args();

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
function has_subheader( $post_id = null ) {
	$post_id = ( null === $post_id ) ? get_the_ID() : $post_id;
	return (bool) get_post_meta( $post_id, '_sub_header', true );
}


function display_subheader( $post_id = null ){
	$post_id = ( null === $post_id ) ? get_the_ID() : $post_id;
	$content = get_post_meta( $post_id, '_sub_header', true );

	echo $content;
}

$MetaBox_SubHeader = new MetaBox_SubHeader();


?>