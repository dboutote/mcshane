<?php
/**
 * Template Name: Grid Page
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
	
		<?php while ( have_posts() ) : the_post(); ?>
		
			<h2><?php the_title();?></h2>
			
			<?php the_content();?>
			
		<?php endwhile; ?>

		<?php edit_post_link( __( 'Edit', 'mcshane' ), '<span class="edit-link">', '</span>' ); ?>

	</div><!-- /.right -->

</div> <!-- /.content -->

<?php get_footer();