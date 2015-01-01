<?php

namespace McShane;

/**
 * No direct access
 */
defined( 'ABSPATH' ) or die( 'Nothing here!' );


/**
 * Theme Functions Class
 */

class Theme_Functions
{

	const THEME_PREFIX = 'mcshane';

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
		add_action( 'wp_head', array($this,'show_favicon') );
		add_action( 'admin_menu', array($this, 'no_theme_customize') );
		add_action( 'init', array($this, 'register_theme_styles') );
		add_action( 'init', array($this, 'register_theme_scripts') );
		add_action( 'wp_enqueue_scripts', array($this, 'load_front_styles') );
		add_action( 'wp_enqueue_scripts', array($this, 'load_front_scripts') );
		add_action( 'widgets_init', array($this, 'widgets_init') );
		add_filter( 'wp_title', array($this, 'wp_title'), 10,2 );

		#add_action( 'wp_head', array($this, 'add_social_meta') );

		add_filter( 'nav_menu_css_class', array($this, 'nav_active_class'), 10 , 2);
		add_filter( 'post_gallery', array($this, 'post_gallery'), 0 , 2);
		add_filter( 'widget_text', array($this, 'filter_widget_text'), 0 , 2);
	}
	
	
	/**
	 * String Replace text in widgets
	 *
	 * filter the url for images
	 * 
	 * @access public
	 * @since McShane 1.0
	 */
	public function filter_widget_text($text)
	{
		$search = array(
			'<!-- theme_img_url -->'
		);
		
		$replace = array(
			get_stylesheet_directory_uri() . '/images'
		);
		
		$text = str_replace($search, $replace, $text);
		
		return $text;
	}	


	/**
	 * Override Gallery shortcode
	 * 
	 * @access public
	 * @since McShane 1.0
	 */
	public function post_gallery( $output, $attr )
	{

		if( ! isset($attr['tpl']) || empty($attr['tpl']) ){
			return $output;
		}

		$post = get_post();

		if ( ! empty( $attr['ids'] ) ) {
			// 'ids' is explicitly ordered, unless you specify otherwise.
			if ( empty( $attr['orderby'] ) ) {
				$attr['orderby'] = 'post__in';
			}
			$attr['include'] = $attr['ids'];
		}

		// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
		if ( isset( $attr['orderby'] ) ) {
			$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
			if ( ! $attr['orderby'] ) {
				unset( $attr['orderby'] );
			}
		}

		$html5 = current_theme_supports( 'html5', 'gallery' );

		$atts = shortcode_atts( array(
			'order'      => 'ASC',
			'orderby'    => 'menu_order ID',
			'id'         => $post ? $post->ID : 0,
			'itemtag'    => $html5 ? 'figure'     : 'dl',
			'icontag'    => $html5 ? 'div'        : 'dt',
			'captiontag' => $html5 ? 'figcaption' : 'dd',
			'columns'    => 3,
			'size'       => 'full',
			'include'    => '',
			'exclude'    => '',
			'link'       => ''
		), $attr, 'gallery' );

		$id = intval( $atts['id'] );

		if ( 'RAND' == $atts['order'] ) {
			$atts['orderby'] = 'none';
		}

		if ( ! empty( $atts['include'] ) ) {
			$_attachments = get_posts( array( 'include' => $atts['include'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );

			$attachments = array();
			foreach ( $_attachments as $key => $val ) {
				$attachments[$val->ID] = $_attachments[$key];
			}
		} elseif ( ! empty( $atts['exclude'] ) ) {
			$attachments = get_children( array( 'post_parent' => $id, 'exclude' => $atts['exclude'], 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );
		} else {
			$attachments = get_children( array( 'post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $atts['order'], 'orderby' => $atts['orderby'] ) );
		}

		if ( empty( $attachments ) ) {
			return '';
		}

		if ( is_feed() ) {
			$output = "\n";
			foreach ( $attachments as $att_id => $attachment ) {
				$output .= wp_get_attachment_link( $att_id, $atts['size'], true ) . "\n";
			}
			return $output;
		}


		// are we building the front page hero gallery?
		if( 'home-hero' == $attr['tpl'] ) {

			$output = '<ul class="hero-slider">';

				foreach ( $attachments as $id => $attachment ) {
					$image_obj = wp_get_attachment_image_src( $id, $atts['size'], false );
					$img_src = $image_obj[0];
					$iw = $image_obj[1];
					$ih = $image_obj[2];
					$image_meta  = wp_get_attachment_metadata( $id );

					$output .= '<li>';
						$output .= '<img src="'.$img_src.'" class="background-cover" width="'.$iw.'" height="'.$ih.'" alt="" /> <img src="' . get_template_directory_uri() . '/images/hero-shadow.png" class="hero-shadow" alt="" />';
						$output .= '<div class="container">';
							$output .= '<div class="slide-copy">' . wptexturize($attachment->post_excerpt) . '</div>';
						$output .= '</div>';
					$output .= '</li>';
				}

			$output .= '</ul>';

		}


		// are we building grid gallery?
		if( 'grid' == $attr['tpl'] ) {

			$output = ' <div class="gallery clearfix"><ul>';

				foreach ( $attachments as $id => $attachment ) {
					$image_obj = wp_get_attachment_image_src( $id, 'mcsh-gallery-thumb', false );
					$img_src = $image_obj[0];
					$iw = $image_obj[1];
					$ih = $image_obj[2];
					$output .= '<li>';
						$output .= '<a href="#"><img src="'.$img_src.'" class="background-cover" width="'.$iw.'" height="'.$ih.'" alt="" /></a>';
					$output .= '</li>';
				}

			$output .= '</ul></div>';

		}

		return $output;

	}


	/**
	 * Register Widget areas
	 *
	 * @access public
	 * @since McShane 1.0
	 */
	public function widgets_init()
	{

		register_sidebar( array(
			'name'          => __( 'Front Page Primary Sidebar', 'mcshane' ),
			'id'            => 'sidebar-1',
			'description'   => __( 'The first aside area below the homepage rotator', 'mcshane' ),
			'before_widget' => '',
			'after_widget'  => '',
			'before_title'  => '<h1 class="widget-title">',
			'after_title'   => '</h1>',
		) );

		register_sidebar( array(
			'name'          => __( 'Footer Sidebar: Left', 'mcshane' ),
			'id'            => 'sidebar-footer-left',
			'description'   => __( 'The left footer aside area.', 'mcshane' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		) );

		register_sidebar( array(
			'name'          => __( 'Footer Sidebar: Middle', 'mcshane' ),
			'id'            => 'sidebar-footer-middle',
			'description'   => __( 'The middle footer aside area.', 'mcshane' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		) );

		register_sidebar( array(
			'name'          => __( 'Footer Sidebar: Right', 'mcshane' ),
			'id'            => 'sidebar-footer-right',
			'description'   => __( 'The right footer aside area.', 'mcshane' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		) );

		register_sidebar( array(
			'name'          => __( 'Footer Sidebar: Sub', 'mcshane' ),
			'id'            => 'sidebar-footer-sub',
			'description'   => __( 'The sub-footer aside area.', 'mcshane' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		) );		

	}


	/**
	 * Add the Bootstrap "active" nav link class
	 *
	 * @access public
	 * @since McShane 1.0
	 */
	public function nav_active_class($classes, $item)
	{
		global $wp_query;
		$queried_object = $wp_query->get_queried_object();
		$queried_object_id = (int) $wp_query->queried_object_id;

		if( $item->object_id == $queried_object_id ){ $classes[] = "active"; }
		return $classes;
	}


	/**
	 * Retrieve the site's theme avatar
	 *
	 * Used for social media meta information
	 *
	 * @access public
	 * @since McShane 1.0
	 */
	protected function _get_site_avatar_url()
	{
		$theme_img_dir = apply_filters('theme_img_dir', 'img');
		$theme_avatar_name = apply_filters('theme_avatar_name', 'site_avatar.png');
		$avatar_url = '';

		$child_avatar_path = get_stylesheet_directory() . '/'. $theme_img_dir .'/' . $theme_avatar_name;
		if( file_exists( $child_avatar_path ) ) {
			$avatar_url = get_stylesheet_directory_uri() . '/'. $theme_img_dir .'/' . $theme_avatar_name;
		}

		if( '' === $avatar_url ){
			$parent_avatar_path = get_template_directory() . '/'. $theme_img_dir .'/' . $theme_avatar_name;
			if( file_exists( $parent_avatar_path ) ) {
				$avatar_url = get_template_directory_uri() . '/'. $theme_img_dir .'/' . $theme_avatar_name;
			}
		}

		return apply_filters('theme_avatar_url', $avatar_url);
	}


	/**
	 * Chunk a string an XX characters
	 *
	 * @access public
	 * @since McShane 1.0
	 */
	public function _abbreviate($text, $max = '95')
	{
		if ( strlen($text) <= $max ){
			return $text;
		}
		return substr($text, 0, $max-3) . '&#8230;';
	}


	/**
	 * Add Social meta tags
	 *
	 * @access public
	 * @since McShane 1.0
	 */
	public function add_social_meta()
	{

		global $wp_query;
		$meta_set = false;
		$meta = $pagelink = $pagetitle = $pagecontent = '';
		$avatar_url = $this->_get_site_avatar_url();

		// if we're on the front page or the blog index
		if( is_front_page() || is_home() ){
			$pagelink = site_url();
			$pagetitle = get_bloginfo('name');
			$pagecontent = get_bloginfo('description');
			$meta_set = true;
		}

		// if we're on a Page, a Post
		if( is_page() || is_singular('post') ){
			$pagelink = get_permalink( $wp_query->queried_object->ID );
			$pagetitle = $this->_abbreviate($wp_query->queried_object->post_title, '95');
			$pagecontent = ( '' != $wp_query->queried_object->post_excerpt ) ? $wp_query->queried_object->post_excerpt : $wp_query->queried_object->post_content;
			$meta_set = true;
		}

		if( is_page() ){
			$post_id = $wp_query->get_queried_object_id();
			$pagelink = home_url('?p=' . $post_id);
		}

		if( true === $meta_set ) {

			$pagetitle = wp_kses($pagetitle, $allowed_html=array());
			$pagetitle = esc_attr__($pagetitle);
			$pagecontent = wp_kses($pagecontent, $allowed_html=array());
			$pagecontent = $this->_abbreviate($pagecontent, '297');
			$pagecontent = esc_attr__($pagecontent);

			// facebook
			$meta .= "\n".'<meta property="og:type" content="website" />';
			$meta .= "\n".'<meta property="og:url" content="'.$pagelink.'" />';
			$meta .= "\n".'<meta property="og:title" content="'. $pagetitle .'" />';
			$meta .= "\n".'<meta property="og:description" content="'. $pagecontent .'" />';
			$meta .= "\n".'<meta property="og:image" content="'.$avatar_url.'" />';

			// twitter
			$meta .= "\n".'<meta name="twitter:card" content="summary" />';
			$meta .= "\n".'<meta name="twitter:url" content="'.$pagelink.'" />';
			$meta .= "\n".'<meta name="twitter:title" content="'. $pagetitle .'" />';
			$meta .= "\n".'<meta name="twitter:description" content="'.$pagecontent.'" />';
			$meta .= "\n".'<meta name="twitter:image:src" content="'.$avatar_url.'" />';
			$meta .= "\n";

		}


		echo $meta;
	}


	/**
	 * Filter the page title.
	 *
	 * Create a nicely formatted and more specific title element text for output
	 * in head of document, based on current view.
	 *
	 * @access public
	 * @since McShane 1.0
	 *
	 * @param string $title Default title text for current view.
	 * @param string $sep Optional separator.
	 * @return string The filtered title.
	 */
	public function wp_title( $title, $sep )
	{
		global $paged, $page;

		if ( is_feed() ) {
			return $title;
		}

		// Add the site name.
		$title .= get_bloginfo( 'name', 'display' );

		// Add the site description for the home/front page.
		$site_description = get_bloginfo( 'description', 'display' );
		if ( $site_description && ( is_home() || is_front_page() ) ) {
			$title = "$title $sep $site_description";
		}

		// Add a page number if necessary.
		if ( $paged >= 2 || $page >= 2 ) {
			$title = "$title $sep " . sprintf( __( 'Page %s', 'rinsight' ), max( $paged, $page ) );
		}

		return $title;
	}


	/**
	 * Load theme scripts
	 *
	 * @access public
	 * @since McShane 1.0
	 */
	public function load_front_scripts()
	{
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		wp_enqueue_script( self::THEME_PREFIX . '-main');

		#get_stylesheet_directory_uri
		wp_localize_script(
			self::THEME_PREFIX . '-main',
			'mc_scripts',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'theme_images_url' => get_stylesheet_directory_uri() . '/images/'
			)
		);
	}


	/**
	 * Load theme styles
	 *
	 * @access public
	 * @since McShane 1.0
	 */
	public function load_front_styles()
	{
		$font_url = $this->_get_font_url();

		if ( ! empty( $font_url ) ) {
			wp_enqueue_style( self::THEME_PREFIX .'-fonts', esc_url_raw( $font_url ), array(), null );
		}

		wp_enqueue_style( self::THEME_PREFIX . '-main' );

	}


	/**
	 * Register parent scripts
	 *
	 * @access public
	 * @since McShane 1.0
	 */
	public function register_theme_scripts()
	{

		wp_register_script(
			'google-maps',
			'https://maps.googleapis.com/maps/api/js?v=3.exp',
			array( 'jquery'),
			'',
			true
		);

		wp_register_script(
			'matchmedia',
			get_template_directory_uri()  . '/js/matchMedia.js',
			array( 'google-maps'),
			'1',
			true
		);

		wp_register_script(
			'buggyfill',
			get_template_directory_uri()  . '/js/viewport-units-buggyfill.js',
			array( 'matchmedia'),
			'0.4.1',
			true
		);

		wp_register_script(
			'backgroundcover',
			get_template_directory_uri()  . '/js/jquery.backgroundcover.min.js',
			array( 'buggyfill'),
			'2013',
			true
		);

		wp_register_script(
			'lightSlider',
			get_template_directory_uri()  . '/js/jquery.lightSlider.min.js',
			array( 'backgroundcover'),
			'1.1.1',
			true
		);

		wp_register_script(
			'featherlight',
			get_template_directory_uri()  . '/js/featherlight.min.js',
			array( 'lightSlider'),
			'1.0.1',
			true
		);


		wp_register_script(
			self::THEME_PREFIX . '-main',
			get_template_directory_uri()  . '/js/script.js',
			array('jquery', 'google-maps', 'buggyfill', 'backgroundcover', 'lightSlider', 'featherlight'),
			'1.0',
			true
		);

	}


	/**
	 * Register parent styles
	 *
	 * @access public
	 * @since McShane 1.0
	 */
	public function register_theme_styles()
	{

		$min = ( defined('WP_DEBUG') && WP_DEBUG ) ? '' : '.min';
		$min = '';

		wp_register_style(
			'lightSlider',
			get_template_directory_uri()  . '/css/lightSlider.css',
			array(),
			'1.0',
			'all'
		);

		wp_register_style(
			'featherLight',
			get_template_directory_uri()  . '/css/featherlight.min.css',
			array('lightSlider'),
			'1.0.1',
			'all'
		);

		wp_register_style(
			'master',
			get_template_directory_uri()  . '/css/style.css',
			array('featherLight'),
			'1.0',
			'all'
		);

		wp_register_style(
			self::THEME_PREFIX . '-main',
			get_template_directory_uri()  . '/style.css',
			array('master'),
			'1.0',
			'all'
		);

	}


	/**
	 * Register Google Font url
	 *
	 * @access protected
	 * @since McShane 1.0
	 */
	protected function _get_font_url()
	{
		$fonts_url = '';

		/* Translators: If there are characters in your language that are not
		 * supported by Fira Sans, translate this to 'off'. Do not translate into your
		 * own language.
		 */
		$fira_sans = _x( 'on', 'Fira Sans font: on or off', 'rinsight' );

		if ( 'off' !== $fira_sans ) {
			$font_families = array();

			if ( 'off' !== $fira_sans ) {
				$font_families[] = 'Fira Sans:300,500,700,300italic,500italic,700italic';
			}

			$query_args = array(
				'family' => urlencode( implode( '|', $font_families ) ),
				'subset' => urlencode( 'latin,latin-ext' ),
			);
			$fonts_url = add_query_arg( $query_args, "//fonts.googleapis.com/css" );
		}
		return $fonts_url;
	}


	/**
	 * Disable the Theme Customizer
	 *
	 * It's not needed for this theme.
	 *
	 * @access public
	 * @since McShane 1.0
	 *
	 * @uses wp_die()
	 */
	public function no_theme_customize()
	{
		global $pagenow;

		if( 'customize.php' === $pagenow ){
			wp_die(
				sprintf(
					__( 'The Theme Customizer is not compatible with your current theme: <strong>%s</strong>.', 'rinsight' ),
					wp_get_theme()
				),
				'',
				array('back_link' => true)
				);
		}
		remove_submenu_page( 'themes.php', 'customize.php?return=%2Fwp-admin%2Fthemes.php' );
	}


	/**
	 * Add the Site's Favicon to the site header
	*
	 * @access public
	 * @since McShane 1.0
	 */
	public function show_favicon()
	{
		$favicon_url = apply_filters('favicon_url', get_stylesheet_directory_uri().'/img/icon/favicon.ico');
		echo "\n".'<link rel="shortcut icon" href="'.$favicon_url.'" type="image/x-icon">';
		echo "\n".'<link rel="icon" href="'.$favicon_url.'" type="image/x-icon">';
	}

}

$Theme_Functions = new Theme_Functions();