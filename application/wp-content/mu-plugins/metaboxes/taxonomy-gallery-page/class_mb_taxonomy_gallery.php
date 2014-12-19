<?php
/**
 * No direct access
 */
defined( 'ABSPATH' ) or die( 'Nothing here!' );


/**
 * MetaBox_TaxonomyGallery Metabox Class
 *
 * Adds a configuration for the background image rotator on landing-page.php templates
 *
 */
class MetaBox_TaxonomyGallery {

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

		add_action( 'add_meta_boxes', array($this,'create_metabox') );
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
			'tg_scripts',
			MU_URL  . 'metaboxes/taxonomy-gallery-page/js/script.js',
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

		wp_enqueue_script('tg_scripts');

		wp_localize_script(
			'tg_scripts',
			'tgJax',
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
		$basename = 'taxonomygallery';
		$post_type_name = 'post';

		$post_types = array('page');
		$post_type = get_post_type();

		if( $post_type ){
			$post_type_name = strtolower( get_post_type_object( $post_type )->labels->singular_name );
		}

		$meta_fields = array(
			'tax_type' => array(
				'name' => 'tax_type',
				'type' => 'select',
				'default' => '',
				'title' => __('Taxonomy'),
				'description' => __('Select which category of entries will appear on this page.')
			),
			'tax_terms' => array(
				'name' => 'tax_terms',
				'type' => 'select',
				'default' => '',
				'title' => __('Category'),
				'description' => __('Select which terms will appear on this page.')
			),
		);

		$args = array(
			'meta_box_id' => $basename . 'div',
			'meta_box_name' => $basename . 'info',
			'meta_box_title' => __( 'Taxonomy Gallery Settings' ),
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

		$selected_taxonomy = '';

		$tax_args= array(
			'public' => true,
		);
		$output = 'objects';
		$taxonomies = get_taxonomies($tax_args,$output);
		foreach ($taxonomies as $tax ){
			$tax_array[$tax->name] = $tax->labels->singular_name;
		}


		$output ='<div class="tg-settings">';

		$output .= '<p>' . $meta_box_description . '</p>';

		foreach( $meta_fields as $meta_field ) {

			$meta_field_value = get_post_meta($post->ID, '_'.$meta_field['name'], true);

			if( '' === $meta_field_value ) {
				$meta_field_value = $meta_field['default'];
			}

			wp_nonce_field( plugin_basename(__CLASS__), $meta_field['name'].'_noncename' );


			if( 'tax_type' === $meta_field['name'] ) {

				// sort alphabetically
				asort($tax_array);
				$type_selected = ( '' !== $meta_field_value ) ? ' type-selected': '';
				$selected_taxonomy = $meta_field_value;
				$output .= '<p><b><label for="'.$meta_field['name'].'">'.$meta_field['title'].'</label></b><br />';
				$output .= '<select class="tax-type'.$type_selected.'" name="'.$meta_field['name'].'" style="width:99%;">';
				$output .= '<option value="">-- '. __( 'Select Taxonomy' ).' --</option>';
				foreach( $tax_array as $ptype => $pname ){
					$output .= '<option id="'.$meta_field['name'].'_'.$ptype.'" value="'.$ptype.'"'.selected($ptype,$meta_field_value, false ).' />'.__($pname).'</option>';
				}
				$output .= '</select>';
				$output .= $meta_field['description'].'<br /></p>';
			}

			if( 'tax_terms' === $meta_field['name'] ) {
				$style = ' style="display:none;"';
				$select_options = '';
				if('' !== $selected_taxonomy){
					$style = '';
					$selected_term = $meta_field_value;
					$tax_terms = get_terms($selected_taxonomy);
					foreach($tax_terms as $term ){
						$select_options .= '<option value="' . $term->taxonomy . ':' . $term->slug . '"'.selected($term->taxonomy . ':' .$term->slug,$selected_term, false ).'>'.$term->name.'</option>';
					}
				}
				$output .= '<div class="taxplaceholder"'.$style.'>';
				$output .= '<p><b><label for="'.$meta_field['name'].'"><span id="selected-tax-title"></span>'.$meta_field['title'].'</label></b><br />';
				$output .= '<select class="tax-terms" name="'.$meta_field['name'].'" style="width:99%;">';
				$output .= '<option value="">-- '. __( 'Select Term' ).' --</option>';
				$output .= $select_options;
				$output .= '</select>';
				$output .= $meta_field['description'].'<br /></p>';
				$output .= '</div>';
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
		if( !isset($_POST['page_template']) || 'page-templates/taxonomy-gallery.php' !== $_POST['page_template'] ){
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

$MetaBox_TaxonomyGallery = new MetaBox_TaxonomyGallery();