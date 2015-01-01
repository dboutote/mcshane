<?php
/**
 * The Template for displaying all single cpt_property (Property) post types.
 *
 * @package WordPress
 * @subpackage McShane
 * @since McShane 1.0
 */

global $post;
$post_type = get_post_type_object( get_post_type() );
$postid = get_the_ID();
$_tax = 'ctax_proptype';

$_terms = wp_get_object_terms( $postid,  $_tax );
$parent_tax_name = ( !is_wp_error($_terms) && count($_terms) > 0 ) ? $_terms[0]->name : '' ;
$parent_tax_slug = ( !is_wp_error($_terms) && count($_terms) > 0 ) ? $_terms[0]->slug : '' ;
$site_section = false;
$_site_section = get_post_meta($postid, '_site_section', true);
if($_site_section){
	$site_section = get_post($_site_section);
}

// property meta
$_property_location       = get_post_meta($postid, '_property_location', true);
$_sub_header              = get_post_meta($postid, '_sub_header', true);
$_feat_quote              = get_post_meta($postid, '_feat_quote', true);
$_feat_quote_author       = get_post_meta($postid, '_feat_quote_author', true);
$_feat_quote_author_title = get_post_meta($postid, '_feat_quote_author_title', true);
$_project_url             = get_post_meta($postid, '_project_url', true);
$_project_url_text        = get_post_meta($postid, '_project_url_text', true);
$_gallery_title           = get_post_meta($postid, '_gallery_title', true);

// property tags
$is_available = $is_representative = false;
$_tag_terms = wp_get_object_terms( $postid,  'ctax_proptag' );
if( !is_wp_error($_tag_terms) && count($_tag_terms) > 0 ){

	foreach ( $_tag_terms as $_tag_term) {
		if( 'available' == $_tag_term-> slug ) {
			$is_available = true;
		}
		if( 'representative' == $_tag_term-> slug ) {
			$is_representative = true;
		}
	}

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
					<?php if( $is_available ) { ?>
						/ <li><a href="<?php echo get_permalink( get_page_by_path( $site_section->post_name . '/' . sanitize_title($parent_tax_slug) . '/available-properties' ) ); ?>"><?php _e('Available Properties'); ?></a></li>
					<?php }; ?>
					<?php if( $is_representative ) { ?>
						/ <li><a href="<?php echo get_permalink( get_page_by_path( $site_section->post_name . '/' . sanitize_title($parent_tax_slug) . '/representative-projects' ) ); ?>"><?php _e('Representative Projects'); ?></a></li>
					<?php }; ?>
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
			<?php if( has_subheader( $postid ) ) { ?> <h3><?php display_subheader( $postid );?></h3> <?php } ?>
			<?php if( '' !== $_property_location ) { ?>
				<div class="location"><?php _e($_property_location); ?></div>
			<?php } ?>
		</div>

		<?php while ( have_posts() ) : the_post(); ?>

			<div class="hero">
				<?php
				$attachments = get_children(
					array(
						'post_parent' => $postid,
						'post_status' => 'inherit',
						'post_type' => 'attachment',
						'post_mime_type' => 'image',
					)
				);

				if ( !empty( $attachments ) ) {

					$output = '<ul id="property-slides">';

					foreach ( $attachments as $id => $attachment ) {
						$image_obj = wp_get_attachment_image_src( $id, 'full', false );
						$img_src = $image_obj[0];
						$iw = $image_obj[1];
						$ih = $image_obj[2];

						$output .= '<li>';
							$output .= '<img src="'.$img_src.'" class="background-cover" width="'.$iw.'" height="'.$ih.'" class="background-cover" alt="" />';
						$output .= '</li>';
					}
					$output .= '</ul>';
					echo $output;
				}; ?>
				<?php if( $_gallery_title ) { ?> <div class="title"><?php _e($_gallery_title); ?></div> <?php }; ?>
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

			<div class="clearfix">

				<div class="property-page-left">

					<?php if ( !empty( $attachments ) ) {  ?>

						<h3>Project Photos</h3>
						<div class="property-gallery">
							<?php
							$output = '<ul class="slider">';
							foreach ( $attachments as $id => $attachment ) {
								$image_obj = wp_get_attachment_image_src( $id, 'full', false );
								$img_src = $image_obj[0];
								$iw = $image_obj[1];
								$ih = $image_obj[2];

								$output .= '<li>';
									$output .= '<img src="'.$img_src.'" class="background-cover" width="'.$iw.'" height="'.$ih.'" class="background-cover" alt="" />';
								$output .= '</li>';
							}
							$output .= '</ul>';
							echo $output;
							?>
						</div>
					<?php }; ?>

					<?php wp_reset_postdata(); ?>

					<?php if( $_project_url ) : ?>
						<iframe class="web-box" width="950" height="700" src="<?php echo esc_url($_project_url); ?>"></iframe>
						<h3>Launch: <a href="#" data-featherlight=".web-box"><?php echo ( $_project_url_text ) ? $_project_url_text : $_project_url; ?></a></h3>
					<?php endif; ?>

				</div> <!-- /.property-page-left -->

				<div class="property-page-right">
					<?php if($_feat_quote) { ?>
					<div class="quote">
						<p>&#8220;<?php _e($_feat_quote); ?>&#8221;</p>
						<?php if($_feat_quote_author) { ?>
							<p><em>&#8212;
								<?php echo $_feat_quote_author; ?><?php if($_feat_quote_author_title) { echo ',<br />&emsp;' . $_feat_quote_author_title; }; ?>
							</em></p>
						<?php }; ?>
					</div>
					<?php } ?>
				</div>

			</div> <!-- /.clearfix -->

		<?php endwhile; ?>

		<?php edit_post_link( __( 'Edit', 'mcshane' ), '<span class="edit-link">', '</span>' ); ?>

	</div><!-- /.right -->

</div> <!-- /.content -->

<?php get_footer();