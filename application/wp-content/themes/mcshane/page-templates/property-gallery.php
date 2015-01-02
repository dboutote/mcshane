<?php
/**
 * Template Name: Property Gallery Page
 * 
 * Builds page of content categorized with the selected taxonomy term.
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

		<?php if( $post && $post->post_parent > 0 ) { ?>

			<h1><?php echo get_post( $post->post_parent )->post_title;?></h1>
	
			<hr />	

		<?php } ?>
			
		<?php while ( have_posts() ) : the_post(); ?>
			
			<?php the_content();?>	
			
		<?php endwhile; ?>

		<?php
		$tax_query = array();		
		$_type_terms = get_post_meta(get_the_ID(), '_property_type', true);
		if( $_type_terms ){
			$_selected_type = explode(':', $_type_terms);
			$_selected_type_tax = $_selected_type[0];
			$_selected_type_term = $_selected_type[1];
			
			$tax_query[] = array(
				'taxonomy' => $_selected_type_tax,
				'field'    => 'slug',
				'terms'    => $_selected_type_term,
			);
		}

		$_tag_terms = get_post_meta(get_the_ID(), '_property_tag', true);
		if( $_tag_terms ){
			$_selected_tag = explode(':', $_tag_terms);
			$_selected_tag_tax = $_selected_tag[0];
			$_selected_tag_term = $_selected_tag[1];
			
			$tax_query[] = array(
				'taxonomy' => $_selected_tag_tax,
				'field'    => 'slug',
				'terms'    => $_selected_tag_term,
			);
		}
		
		$r = new WP_Query(
			array(
				'post_type'           => 'cpt_property',
				'posts_per_page'      => '-1',
				'no_found_rows'       => true,
				'post_status'         => 'publish',
				'ignore_sticky_posts' => true,
				'orderby'             => 'menu_order',
				'order'               => 'asc',
				'tax_query'           => $tax_query,
			)
		); ?>

		<?php if ($r->have_posts()) : ?>

			<h3><?php the_title(); ?></h3>

			<div class="gallery clearfix">
			
				<ul>

					<?php while ( $r->have_posts() ) : $r->the_post(); ?>

						<li>
							<a href="<?php the_permalink();?>">
								<?php
								$img_src = $iw = $ih = '';
								if( has_post_thumbnail() ){
								$image_obj = wp_get_attachment_image_src( get_post_thumbnail_id(), 'mcsh-gallery-thumb');
								$img_src = $image_obj[0];
								$iw = $image_obj[1];
								$ih = $image_obj[2];
								}; ?>
								<img src="<?php echo $img_src;?>" class="background-cover" width="<?php echo $iw;?>" height="<?php echo $ih;?>" />
								<span class="title">
									<strong><?php the_title();?></strong>
									<?php $_property_location = get_post_meta(get_the_ID(), '_property_location', true); ?>
									<?php if($_property_location) { ?> <br /> <?php echo $_property_location; }; ?>
								</span>
							</a>
						</li>

					<?php endwhile; ?>

				</ul>
				
			</div> <!-- /.gallery -->

		<?php endif; ?>

		<?php wp_reset_postdata(); ?>
		
		<?php edit_post_link( __( 'Edit', 'mcshane' ), '<span class="edit-link">', '</span>' ); ?>

	</div><!-- /.right -->

</div> <!-- /.content -->

<?php get_footer();