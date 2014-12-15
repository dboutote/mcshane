<?php
/**
 * No direct access
 */
defined( 'ABSPATH' ) or die( 'Nothing here!' );


/**
 * CPT_FeatLink post type class
 *
 * A custom post type for displaying emhanced links
 *
 * @package WordPress
 * @subpackage McShane
 * @since McShane 1.0
 */
class CPT_FeatLink
{
	private $meta_config_args;
	const POST_TYPE = 'cpt_flink';


	/**
	 * The constructor
	 *
	 * Initialize & hook into WP
	 *
	 * @access  public
	 * @since   1.0
	 * @return  void
 	 */
	public function __construct()
	{
		add_action( 'init', array($this, 'register_post_type'), 0 );	
		add_action( 'save_post_'.self::POST_TYPE, array($this,'save_meta'), 0, 3 );		
		add_filter( 'include_subheader_dont_show_list', array($this, 'check_post_type'), 0,2);
		add_filter( 'the_content',  array($this,'process_shortcodes'), 7 );
		#add_shortcode( 'feat_link', array($this, 'feat_link') );		
	}
	
	
	/**
	 * Preprocess shortcodes before WordPress processes the content
	 *
	 * @access  public
	 * @since   1.0
	 * @uses do_shortcode()
	 */
	function process_shortcodes($content)
	{
		global $shortcode_tags;

		$orig_shortcode_tags = $shortcode_tags;
		$shortcode_tags      = array();

		add_shortcode( 'feat_link', array($this, 'feat_link') );

		$content = do_shortcode($content);

		// Put the original shortcodes back
		$shortcode_tags = $orig_shortcode_tags;

		return $content;
	}
	
	
	/**
	 * Process the feat_link Shortcode
	 *
	 * @access public
	 * @since 1.0
	 * @param array $atts
	 * @param string $content
	 * @uses shortcode_atts()
	 * @return string $output HTML
	 */
	public function feat_link($atts, $content = null, $code = '')
	{
		extract(shortcode_atts(array(
			'id' => 0
		), $atts));

		if( (int)$id < 1 ) {
			return;
		}

		// get the post object for this content type
		$post_object = get_post($id);

		if( !is_null($post_object) ){
		
			$_flink_url = get_post_meta($id, '_flink_url', true);
		
			$content = '';
			$content .= '<div class="feature-link clearfix">';
				$content .= '<div class="left">';
					if($_flink_url) {
						$content .= '<a href="' . esc_url($_flink_url) . '"><img src="'.get_stylesheet_directory_uri() . '/images/arrow.svg"></a>';
					}
					$content .= '<div class="title">' . apply_filters('the_title', $post_object->post_title) . '</div>';
					$content .= '<div class="content">' . $post_object->post_excerpt . '</div>';
				$content .= '</div>';
				if( has_post_thumbnail($id) ) {
					$image_obj = wp_get_attachment_image_src( get_post_thumbnail_id($id), 'full');
					$img_src = $image_obj[0];
					$img_width = $image_obj[1];
					$img_height = $image_obj[2];
					$content .= '<div class="right">';
						$content .= '<img src="'.$img_src.'" width="'.$img_width.'" height="'.$img_height.'" class="background-cover" alt="" />';						
					$content .= '</div>';
				}				
			$content .= '</div>';
			
			
			wp_reset_postdata();
			
			return $content;
		}
		
		
		wp_reset_postdata();

		return $content;

	}	

	/**
	 * Remove this post type from the Sub Header meta box
	 *
	 * @access public
	 * @since 1.0
	 *
	 * @param array $dont_show The array of post types to exclude
	 * @param string $post_type The post type to check against the $dont_show array
	 */
	public function check_post_type($dont_show, $post_type)
	{
		$dont_show[] = self::POST_TYPE;
		return $dont_show;
	}


