<?php
/**
 * The Header for our theme
 *
 * @package WordPress
 * @subpackage McShane
 * @since McShane 1.0
 */
?><!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) & !(IE 8)]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width">
<title><?php wp_title( '|', true, 'right' ); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<!--[if lt IE 9]>
	<script src="<?php echo get_template_directory_uri(); ?>/js/html5shiv-printshiv.min.js"></script>
	<script src="<?php echo get_template_directory_uri(); ?>/js/html5shiv.min.js"></script>
<![endif]-->
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<header role="banner">
	
	<div class="container clearfix">
	
		<nav>
		
			<div class="logo">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><img src="<?php echo get_stylesheet_directory_uri();?>/images/logo.svg"/></a>
			</div>
			
			<ul id="social">
				<li><a href="#"><img src="<?php echo get_stylesheet_directory_uri();?>/images/twitter.svg"/></a></li>
				<li><a href="#"><img src="<?php echo get_stylesheet_directory_uri();?>/images/newsletter.svg"/></a></li>
			</ul>
			
			<ul id="mobile-menu">
				<li><a href="#"><img src="images/mobile-menu.svg"/></a></li>
			</ul>
			
			<ul id="main">
				<?php 
				$menu_args = array(
					'container'=> false,
					'fallback_cb' => false,		
					'items_wrap' => '%3$s',					
					'theme_location' => 'primary'
				); ?>
				<?php wp_nav_menu($menu_args); ?>
			</ul>
			
		</nav>
		
	</div><!-- /.container -->
	
</header>
