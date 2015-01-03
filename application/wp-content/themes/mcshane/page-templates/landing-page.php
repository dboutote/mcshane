<?php
/**
 * Template Name: Top-level Landing Page
 *
 * @package WordPress
 * @subpackage McShane
 * @since McShane 1.0
 */

global $post;

// post meta
$postid = get_the_ID();
$bg_images_arr = '';
$_bg_images = get_post_meta($postid, '_bg_images', true);
if( $_bg_images ){
	$bg_images_arr = explode( "\n", $_bg_images );
}

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

	<div class="bg-slider">
		<?php if( is_array($bg_images_arr) ) { ?>
			<ul>
				<?php foreach($bg_images_arr as $bg_img_url ) {
					$img_size = @getimagesize(trim($bg_img_url));
					if( $img_size ) {
						$iw = $img_size[0];
						$ih = $img_size[1]; ?>
						<li><img src="<?php echo esc_url($bg_img_url); ?>" class="background-cover" width="<?php echo $iw;?>" height="<?php echo $ih;?>" alt=""/> <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/landing-shadow.png" class="landing-shadow" alt=""/> </li>
					<?php }; ?>
				<?php }; ?>
			</ul>
		<?php }; ?>
	</div> <!-- /.bg-slider -->

	<div class="left">
		<?php 
		$child_pages = get_children_pages( get_top_ancestor_id() );
				
		if( !empty($child_pages) ) {
			$walker = new Walker_SideNav;
			$args = array($child_pages, 0);
			echo '<nav><ul>';
			echo call_user_func_array(array($walker, 'walk'), $args);
			echo '</ul></nav>';
		} ?>
		&nbsp;
	</div> <!-- /.left -->

	<div class="right">

		<div class="landing-content">
				
			<?php while ( have_posts() ) : the_post(); ?>

				<h2><?php the_title();?></h2>

				<?php the_content();?>

			<?php endwhile; ?>

		</div><!-- /.landing-content -->

		<?php edit_post_link( __( 'Edit', 'mcshane' ), '<span class="edit-link">', '</span>' ); ?>

	</div><!-- /.right -->

</div> <!-- /.content -->

<?php get_footer();