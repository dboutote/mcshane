<?php
/**
 * No direct access
 */
defined( 'ABSPATH' ) or die( 'Nothing here!' );


/**
 * CPT_Galleries post type class
 *
 * A custom post type for displaying staff profiles
 *
 * @package WordPress
 * @subpackage McShane
 * @since McShane 1.0
 */
class CPT_Galleries
{
	private $meta_config_args;
	const POST_TYPE = 'cpt_gallery';


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
		#add_action( 'init', array($this, 'register_taxonomy'), 999 );
		add_action( 'init', array($this, 'register_scripts') );
		add_action( 'admin_enqueue_scripts', array($this, 'add_scripts_backend'), 101 );
		add_action( 'save_post_'.self::POST_TYPE, array($this,'save_meta'), 0, 3 );
		add_filter( 'include_subheader_dont_show_list', array($this, 'check_post_type'), 0,2);
		add_filter( 'include_featquote_dont_show_list', array($this, 'check_post_type'), 0,2);
		#add_filter( 'manage_'.self::POST_TYPE.'_posts_columns', array($this, 'add_new_columns') );
		#add_action( 'manage_'.self::POST_TYPE.'_posts_custom_column', array($this,'add_column_data'), 10, 2 );
		add_action( 'wp_ajax_setup_gallery_paged_posts', array($this, 'get_paged_posts') );
		add_action( 'wp_ajax_setup_gallery_new_posts', array($this, 'get_new_posts') );
		#add_filter( 'the_content',  array($this,'process_shortcodes'), 7 );
		add_shortcode( 'content_gallery', array($this, 'content_gallery') );
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

		add_shortcode( 'content_gallery', array($this, 'content_gallery') );

		$content = do_shortcode($content);

		// Put the original shortcodes back
		$shortcode_tags = $orig_shortcode_tags;

