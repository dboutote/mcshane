<?php
/**
 * Custom Template for the Team Members page
 *
 * @package WordPress
 * @subpackage McShane
 * @since McShane 1.0
 */

get_header();

global $post;
?>


<div class="breadcrumbs">

	<div class="container clearfix">
		<?php if ( function_exists( 'breadcrumb_trail' ) ) { ?>
			<?php breadcrumb_trail(array( 'container' => 'ul', 'show_browse' => false, 'separator'=>'/' )); ?>
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

	<div class="left">
		[hierarchal nav]
		<!-- hierarchal navigation -->
	</div> <!-- /.left -->

	<div class="right">

		<?php while ( have_posts() ) : the_post(); ?>
			<h1><?php the_title();?></h1>
			<hr />
			<?php the_content();?>
		<?php endwhile; ?>
		
		<?php
		$_taxonomy 	= 'ctax_teamdepartment';
		$_terms = get_terms( $_taxonomy );
		
		// sort by taxonomy menu order
		usort($_terms, function($a, $b) {
			return $a->menu_order - $b->menu_order;
		});
			
		foreach( $_terms as $_term ) :

			$tax_query =  array(
				array(
					'taxonomy' => $_taxonomy,
					'field'    => 'slug',
					'terms'    => $_term->slug,
				)
			);			

			$r = new WP_Query(
				array(
					'post_type'           => 'cpt_profile',
					'posts_per_page'      => '-1',
					'no_found_rows'       => true,
					'post_status'         => 'publish',
					'ignore_sticky_posts' => true,
					'orderby'             => 'menu_order',
					'order'               => 'asc',
					'tax_query'           => $tax_query,
				)
			)?>

			<?php if ($r->have_posts()) : ?>

				<h3><?php echo $_term->name;?></h3>

				<div class="gallery clearfix">
					<ul>

						<?php while ( $r->have_posts() ) : $r->the_post(); ?>

							<li>
								<a href="<?php the_permalink();?>">
									<?php
									$img_src = $iw = $ih = '';
									if( has_post_thumbnail() ){
									$image_obj = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full');
									$img_src = $image_obj[0];
									$iw = $image_obj[1];
									$ih = $image_obj[2];
									}; ?>
									<img src="<?php echo $img_src;?>" class="background-cover" width="<?php echo $iw;?>" height="<?php echo $ih;?>" />
									<span class="title">
										<strong><?php the_title();?></strong>
										<br />
										<?php if( has_subheader() ) { display_subheader(); } ?>
									</span>
								</a>
							</li>

						<?php endwhile; ?>

					</ul>
				</div> <!-- /.gallery -->

			<?php endif; ?>
			
			<?php wp_reset_postdata(); ?>
			
		<?php endforeach; ?>

			
		
		
		
	</div> <!-- /.right -->

</div> <!-- /.content -->

<?php
get_footer();