	/**
	 * Register post type
	 *
	 * @access public
	 * @since 1.0
	 */
	public static function register_post_type()
	{
		$name = 'Featured Link';
		$plural = $name . 's';

		// Labels
		$labels = array(
			'name'                 => _x( $plural, 'post type general name' ),
			'singular_name'        => _x( $name, 'post type singular name' ),
			'add_new'              => _x( 'Add New', strtolower( $name ) ),
			'menu_name'            => __( $plural ),
			'add_new_item'         => __( 'Add New ' . $name ),
			'edit_item'            => __( 'Edit ' . $name ),
			'new_item'             => __( 'New ' . $name ),
			'all_items'            => __( 'All ' . $plural ),
			'view_item'            => __( 'View ' . $name ),
			'search_items'         => __( 'Search ' . $plural ),
			'not_found'            => __( 'No ' . strtolower( $plural ) . ' found'),
			'not_found_in_trash'   => __( 'No ' . strtolower( $plural ) . ' found in Trash'),
		);

		// Register post type
		register_post_type(
			self::POST_TYPE,
			array(
				'labels'                 => $labels,
				'public'                 => false,
				'show_ui'                => true,
				'exclude_from_search'    => true,
				'show_in_nav_menus'      => false,
				//'menu_position'          => 5,
				'menu_icon'              => 'dashicons-admin-links',
				'capability_type'        => 'post',
				'capabilities'           => array(
					'edit_post'              => 'manage_categories',
					'edit_posts'             => 'manage_categories',
					'edit_others_posts'      => 'manage_categories',
					'publish_posts'          => 'manage_categories',
					'read_private_posts'     => 'manage_categories',
					'delete_post'            => 'manage_categories',
					'read_post'              => 'manage_categories'
				),
				'supports'               => array('title','excerpt', 'thumbnail'),
				'register_meta_box_cb'   => array(__CLASS__, 'create_metabox' ),
				//'taxonomies'             => array('ctax_teamdepartment'),
				'has_archive'            => false,
				'rewrite'                => false,
				'query_var'              => false
			)
		);
	}


	/**
	 * Create the metabox
	 *
	 * @access public
	 * @since 1.0
	 *
	 * @uses add_meta_box()
	 */
	public static function create_metabox()
	{
		$args = self::_get_meta_box_args();
		extract($args);

		if ( function_exists('add_meta_box') ) {
			foreach ($content_types as $content_type) {
				add_meta_box($meta_box_id, $meta_box_title, array(__CLASS__, 'inner_metabox'), $content_type, $meta_box_position );
			}
		}
	}


	/**
	 * Configuration params for the Metabox
	 *
	 * @access protected
	 * @since 1.0
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
	 */
	protected static function _set_meta_box_args()
	{

		$basename = 'featlinkinfo';
		$post_type = get_post_type();
		$post_types = array(self::POST_TYPE);
		if( $post_type ){
			$post_type_name =  get_post_type_object( $post_type )->labels->singular_name;
			$post_type_name_lower = strtolower($post_type_name);
		}

		$meta_fields = array(
			'flink_url' => array(
				'name' => 'flink_url',
				'type' => 'text',
				'default' => '',
				'title' => __('Link URL'),
				'description' => __( 'Enter the URL for this featured link.', 'mcshane' )
			)
		);

		$args = array(
			'meta_box_id' => $basename . 'div',
			'meta_box_name' => $basename . 'info',
			'meta_box_title' => sprintf( __( '%s Shortcode Info', 'mcshane' ), $post_type_name ),
			'meta_box_default' => '',
			'meta_box_description' => '',
			'content_types' => $post_types,
			'meta_box_position' => 'advanced',
			'meta_box_priority' => 'high',
			'meta_fields' => $meta_fields
		);

		return $args;


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
		global $pagenow;

		// get configuration args
		$args = self::_get_meta_box_args();
		extract($args);
		
		$postID = ( $post && $post->ID > 0 ) ? $post->ID : 0 ;
		$postParent = ( $post && $post->post_parent > 0 ) ? $post->post_parent : 0 ;	

		if('post-new.php' === $pagenow){
			$postID = 0;
		}		

		$output = '';
		$output .= '<p>'.$meta_box_description.'</p>';
				
		foreach( $meta_fields as $meta_field ) {

			$meta_field_value = get_post_meta($post->ID, '_'.$meta_field['name'], true);

			if( '' === $meta_field_value ) {
				$meta_field_value = $meta_field['default'];
			}

			wp_nonce_field( plugin_basename(__CLASS__), $meta_field['name'].'_noncename' );

			if ( 'flink_url' === $meta_field['name']) {
				$output .= '<p><b><label for="'.$meta_field['name'].'">'.$meta_field['title'].'</label></b><br />';
				$output .= '<input class="reg-text" type="text" id="'.$meta_field['name'].'" name="'.$meta_field['name'].'" value="'.$meta_field_value.'" size="16" style="width: 99%;" /> <span class="desc">'.$meta_field['description'].'</span></p>';
			}
			
		}
		
		if( $postID > 0 && $postParent < 1 ){
			$output .=  '<p>Copy/paste this shortcode wherever you want your link to appear.<br /><input type="text" style="width:98%;" value="[feat_link id='.$postID.']" readonly="readonly" /></p>';
		}		
		
		echo $output;

		return;

	}


	/**
	 * Process saving the metadata
	 *
	 * @access public
	 * @since 1.0
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
		$args = self::_get_meta_box_args();
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


$CPT_FeatLink = new CPT_FeatLink();