		return $content;
	}

	
	/**
	 * Process the content_gallery Shortcode
	 *
	 * @access public
	 * @since 1.0
	 * @param array $atts
	 * @param string $content
	 * @uses shortcode_atts()
	 * @return string $output HTML
	 */
	public function content_gallery($atts, $content = null, $code = '')
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

			$_gallery_items = get_post_meta($id, '_gallery_items', true);
			$gallery_title = $post_object->post_title;
			$gallery_title_class = sanitize_title($gallery_title);

			if( is_array($_gallery_items) && count($_gallery_items) > 1 ) {

				usort($_gallery_items, function($a, $b) {
					return $a['order'] - $b['order'];
				});
				ob_start(); ?>

				<div class="gallery clearfix <?php echo $gallery_title_class;?>-gallery">
					<ul>
						<?php foreach($_gallery_items as $_gallery_item) { ?>
							<?php
							$id = (int)$_gallery_item['ID'];
							$the_title = $_gallery_item['name'];
							$is_featured = ( isset($_gallery_item['featured']) ) ? true : false;
							?>
							<li<?php echo ($is_featured)? ' class="featured"': '';?>>
								<a href="<?php echo esc_url(get_permalink($id));?>">
									<?php
									$img_src = $iw = $ih = '';
									if( has_post_thumbnail($id) ){
										$image_obj = wp_get_attachment_image_src( get_post_thumbnail_id($id), 'full');
										$img_src = $image_obj[0];
										$iw = $image_obj[1];
										$ih = $image_obj[2];
									}; ?>
									<img src="<?php echo $img_src;?>" class="background-cover" width="<?php echo $iw;?>" height="<?php echo $ih;?>" />
									<span class="title">
										<strong><?php echo $the_title; ?></strong>
										<br />
										<?php if( has_subheader($id) ) { display_subheader($id); } ?>
									</span>
								</a>
							</li>
						<?php }?>
					</ul>
				</div>

				<?php
				$content = ob_get_clean();

			}

			wp_reset_postdata();

			return $content;
		}


		wp_reset_postdata();

		return $content;

	}


	/**
	 * Return a list of new posts for building a gallery
	 *
	 * @access  public
	 * @since   1.0
	 * @uses get_post_types()
	 * @uses get_object_taxonomies()
	 * @uses get_terms()
	 * @return  string JSON string of select options
	 */
	public function get_new_posts()
	{
		$raw_data = $_POST;
		$user_errors = array();
		$response = array();
		$notice = '';

		$post_types = get_post_types();
		$selected_type = $_POST['post_type'];

		if( !in_array($selected_type, $post_types ) ){
			$response['code'] = '-1';
			$response['notice'] = __('That post type is not registered.');
			die(json_encode($response));
		}

		$tax_query = array();
		$number = 10;

		// build query
		$r = new WP_Query(
			array(
				'post_type'           => $selected_type,
				'posts_per_page'      => $number,
				'no_found_rows'       => true,
				'post_status'         => 'publish',
				'ignore_sticky_posts' => true,
				'tax_query'           => $tax_query,
			)
		);

		if($r->have_posts()) {
			$notice = '';
			while ( $r->have_posts() ) : $r->the_post();
				$notice .= '<label class="gallery-item-title">';
					$notice .= '<li><input class="gallery-item-checkbox" type="checkbox" value="' . get_the_ID() . '" name="gallery-item[-1][gallery-item-object-id]" data-postname="' . get_the_title() . '">' . get_the_title() . '</li>';
				$notice .= '</label>';
			endwhile;
		} else {
			$response['code'] = '-1';
			$response['notice'] = __('No posts found.');
			die(json_encode($response));
		}

		wp_reset_postdata();

		$response['code'] = '1';
		$response['notice'] = $notice;
		die(json_encode($response));
	}


	/**
	 * Return a list of paged posts for building a gallery
	 *
	 * @access  public
	 * @since   1.0
	 * @uses get_post_types()
	 * @uses get_object_taxonomies()
	 * @uses get_terms()
	 * @return  string JSON string of select options
	 */
	public function get_paged_posts()
	{
		$raw_data = $_POST;
		$user_errors = array();
		$response = array();
		$notice = '';

		$post_types = get_post_types();
		$selected_type = $_POST['post_type'];

		if( !in_array($selected_type, $post_types ) ){
			$response['code'] = '-1';
			$response['notice'] = $notice;
			die(json_encode($response));
		}

		$tax_query = array();
		$number = 10;
		$paged = absint( $raw_data['paged'] );

		// build query
		$r = new WP_Query(
			array(
				'post_type'           => $selected_type,
				'posts_per_page'      => $number,
				'no_found_rows'       => true,
				'post_status'         => 'publish',
				'ignore_sticky_posts' => true,
				'tax_query'           => $tax_query,
				'paged'               => $paged,
			)
		);

		if($r->have_posts()) {
			$notice = '';
			while ( $r->have_posts() ) : $r->the_post();
				$notice .= '<label class="gallery-item-title">';
					$notice .= '<li><input class="gallery-item-checkbox" type="checkbox" value="' . get_the_ID() . '" name="gallery-item[-1][gallery-item-object-id]" data-postname="' . get_the_title() . '">' . get_the_title() . '</li>';
				$notice .= '</label>';
			endwhile;
		} else {
			$response['code'] = '-1';
			$response['notice'] = __('No posts found.');
			die(json_encode($response));
		}

		wp_reset_postdata();

		$response['code'] = '1';
		$response['notice'] = $notice;
		die(json_encode($response));
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
		if( !in_array( $hook, array('post.php', 'post-new.php') ) ){
			return;
		}

		/*if( !isset($_GET['post_type']) ){
			return;
		}

		if( isset($_GET['post_type']) && self::POST_TYPE !== $_GET['post_type'] ) {
			return;
		}
		*/

		wp_enqueue_style('cg_styles');

		wp_enqueue_script('cg_scripts');

		wp_localize_script(
			'cg_scripts',
			'cgJax',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' )
			)
		);



		return;
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
			'cg_scripts',
			MU_URL  . 'custom-post-types/content-galleries/js/script.js',
			array( 'jquery' ),
			1.0,
			true
		);
		wp_register_style(
			'cg_styles',
			MU_URL  . 'custom-post-types/content-galleries/css/admin.css',
			array(),
			1.0,
			'all'
		);
	}


	/**
	 * Add taxonomy data to taxonomy column on edit.php Table
	 *
	 * @access public
	 * @since 1.0
	 *
	 * @param string $column_name The name of the current table column
	 * @return integer $post_id The ID of the current post
	 */
	function add_column_data( $column_name, $post_id )
	{
		if( $column_name == 'ctax_teamdepartment' ) {
			$_posttype 	= self::POST_TYPE;
			$_taxonomy 	= 'ctax_teamdepartment';
			$terms 		= get_the_terms( $post_id, $_taxonomy );
			if ( !empty( $terms ) ) {
				$out = array();
				foreach ( $terms as $c )
					$_taxonomy_title = esc_html(sanitize_term_field('name', $c->name, $c->term_id, 'category', 'display'));
					$out[] = "<a href='edit.php?ctax_teamdepartment=$_taxonomy_title&post_type=$_posttype'>$_taxonomy_title</a>";
				echo join( ', ', $out );
			}
			else {
				_e('Uncategorized');
			}
		}
	}


	/**
	 * Add taxonomy column on edit.php Table
	 *
	 * @access public
	 * @since 1.0
	 *
	 * @param array $columns Default table columns
	 * @return array $columns Updated column array with new taxonomy column
	 */
	function add_new_columns($columns)
	{
		unset($columns['date']);
		$columns['ctax_teamdepartment'] = __('Department');
		$columns['date'] = __('Date');
		return $columns;
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
		$name = 'Content Gallery';
		$plural     = 'Content Galleries';

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
				'menu_icon'              => 'dashicons-format-gallery',
				'capability_type'        => 'post',
				'supports'               => array('title'),
				'register_meta_box_cb'   => array(__CLASS__, 'create_metabox' ),
				'taxonomies'             => array(),
				'has_archive'            => false,
				'rewrite'                => false,
				'query_var'              => false,
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

		$basename = 'contentgalleryinfo';
		$post_type = get_post_type();
		$post_types = array(self::POST_TYPE);
		if( $post_type ){
			$post_type_name =  get_post_type_object( $post_type )->labels->singular_name;
			$post_type_name_lower = strtolower($post_type_name);
		}

		$meta_fields = array(
			'gallery_items' => array(
				'name' => 'gallery_items',
				'type' => 'text',
				'default' => '',
				'title' => '',
				'description' => '',
			),
		);

		$args = array(
			'meta_box_id' => $basename . 'div',
			'meta_box_name' => $basename . 'info',
			'meta_box_title' => sprintf( __( 'Additional %s Info', 'mcshane' ), $post_type_name ),
			'meta_box_default' => '',
			'meta_box_description' => sprintf( __( 'Use these settings to add additional info for this %s.', 'mcshane' ), $post_type_name, $post_type_name ),
			'content_types' => $post_types,
			'meta_box_position' => 'normal',
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
		$output = '';

		// get configuration args
		$args = self::_get_meta_box_args();
		extract($args);

		$postID = ( $post && $post->ID > 0 ) ? $post->ID : 0 ;
		$postParent = ( $post && $post->post_parent > 0 ) ? $post->post_parent : 0 ;

		if('post-new.php' === $pagenow){
			$postID = 0;
		}

		foreach( $meta_fields as $meta_field ) {

			$meta_field_value = get_post_meta($post->ID, '_'.$meta_field['name'], true);

			#debug($meta_field);
			#debug($meta_field_value);

			#debug(get_post_custom($post->ID));

			if( '' === $meta_field_value ) {
				$meta_field_value = $meta_field['default'];
			}
			wp_nonce_field( plugin_basename(__CLASS__), $meta_field['name'].'_noncename' );

			if ( 'gallery_items' === $meta_field['name']) {

				ob_start(); ?>

				<div id="gallery-frame">

					<div id="gallery-settings-column" class="left">

						<select id="gallery-settings-posttype" name="gallery_settings_posttype" class="widefat wfc-entries-type">
							<?php
							// get all public post types
							$args = array( 'public' => true );
							$arg_output = 'objects';
							$post_types = get_post_types($args,$arg_output);

							// exclude these post types
							$exclude = array('attachment', 'revision','nav_menu_item', 'page');
							foreach ($post_types as $key => $v){
								if(in_array($key, $exclude)) {
									unset($post_types[$key]);
								}
							}

							foreach ($post_types as $post_type ) {
								$query_var = ( ''!= $post_type->query_var ) ? $post_type->query_var : $post_type->name ; ?>
								<option value="<?php echo $query_var; ?>"<?php ?><?php selected( 'post', $query_var ); ?>><?php _e( $post_type->labels->singular_name ); ?></option>
							<?php } ?>
						</select>

						<?php
						$original_post = $post;
						$posts_per_page = 10;
						$curr_page = 1;

						$post_args = array(
							'posts_per_page' => $posts_per_page,
							'post_type' => 'post'
						);

						$r = new WP_Query($post_args);

						if ($r->have_posts()) : ?>

							<div id="gallery-settings" class="posttypediv">

								<div class="tabs-panel">

									<ul id="gallery-settings-checklist">
										<?php while ( $r->have_posts() ) : $r->the_post(); ?>
											<label class="gallery-item-title">
												<li><input class="gallery-item-checkbox" type="checkbox" value="<?php echo $post->ID;?>" name="gallery-item[-1][gallery-item-object-id]" data-postname="<?php echo $post->post_title;?>"><?php echo $post->post_title;?></li>
											</label>
										<?php endwhile; ?>
									</ul>

									<?php if( $r->max_num_pages > 1 ) { ?>
										<div id="gallery-settings-nav">
											<input type="hidden" name="gallery_settings_max_pages" value="<?php echo $r->max_num_pages; ?>" />
											<input type="hidden" name="gallery_settings_curr_page" value="<?php echo $curr_page; ?>" />
											<span class="meta-nav prev button">prev</span>
											<span class="meta-nav next button">next</span>
											<span class="spinner"></span>
										</div>
									<?php }; ?>

								</div> <!-- /.tabs-panel -->

								<div id="gallery-settings-controls" class="controls">
									<span id="submit-gallery-posttype" class="button-secondary submit-add-to-gallery right">Add to Gallery</span>
									<span class="spinner"></span>
								</div>

							</div>  <!-- /#gallery-settings -->

						<?php endif; ?>

						<?php
						wp_reset_postdata();
						$post = $original_post;
						//debug($r);
						?>
					</div>  <!-- /#gallery-settings-column -->

					<div id="gallery-management-column">

						<div class="gallery-management">

							<table id="gallery-management-list" class="widefat comments fixed" cellspacing="0">
								<thead>
									<tr>
										<th style="max-width:100px;">Order</th>
										<th>Title</th>
										<th>Featured</th>
										<th style="max-width:100px;">Remove</th>
									</tr>
								</thead>
								<tbody>

								<?php if( is_array($meta_field_value) && count($meta_field_value) > 1 ) {

									usort($meta_field_value, function($a, $b) {
										return $a['order'] - $b['order'];
									});

									$style = '';
									$count = 1;
									foreach ($meta_field_value as $gallery_item ) {
										$style = ( ' class="alternate"' == $style ) ? '' : ' class="alternate"';
										$list_item_id = $gallery_item['ID'];
										$list_item_name = $gallery_item['name'];
										$list_item_order = $gallery_item['order'];
										$list_item_featured = ( isset($gallery_item['featured']) ) ? ' checked="checked"' : '';
										?>
										<tr <?php echo $style; ?> id="post-<?php echo $list_item_id; ?>">
											<td>
												<input class="hidden" type="hidden" value="<?php echo $list_item_id; ?>" name="gallery_items[<?php echo $list_item_id; ?>][ID]" />
												<input class="small-text" type="text" value="<?php echo $list_item_order; ?>" name="gallery_items[<?php echo $list_item_id; ?>][order]" />
											</td>
											<td>
												<input class="text readonly" type="text" value="<?php echo $list_item_name; ?>" name="gallery_items[<?php echo $list_item_id; ?>][name]" readonly="readonly"/>
											</td>
											<td>
												<input class="checkbox" type="checkbox" value="1" name="gallery_items[<?php echo $list_item_id; ?>][featured]" <?php echo $list_item_featured;?>/>
											</td>
											<td><a class="del">[x]</a></td>
										</tr>
									<?php }

								}?>
								</tbody>
							</table>

						</div>

						<?php if( $postID > 0 && $postParent < 1 ){ ?>
							<p class="shortcode">Copy/paste this shortcode wherever you want your gallery to appear.<br />
							<input class="shortcodecode widefat" type="text" value="[content_gallery id=<?php echo $postID;?>]" readonly="readonly" /></p>
						<?php }; ?>

					</div>  <!-- /#gallery-management-column -->

				</div>  <!-- /#gallery-frame -->

				<?php

				$output .= ob_get_clean();
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

#debug($meta_field);

			// verify this came from the our screen and with proper authorization, (b/c save_post can be triggered at other times)
			#if( !isset($_POST[$meta_field['name'].'_noncename']) || !wp_verify_nonce( $_POST[$meta_field['name'].'_noncename'], __CLASS__ ) ) {
			#	return $post_id;
			#}

			// Ok, we're authenticated: we need to find and save the data
			$data = ( isset($_POST[$meta_field['name']]) ) ? $_POST[$meta_field['name']] : '';
			$data = ( is_array($data) ) ? array_filter($data) : trim($data);
#debug(wp_verify_nonce( $_POST[$meta_field['name'].'_noncename'], __CLASS__ ));
#debug($data);

			if ( '' != $data && '-1' != $data  ) {
				update_post_meta( $post->ID, '_'.$meta_field['name'], $data );
			} else {
				delete_post_meta( $post->ID, '_'.$meta_field['name'] );
			}

		}
#debug($_POST);
#wp_die(__METHOD__);
		return $post_id;

	 }





}


$CPT_Galleries = new CPT_Galleries();