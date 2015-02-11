<?php
/**
 * Widget_Featured_Content widget class
 *
 * A sidebar Widget for displaying the latest content of any post type, including custom types.  Allows you to select sort filter and order.
 *
 * @link http://codex.wordpress.org/Widgets_API#Developing_Widgets
 *
 * @package WordPress
 * @subpackage McShane
 * @since McShane 1.0
 */
class Widget_Featured_Content extends WP_Widget {


	/**
	 * Constructor.
	 *
	 * @since 1.0
	 *
	 * @return Widget_Featured_Content
	 */
	public function __construct()
	{
		$widget_ops = array(
			'classname' => 'widget_featured_content',
			'description' => __( "Display a mini featured content slider.")
		);

		parent::__construct('featured-content', __('Featured Content'), $widget_ops);

		$this->alt_option_name = 'widget_featured_content';

		add_action( 'init', array($this, 'register_scripts') );
		add_action( 'admin_enqueue_scripts', array($this, 'add_scripts_backend'), 101 );
		add_action( 'wp_ajax_setup_type_taxonomies', array($this, 'get_type_taxonomies') );

		add_action( 'save_post', array($this, 'flush_widget_cache') );
		add_action( 'deleted_post', array($this, 'flush_widget_cache') );
		add_action( 'switch_theme', array($this, 'flush_widget_cache') );
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
	public function get_type_taxonomies()
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

		$tax_types = get_object_taxonomies($selected_type);

		if( empty($tax_types) ){
			$response['code'] = '-1';
			$response['notice'] = $notice;
			die(json_encode($response));
		}

		$tax_terms = get_terms($tax_types, array('hide_empty'=>false));

		if( empty($tax_terms) ){
			$response['code'] = '-1';
			$response['notice'] = $notice;
			die(json_encode($response));
		}

		$notice .= '<option value="">' . __( 'Select Category' ) . '</option>';
		foreach($tax_terms as $term ){
			$notice .= '<option value="'.$term->taxonomy . ':' .$term->slug.'">'.$term->name.' ('.$term->count.')</option>';
		}

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
		if( 'widgets.php' !== $hook ){ return; }

		wp_enqueue_script('fcw_scripts');

		wp_localize_script(
			'fcw_scripts',
			'fcwJax',
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
			'fcw_scripts',
			MU_URL  . 'widgets/widget-feat-content/js/script.js',
			array( 'jquery' ),
			1.0,
			true
		);
	}


	/**
	 * Output the HTML for this widget.
	 *
	 * @access public
	 * @since 1.0
	 *
	 * @param array $args     An array of standard parameters for widgets in this theme.
	 * @param array $instance An array of settings for this widget instance.
	 */
	public function widget($args, $instance)
	{
		$cache = array();
		if ( ! $this->is_preview() ) {
			$cache = wp_cache_get( 'widget_featured_content', 'widget' );
		}

		if ( ! is_array( $cache ) ) {
			$cache = array();
		}

		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}

		ob_start();

		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : '';
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$posttype = ( ! empty( $instance['posttype'] ) ) ? $instance['posttype'] : 'post';
		$orderby = ( ! empty( $instance['orderby'] ) ) ? $instance['orderby'] : 'date';
		$order = ( ! empty( $instance['order'] ) ) ? $instance['order'] : 'desc';
		$category = ( ! empty( $instance['category'] ) ) ? $instance['category'] : '';

		$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
		if ( ! $number ){
			$number = 5;
		}

		$text_url = ( ! empty( $instance['text_url'] ) ) ? $instance['text_url'] : '';
		$text_link = ( ! empty( $instance['text_link'] ) ) ? $instance['text_link'] : $text_url;

		$tax_query = array();

		if( '' !== $category ) {
			$meta_tax = explode(':', $category);
			$selected_tax = $meta_tax[0];
			$selected_term = $meta_tax[1];

			$tax_query =  array(
				array(
					'taxonomy' => $selected_tax,
					'field'    => 'slug',
					'terms'    => $selected_term,
				)
			);
		}

