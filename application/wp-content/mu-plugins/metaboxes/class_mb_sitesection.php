<?php

/**
 * MetaBox_SiteSection Meta Box Class
 *
 * Adds a required dropdown on all non-Page post types to indicate where in the site hierarchy the content should appear.
 * Used for breadcrumb and side navigation.
 *
 */
class MetaBox_SiteSection {

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
	 * Get a list of all top-level pages
	 *
	 * @access  protected
	 * @since   1.0
	 * @return  array $pages 
 	 */
	protected static function _get_pages()
	{
		global $wpdb;
		$pages = $wpdb->get_results(
			$wpdb->prepare(
				"
				SELECT ID, post_title
				FROM $wpdb->posts
				WHERE post_status = 'publish'
				AND post_type = %s
				AND post_parent = %d
				ORDER BY menu_order ASC
				",
				'page',
				0
			)
		);

		return $pages;
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
		$basename = 'sitesection';
		$post_type_name = 'post';
		$post_type_name_lower = '';

		$post_types = get_post_types();
		$post_type = get_post_type();

		if( $post_type ){
			$post_type_name =  get_post_type_object( $post_type )->labels->singular_name;
			$post_type_name_lower = strtolower($post_type_name);
		}

		$meta_fields = array(
			'site_section' => array(
				'name' => 'site_section',
				'type' => 'select',
				'default' => '',
				'title' => __('Site Section'),
				'description' => sprintf( __( 'Select the Site Section for this %s. <em>(Used for site navigation.)</em>', 'mcshane' ), $post_type_name_lower ),
			),
		);

		$args = array(
			'meta_box_id' => $basename . 'div',
			'meta_box_name' => $basename . 'info',
			'meta_box_title' => __( 'Site Section' ),
			'meta_box_default' => '',
			'meta_box_description' => sprintf( __( 'Select the Site Section for this %s.', 'mcshane' ), $post_type_name_lower ),
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

		if( 'page' === get_post_type() ){
			return;
		}

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

		if ( in_array( $post_type, apply_filters( 'include_sitesection_dont_show_list', $this->dont_show_in, $post_type ) ) ){
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
			
			if ( 'site_section' === $meta_field['name']) {
				$pages = self::_get_pages();				
				$options = '<option value="-1">-- Select a Site Section --</option>';
				if( count($pages) > 0 ){
					foreach( $pages as $p ){
						$selected = ( (int)$p->ID === (int) $meta_field_value) ? 'selected="selected"' : '';
						$options .= "<option value='".$p->ID."' {$selected} >".$p->post_title."</option>";		
					}
				}

				$output .= '<p><label for="'.$meta_field['name'].'">'.$meta_field['description'].'</label><br />';				
				$output .= '<select class="widefat" id="'.$meta_field['name'].'" name="'.$meta_field['name'].'">'.$options.'</select></p>';
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

$MetaBox_SiteSection = new MetaBox_SiteSection();