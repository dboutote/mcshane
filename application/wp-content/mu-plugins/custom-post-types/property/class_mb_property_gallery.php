<?php
/**
 * No direct access
 */
defined( 'ABSPATH' ) or die( 'Nothing here!' );


/**
 * MetaBox_PropertyGallery Metabox Class
 *
 *
 *
 */
class MetaBox_PropertyGallery {

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
		add_action( 'init', array($this, 'register_scripts') );
		add_action( 'admin_enqueue_scripts', array($this, 'add_scripts_backend'), 101 );

		add_action( 'add_meta_boxes_page', array($this,'create_metabox') );
		add_action( 'save_post_page',      array($this,'save_meta'), 0, 3 );

		add_action( 'wp_ajax_setup_taxonomy_terms', array($this, 'get_taxonomy_terms') );
	}

	/**
	 * Build a Select Element with Taxonomy Terms
	 *
	 * @access  public
	 * @since   1.0
	 * @uses get_post_types()
	 * @uses get_object_taxonomies()
	 * @uses get_terms()
	 * @return  string JSON string of select options
	 */
	public function get_taxonomy_terms()
	{
		$raw_data = $_POST;
		$user_errors = array();
		$response = array();
		$notice = '';


		$taxonomies = get_taxonomies();
		$selected_taxonomy = $_POST['tax_type'];
		$selected_taxonomy_obj = get_taxonomy( $selected_taxonomy );

		if( !in_array($selected_taxonomy, $taxonomies ) ){
			$response['code'] = '-1';
			$response['notice'] = __('Taxonomy does not exist.');
			die(json_encode($response));
		}

		$tax_terms = get_terms($selected_taxonomy, array('hide_empty'=>false));

		if( empty($tax_terms) ){
			$response['code'] = '-1';
			$response['notice'] = __('No terms returned.');
			die(json_encode($response));
		}

		$notice .= '<option value="">-- '. __( 'Select Category' ).' --</option>';
		foreach($tax_terms as $term ){
			$notice .= '<option value="'.$term->taxonomy . ':' .$term->slug.'">'.$term->name.' ('.$term->count.')</option>';
		}

		$response['code'] = '1';
		$response['notice'] = $notice;
		$response['tax_name'] = $selected_taxonomy_obj->labels->singular_name;
		die(json_encode($response));
	}


	/**
	 * Register scripts in the backend
	 *
	 * @access  public
	 * @since   1.0
	 * @uses wp_register_script()
	 * @return  void
	 */
	public static function register_scripts()
	{
		wp_register_script(
			'pg_scripts',
			MU_URL  . 'custom-post-types/property/js/script.js',
			array( 'jquery' ),
			1.0,
			true
		);
	}


	/**
	 * Load scripts in the backend
	 *
	 * @access  public
	 * @since   1.0
	 * @uses wp_enqueue_script()
	 * @uses wp_localize_script()
	 */
	public static function add_scripts_backend($hook)
	{
		if( !in_array( $hook, array('post.php', 'post-new.php') ) ){ return; }

		wp_enqueue_script('pg_scripts');

		wp_localize_script(
			'pg_scripts',
			'pgJax',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' )
			)
		);

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
		$basename = 'propertygallery';
		$post_type_name = 'post';

		$post_types = array('page');
		$post_type = get_post_type();

		if( $post_type ){
			$post_type_name = strtolower( get_post_type_object( $post_type )->labels->singular_name );
		}

		$meta_fields = array(
			'property_type' => array(
				'name' => 'property_type',
				'type' => 'select',
				'default' => '',
				'title' => __('Property Type'),
				'description' => __('Select which type of properties will appear on this page.')
			),
			'property_tag' => array(
				'name' => 'property_tag',
				'type' => 'select',
				'default' => '',
				'title' => __('Property Tag '),
				'description' => __('Select which terms will appear on this page.')
			),
		);

		$args = array(
			'meta_box_id' => $basename . 'div',
			'meta_box_name' => $basename . 'info',
			'meta_box_title' => __( 'Property Gallery Settings' ),
			'meta_box_default' => '',
			'meta_box_description' => '',
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
		if('page' !== get_post_type()){
			return;
		}

		$args = $this->get_meta_box_args();
		extract($args);

		if ( function_exists('add_meta_box') ) {
			foreach ($content_types as $content_type) {
				add_meta_box($meta_box_id, $meta_box_title, array($this, 'inner_metabox'), $content_type, $meta_box_position );
			}
		}
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

		$output ='<div class="pg-settings">';

		$output .= '<p>' . $meta_box_description . '</p>';

		foreach( $meta_fields as $meta_field ) {

			$meta_field_value = get_post_meta($post->ID, '_'.$meta_field['name'], true);

			if( '' === $meta_field_value ) {
				$meta_field_value = $meta_field['default'];
			}

			wp_nonce_field( plugin_basename(__CLASS__), $meta_field['name'].'_noncename' );

			if( 'property_type' === $meta_field['name'] ) {

				$_type_terms = get_terms('ctax_proptype', array('hide_empty'=>false));

				$output .= '<p><b><label for="'.$meta_field['name'].'">'.$meta_field['title'].'</label></b><br />';
				$output .= '<select class="tax-prop-type" name="'.$meta_field['name'].'" style="width:99%;">';
				$output .= '<option value="">-- '. __( 'Select Property Type' ).' --</option>';
				foreach( $_type_terms as $_type_term ){
					$taxterm = $_type_term->taxonomy . ':' . $_type_term->slug;
					$output .= '<option value="' . $taxterm . '"' . selected($taxterm,$meta_field_value, false ).' />' . __( $_type_term->name ) . '</option>';
				}
				$output .= '</select>';
				$output .= $meta_field['description'].'<br /></p>';

			}

			if( 'property_tag' === $meta_field['name'] ) {

				$_type_terms = get_terms('ctax_proptag', array('hide_empty'=>false));

				$output .= '<p><b><label for="'.$meta_field['name'].'">'.$meta_field['title'].'</label></b><br />';
				$output .= '<select class="tax-prop-tag" name="'.$meta_field['name'].'" style="width:99%;">';
				$output .= '<option value="">-- '. __( 'Select Property Tag' ).' --</option>';
				foreach( $_type_terms as $_type_term ){
					$taxterm = $_type_term->taxonomy . ':' . $_type_term->slug;
					$output .= '<option value="' . $taxterm . '"' . selected($taxterm,$meta_field_value, false ).' />' . __( $_type_term->name ) . '</option>';
				}
				$output .= '</select>';
				$output .= $meta_field['description'].'<br /></p>';

			}

		}

		$output .= '</div> <!-- /.tg-settings -->';

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

		if( 'page' !== $post->post_type ){
			return $post_id;
		}

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

		// if we're not saving the Landing Page page template
		if( !isset($_POST['page_template']) || 'page-templates/property-gallery.php' !== $_POST['page_template'] ){
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