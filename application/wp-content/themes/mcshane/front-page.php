<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other 'pages' on your WordPress site will use a different template.
 *
 * @package WordPress
 * @subpackage RealInsight
 * @since RealInsight 1.0
 */

get_header(); ?>

<div id="home-hero">

	<?php while ( have_posts() ) : the_post(); ?>
		<?php the_content(); ?>
	<?php endwhile; ?>
	
	<div class="container">
		<div class="quick-links">Learn more about:
			<?php  
			$secondary_menu_args = array(					
				'container'=> false,
				'fallback_cb' => false,		
				'items_wrap' => '%3$s',
				'theme_location' => 'quicklinks'
			);  ?>				
			<ul>				
				<?php wp_nav_menu($secondary_menu_args); ?>
			</ul>
		</div>
	</div> <!-- /.container -->
	
</div> <!-- /#home-hero -->

<div class="content container">
	
	<?php get_sidebar( 'frontpage' ); ?>
	
</div> <!-- /.content -->

<?php get_footer();