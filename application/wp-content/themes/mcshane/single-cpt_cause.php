<?php
/**
 * The Template for displaying all single cpt_cause (Cause) post types.
 *
 * @package WordPress
 * @subpackage McShane
 * @since McShane 1.0
 */
 
global $post;
$postid = get_the_ID();
$_tax = 'ctax_causetype';

$_terms = wp_get_object_terms( $postid,  $_tax );
$parent_tax_name = ( !is_wp_error($_terms) && count($_terms) > 0 ) ? $_terms[0]->name : '' ;
$parent_tax_slug = ( !is_wp_error($_terms) && count($_terms) > 0 ) ? $_terms[0]->slug : '' ;
$site_section = false;
$_site_section = get_post_meta($postid, '_site_section', true);
if($_site_section){
	$site_section = get_post($_site_section);
}

get_header(); ?>

<div class="breadcrumbs">

	<div class="container clearfix">
		<ul>
			<li><a href="<?php echo esc_url( home_url() );?>">Home</a></li>			
			<?php if( $site_section ) { ?>			
				/ <li><a href="<?php echo get_permalink( $site_section->ID ) ?>"><?php _e($site_section->post_title); ?></a></li>
				<?php if('' !== $parent_tax_slug ) { ?>
					/ <li><a href="<?php echo get_permalink( get_page_by_path( $site_section->post_name . '/' . sanitize_title($parent_tax_slug) ) ); ?>"><?php _e($parent_tax_name); ?></a></li>
				<?php }; ?>
			<?php }; ?>
			/ <li><?php the_title();?></li>
		</ul>

		<div class="right"> 
			<a href="javascript:window.print()" class="print"><img src="<?php echo get_stylesheet_directory_uri();?>/images/print.svg" /></a>
			<?php get_search_form(); ?>
		</div>

	</div>

</div> <!-- /.breadcrumbs -->

<div class="content container clearfix">

	<div class="left">
		[hierarchal nav]
	</div> <!-- /.left -->

	<div class="right">

		<h1><?php _e($parent_tax_name); ?></h1>

		<hr />
		
		<br />
		<br />

		<div class="clearfix">
			<h3><?php the_title(); ?></h3>
			<?php if( has_subheader( $postid ) ) { ?> <br /> <?php display_subheader( $postid ); } ?>
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
					<?php echo wpautop( $post->post_excerpt ); ?>
					<p><a class="accordion-toggle btn" href="#">Read More</a></p>
					<div class="accordion-content">
						<?php the_content(); ?>
					</div>					
				<?php } else { ?>
					<?php the_content(); ?>
				<?php } ?>
			</blockquote>
			
		<?php endwhile; ?>

		<?php edit_post_link( __( 'Edit', 'mcshane' ), '<span class="edit-link">', '</span>' ); ?>

	</div><!-- /.right -->

</div> <!-- /.content -->

<?php get_footer();