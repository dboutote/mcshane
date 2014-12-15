<?php
/**
 * Template Name: Top-level Landing Page
 *
 * @package WordPress
 * @subpackage McShane
 * @since McShane 1.0
 */

get_header(); ?>

<div class="breadcrumbs">

<div class="container clearfix">
	<?php if ( function_exists( 'breadcrumb_trail' ) ) { ?>
		<?php breadcrumb_trail(array( 'container' => 'ul', 'show_browse' => false, 'separator'=>'' )); ?>
	<?php } ?>

<div class="right"> <a href="javascript:window.print()" class="print"><img src="<?php echo get_stylesheet_directory_uri();?>/images/print.svg"/></a>
<form class="search">
<input type="text" placeholder="Search"/>
<input type="submit" value=""/>
</form>
</div>

</div> <!-- /.container -->
</div> <!-- /.breadcrumbs -->


<div class="content container clearfix">

	<?php 
	// post meta
	$postid = get_the_ID();
	$bg_images_arr = '';
	$_bg_images = get_post_meta($postid, '_bg_images', true);		
	if( $_bg_images ){
		$bg_images_arr = explode( "\n", $_bg_images );			
	}
	?>

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
		[children nav]
		<!-- hierarchal navigation -->
	</div> <!-- /.left -->

	<div class="right">
		<div class="landing-content">
			<?php while ( have_posts() ) : the_post(); ?>	
				<h2><?php the_title();?></h2>
				<?php the_content();?>				
			<?php endwhile; ?>
		</div><!-- /.landing-content -->
	</div> <!-- /.right -->


</div> <!-- /.content -->




<?php
get_footer();