		/**
		 * Filter the arguments for the Recent Posts widget.
		 *
		 * @since 3.4.0
		 *
		 * @see WP_Query::get_posts()
		 *
		 * @param array $args An array of arguments used to retrieve the recent posts.
		 */
		$r = new WP_Query(
			array(
				'post_type'           => $posttype,
				'posts_per_page'      => $number,
				'no_found_rows'       => true,
				'post_status'         => 'publish',
				'ignore_sticky_posts' => true,
				'orderby'             => $orderby,
				'order'               => $order,
				'tax_query'           => $tax_query
			)
		);

		if ($r->have_posts()) : ?>

			<?php echo $args['before_widget']; ?>

			<?php if ( $title ) {
				echo $args['before_title'] . $title . $args['after_title'];
			} ?>

			<div class="feature-slider">

				<ul class="slides">

					<?php while ( $r->have_posts() ) : $r->the_post(); ?>

						<?php
						$img_src = $iw = $ih = '';
						if( has_post_thumbnail() ){
							$image_obj = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full');
							$img_src = $image_obj[0];
							$iw = $image_obj[1];
							$ih = $image_obj[2];
							$image_thumb_obj = wp_get_attachment_image_src( get_post_thumbnail_id(), 'mcsh-gallery-thumb');
							$img_thumb_src = $image_thumb_obj[0];
						}; ?>
						<li data-thumb="<?php echo $img_thumb_src;?>">
							<div class="left"><img src="<?php echo $img_src;?>" class="background-cover" width="<?php echo $iw;?>" height="<?php echo $ih;?>" /></div>
							<div class="right">
								<h2><?php get_the_title() ? the_title() : the_ID(); ?></h2>
								<?php if( has_subheader() ) { ?>
									<h4><?php display_subheader(); ?></h4>
								<?php } ?>
								<?php the_excerpt(); ?>
								<a href="<?php the_permalink(); ?>" class="btn bottom">More information</a>
							</div>
						</li>

					<?php endwhile; ?>

				</ul>

				<?php if ( $text_url ) { ?>
					<a href="<?php echo $text_url; ?>" class="btn bottom right"><?php echo $text_link; ?></a>
				<?php }; ?>

			</div> <!-- /.feature-slider -->

		<?php echo $args['after_widget']; ?>

		<?php
		endif;

		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();

