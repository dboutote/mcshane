<?php
/**
 * The Template for displaying all single team member profiles
 *
 * @package WordPress
 * @subpackage McShane
 * @since McShane 1.0
 */

global $post;
$postid = get_the_ID();
$_tax = 'ctax_milestonetype';

$_terms = wp_get_object_terms( $postid,  $_tax );
$parent_tax_name = ( !is_wp_error($_terms) && count($_terms) > 0 ) ? $_terms[0]->name : '' ;
$parent_tax_slug = ( !is_wp_error($_terms) && count($_terms) > 0 ) ? $_terms[0]->slug : '' ;

$site_section = false;
$_site_section = get_post_meta($postid, '_site_section', true);
if($_site_section){
	$site_section = get_post($_site_section);
}

$_site_sub_section = get_post_meta($postid, '_site_sub_section', true);
$parent_name = '';
if( $_site_sub_section ){
	$parent_name = get_post($_site_sub_section)->post_title;
	$ancestors = array_reverse(get_post_ancestors($_site_sub_section));
	$ancestors[] = $_site_sub_section;
}

get_header(); ?>

<div class="breadcrumbs">

	<div class="container clearfix">

		<ul>
			<li><a href="<?php echo esc_url( home_url() );?>">Home</a></li>
			<?php if( !empty($ancestors) ) {
				foreach( $ancestors as $k => $id ) {
					$p = get_post($id);
					echo ' / <li><a href="' . get_permalink($id) .'" >' . $p->post_title . '</a></li>';
				};
			} ?>
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
		<?php
		$child_pages = get_children_pages( $_site_section );

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

		<h1><?php _e($parent_name); ?></h1>

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