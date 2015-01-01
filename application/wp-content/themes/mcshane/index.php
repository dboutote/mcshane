<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme and one
 * of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query,
 * e.g., it puts together the home page when no home.php file exists.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage McShane
 * @since McShane 1.0
 */

global $post;

get_header(); ?>

<div class="breadcrumbs">

	<div class="container clearfix">

		<?php if ( function_exists( 'breadcrumb_trail' ) ) { ?>
			<?php breadcrumb_trail(array( 'container' => 'ul', 'show_browse' => false, 'separator'=>'/' )); ?>
		<?php } ?>

		<div class="right">
			<a href="javascript:window.print()" class="print"><img src="<?php echo get_stylesheet_directory_uri();?>/images/print.svg" /></a>
			<?php get_search_form(); ?>
		</div>

	</div> <!-- /.container -->

</div> <!-- /.breadcrumbs -->

<div class="content container clearfix">

	<div class="left">
		[hierarchal nav]
	</div> <!-- /.left -->

	<div class="right">

		<?php if( $post->post_parent > 0 ) { ?>
			<h1><?php echo get_post( $post->post_parent )->post_title;?></h1>
		<?php } ?>

		<?php while ( have_posts() ) : the_post(); ?>
			<h2><?php the_title();?></h2>
			<?php the_content();?>
			<?php edit_post_link( __( 'Edit', 'mcshane' ), '<span class="edit-link">', '</span>' );?>
		<?php endwhile; ?>

	</div> <!-- /.right -->

</div> <!-- /.content -->


<?php get_footer();