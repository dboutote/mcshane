<?php
/**
 * The Template for displaying all single team member profiles
 *
 * @package WordPress
 * @subpackage McShane
 * @since McShane 1.0
 */
global $post;
get_header(); ?>


<div class="breadcrumbs">

<div class="container clearfix">
<ul>
<li><a href="<?php echo esc_url( home_url() );?>">Home</a></li> /
<li><a href="<?php echo get_permalink( get_page_by_path( 'about-us' ) ) ?>"><?php _e('About Us'); ?></a></li> /
<li><a href="<?php echo get_permalink( get_page_by_path( 'about-us/history') ) ?>"><?php _e('History'); ?></a> / </li>
<li><?php the_title();?></li>
</ul>

<div class="right"> <a href="javascript:window.print()" class="print"><img src="<?php echo get_stylesheet_directory_uri();?>/images/print.svg"/></a>
<form class="search">
<input type="text" placeholder="Search"/>
<input type="submit" value=""/>
</form>
</div>

</div>

</div> <!-- /.breadcrumbs -->


<div class="content container clearfix">

	<div class="left">
		[hierarchal nav]
		<!-- hierarchal navigation -->
	</div> <!-- /.left -->

	<div class="right">

		<h1>History</h1>

		<hr />
		<br />
		<br />

		<div class="clearfix">
			<h3><?php the_title(); ?></h3>
		</div>

		<?php while ( have_posts() ) : the_post(); ?>

			<div class="hero">
				<?php if( has_post_thumbnail() ) {
					$image_obj = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full');
					$img_src = $image_obj[0];
					$img_width = $image_obj[1];
					$img_height = $image_obj[2]; ?>
					<img src="<?php echo $img_src; ?>" width="<?php echo $img_width;?>" height="<?php echo $img_height;?>" class="background-cover" />
				<?php }; ?>
			</div> <!-- /.hero -->

			 <blockquote class="property-content">
				<?php if( '' !== $post->post_excerpt ) { ?>
					<?php echo wpautop( $post->post_excerpt );?>
					<p><a class="accordion-toggle btn" href="#">Read More</a></p>
					<div class="accordion-content">
						<?php the_content(); ?>
					</div>					
				<?php } else { ?>
					<?php the_content(); ?>
				<?php } ?>
				
			</blockquote>

		<?php endwhile; ?>

	</div><!-- /.right -->

</div> <!-- /.content -->


<?php
get_footer();