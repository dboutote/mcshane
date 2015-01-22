<?php
/**
 * Template Name: News Gallery Page
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

		<?php while ( have_posts() ) : the_post(); ?>
		
			<h2><?php the_title();?></h2>

			<?php the_content();?>

		<?php endwhile; ?>

		<?php
		$_news_tax_terms = get_post_meta(get_the_ID(), '_news_tax_terms', true);
		$_p_type = get_post_meta(get_the_ID(), '_news_p_type', true);
		$_p_type = ( $_p_type ) ? $_p_type : 'post';
		$tax_query = array();

		if( '' !== $_news_tax_terms ){
			$meta_tax = explode(':', $_news_tax_terms);
			$selected_tax = $meta_tax[0];
			$selected_term = $meta_tax[1];
			$_term = get_term_by( 'slug', $selected_term, $selected_tax);
			$tax_query =  array(
				array(
					'taxonomy' => $selected_tax,
					'field'    => 'slug',
					'terms'    => $selected_term,
				)
			);
		};

		$r = new WP_Query(
			array(
				'post_type'           => $_p_type,
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

			<div class="gallery clearfix">

					<?php while ( $r->have_posts() ) : $r->the_post(); ?>

					<?php $_pdf_url = get_post_meta(get_the_ID(), '_pdf_url', true); ?>

						<hr />

						<div class="clearfix">

							<a href="<?php the_permalink();?>">
								<?php
								$img_src = $iw = $ih = '';
								if( has_post_thumbnail() ){
									$image_obj = wp_get_attachment_image_src( get_post_thumbnail_id(), 'thumbnail');
									$img_src = $image_obj[0];
									$iw = $image_obj[1];
									$ih = $image_obj[2];
								}; ?>
								<img src="<?php echo $img_src;?>" class="alignleft size-thumbnail" width="<?php echo $iw;?>" height="<?php echo $ih;?>" />
								<?php if( has_fullheader() ) { ?> <h3><?php display_fullheader(); ?></h3> <?php } ?>
							</a>

							<?php if( has_subheader() ) { ?> <p> <?php display_subheader();?></p> <?php } ?>
							<?php if( $_pdf_url ) { ?>
								<p><a href="<?php echo esc_url($_pdf_url); ?>"><img src="<?php echo get_stylesheet_directory_uri();?>/images/pdf_icon.gif" alt="pdf_icon" width="16" height="16" /> Download PDF</a></p>
							<?php } ?>

						</div>

					<?php endwhile; ?>



			</div> <!-- /.gallery -->

		<?php endif; ?>

		<?php wp_reset_postdata(); ?>

		<?php edit_post_link( __( 'Edit', 'mcshane' ), '<span class="edit-link">', '</span>' ); ?>

	</div><!-- /.right -->

</div> <!-- /.content -->

<?php get_footer();