		if ( ! $this->is_preview() ) {
			$cache[ $args['widget_id'] ] = ob_get_flush();
			wp_cache_set( 'widget_featured_content', $cache, 'widget', 300 );
		} else {
			ob_end_flush();
		}
	}


	/**
	 * Deal with the settings when they are saved by the admin.
	 *
	 * Here is where any validation should happen.
	 *
	 * @since 1.0
	 *
	 * @param array $new_instance New widget instance.
	 * @param array $instance     Original widget instance.
	 * @return array Updated widget instance.
	 */
	public function update( $new_instance, $old_instance )
	{
		$instance                       = $old_instance;
		$instance['title']              = strip_tags($new_instance['title']);
		$instance['posttype']           = $new_instance['posttype'];
		$instance['orderby']            = $new_instance['orderby'];
		$instance['order']              = $new_instance['order'];
		$instance['number']             = (int) $new_instance['number'];
		$instance['text_link']          = strip_tags($new_instance['text_link']);
		$instance['text_url']           = strip_tags($new_instance['text_url']);
		$instance['category']           = $new_instance['category'];

		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['widget_featured_content']) )
			delete_option('widget_featured_content');

		return $instance;
	}

	/**
	 * Clear out the cache settings this widget creates
	 *
	 * Called on "save_post", "deleted_post", and "switch_theme" filters
	 *
	 * @since 1.0
	 *
	 */
	public function flush_widget_cache() {
		wp_cache_delete('widget_featured_content', 'widget');
	}


	/**
	 * Display the form for this widget on the Widgets page of the Admin area.
	 *
	 * @since 1.0
	 *
	 * @param array $instance
	 */
	public function form( $instance ) {
        $instance = wp_parse_args( (array) $instance,
            array(
                  'posttype'    => '',
                  'title'       => '',
                  'number'      => 10,
                  'orderby'     => 'date',
                  'order'       => 'DESC',
                  'text_link'   => 'View More',
				  'text_url'    => '',
				  'category'    => ''
			)
        );
		extract($instance);
		?>

		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<div class="wfc-content-settings">
			<p><label for="<?php echo $this->get_field_id('posttype'); ?>"><?php _e( 'Post Type:' ); ?></label>
			<select name="<?php echo $this->get_field_name('posttype'); ?>" id="<?php echo $this->get_field_id('posttype'); ?>" class="widefat wfc-entries-type">
				<option value=""><?php _e( 'Select Post Type' ); ?></option>
				<?php
				// get all public post types
				$args = array( 'public' => true );
				$output = 'objects';
				$post_types = get_post_types($args,$output);

				// exclude these post types
				$exclude = array('attachment', 'revision','nav_menu_item', 'page');
				foreach ($post_types as $key => $v){
					if(in_array($key, $exclude)) {
						unset($post_types[$key]);
					}
				}

				foreach ($post_types as $post_type ) {
					$query_var = ( ''!= $post_type->query_var ) ? $post_type->query_var : $post_type->name ; ?>
					<option value="<?php echo $query_var; ?>"<?php selected( $instance['posttype'], $query_var ); ?>><?php _e( $post_type->labels->singular_name ); ?></option>
				<?php } ?>
			</select></p>

			<?php
			$hlder_style = ' style="display:none;"';
			$select_options = '<option value="">' . __( 'Select Category' ) . '</option>';

			if( '' !== $category || '' !== $posttype ){
				// if the category isn't blank, show the select
				$hlder_style = '';
				$selected_term = '';

				// if the category isn't blank, show the categories
				if( '' !== $category ) {
					$meta_tax = explode(':', $category);
					$selected_tax = $meta_tax[0];
					$selected_term = $meta_tax[1];
				}

				// if the category is empty, but the posttype isn't
				if('' === $category ){
					$selected_tax = get_object_taxonomies($posttype);
				}

				$tax_terms = get_terms($selected_tax, array('hide_empty'=>false));
				foreach($tax_terms as $term ){
					$select_options .= '<option value="'.$term->taxonomy . ':' .$term->slug.'"'.selected($term->slug,$selected_term, false).'>'.$term->name.' ('.$term->count.')</option>';
				}
			}

			?>

			<div class="taxplaceholder"<?php echo $hlder_style;?>>
				<p><label for="<?php echo $this->get_field_id('category'); ?>"><?php _e( 'Category:' ); ?></label>
				<select name="<?php echo $this->get_field_name('category'); ?>" id="<?php echo $this->get_field_id('category'); ?>" class="widefat wfc-entries-tax">
					<?php echo $select_options;?>
				</select></p>
			</div>
		</div>

        <p><label for="<?php echo $this->get_field_id('orderby'); ?>"><?php _e('Sort List By:'); ?></label>
        <select name="<?php echo $this->get_field_name('orderby'); ?>" id="<?php echo $this->get_field_id('orderby'); ?>" class="widefat">
            <?php
            $orderby_args = array(
				'date' => 'Publish Date',
				'title' => 'Title',
				'menu_order' => 'Menu Order',
				'rand' => 'Random'
            );
			asort($orderby_args);

            foreach ($orderby_args as $k => $v ) { ?>
                <option value="<?php echo $k; ?>"<?php selected( $instance['orderby'], $k ); ?>><?php _e( $v ); ?></option>
            <?php } ?>
        </select></p>

        <p><label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('Order List By:'); ?></label>
        <select name="<?php echo $this->get_field_name('order'); ?>" id="<?php echo $this->get_field_id('order'); ?>" class="widefat">
            <?php
            $order_args = array(
                'asc' => 'Ascending',
                'desc' => 'Descending'
            );
            foreach ($order_args as $k => $v ) { ?>
                <option value="<?php echo $k; ?>"<?php selected( $instance['order'], $k ); ?>><?php _e( $v ); ?></option>
            <?php } ?>
        </select></p>

		<p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts to show:' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>

		<p><label for="<?php echo $this->get_field_id( 'text_link' ); ?>"><?php _e( '&#8220;View More&#8221; Link Text:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'text_link' ); ?>" name="<?php echo $this->get_field_name( 'text_link' ); ?>" type="text" value="<?php echo $text_link; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'text_url' ); ?>"><?php _e( '&#8220;View More&#8221; Link URL:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'text_link' ); ?>" name="<?php echo $this->get_field_name( 'text_url' ); ?>" type="text" value="<?php echo $text_url; ?>" /></p>
<?php
	}
}