<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other 'pages' on your WordPress site will use a different template.
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