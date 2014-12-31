<?php
/**
 * The Template for displaying all single team member profiles
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
get_header(); ?>


<div class="breadcrumbs">

<div class="container clearfix">

	<ul>
		<li><a href="<?php echo esc_url( home_url() );?>">Home</a></li>
		<?php if( $site_section ) { ?>
			/ 
			<li><a href="<?php echo get_permalink( $site_section->ID ) ?>"><?php _e($site_section->post_title); ?></a></li> 
			/ 
			<li><a href="<?php echo get_permalink( get_page_by_path( $site_section->post_name . '/' . sanitize_title($post_type->labels->name) ) ); ?>"><?php _e( $post_type->labels->name ); ?></a></li>
			<?php if('' !== $parent_tax_slug ) { ?>
				/ 
				<li><a href="<?php echo get_permalink( get_page_by_path( $site_section->post_name . '/' . sanitize_title($post_type->labels->name) . '/' . $parent_tax_slug ) ); ?>"><?php _e($parent_tax_name); ?></a></li>
			<?php }; ?>
		<?php }; ?>
		/ <li><?php the_title();?></li>
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
	</div> <!-- /.left -->
	
	<div class="right">
	
		<?php 
		// post meta
		$_property_location = get_post_meta($postid, '_property_location', true);
		$_job_phone = get_post_meta($postid, '_job_phone', true);
		$_job_email = get_post_meta($postid, '_job_email', true);
		$_vcard_url = get_post_meta($postid, '_vcard_url', true);
		$_prop_tag = get_post_meta($postid, '_prop_tag', true);
		$_feat_quote = get_post_meta($postid, '_feat_quote', true);
		$_feat_quote_author = get_post_meta($postid, '_feat_quote_author', true);
		$_feat_quote_author_title = get_post_meta($postid, '_feat_quote_author_title', true);
		?>	

		<h1><?php echo $post_type->labels->name;?></h1>

		<hr /> 
		
		<br />
		<br />

		<?php
		if( '' !== $parent_tax_name ) { ?>
			<h3><?php _e($parent_tax_name); ?></h3>
		<?php } ?>

		<?php while ( have_posts() ) : the_post(); ?>

			<div class="contact-hero clearfix">
			
				<div class="team-page-left">
					<?php if( has_post_thumbnail() ) {
						$image_obj = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full');
						$img_src = $image_obj[0];
						$img_width = $image_obj[1];
						$img_height = $image_obj[2]; ?>
						<img src="<?php echo $img_src; ?>" width="<?php echo $img_width;?>" height="<?php echo $img_height;?>" class="background-cover" />
					<?php }; ?>
				</div> <!-- /.team-page-left -->

				<div class="team-page-right-no-margin">
					<h3><?php the_title();?></h3>
					<?php if($_job_title) { ?> <h4><?php echo $_job_title; ?></h4> <?php } ?>
					<p>
						<?php if($_job_phone) { echo $_job_phone . '<br />'; } ?>				
						<?php if($_job_email) { echo antispambot( $_job_email );  } ?>
					</p>
					<?php if($_vcard_url) {?>
						<p><a href="<?php echo esc_url($_vcard_url);?>" class="vcard">Download vCard</a></p>
					<?php } ?>			
				</div> <!-- /.team-page-right -->

			</div> <!-- /.contact-hero -->		

			<div class="clearfix">

				<div class="team-page-left">
				
					<blockquote>
						<?php echo wpautop( get_the_excerpt() );?>
						<p><a class="accordion-toggle btn" href="#">Read More</a></p>
						<div class="accordion-content">
							<?php the_content(); ?>						
						</div>
					</blockquote>
					
					<?php edit_post_link( __( 'Edit', 'mcshane' ), '<span class="edit-link">', '</span>' );?>
					
				</div> <!-- /.team-page-left -->

				<div class="team-page-right">
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
				</div> <!-- /.team-page-right -->

			</div> <!-- /.clearfix -->


		<?php endwhile; ?>
		
		<?php if($_prop_tag) { ?>
		
			<?php 
			$prop_tax = 'ctax_proptag';
			$posttype = 'cpt_property';
			$prop_qty = 10;
			$tax_query =  array(
				array(
					'taxonomy' => $prop_tax,
					'field'    => 'slug',
					'terms'    => $_prop_tag,
				)
			);	
			$r = new WP_Query(
				array(
					'post_type'           => $posttype,
					'posts_per_page'      => $prop_qty,
					'no_found_rows'       => true,
					'post_status'         => 'publish',
					'ignore_sticky_posts' => true,
					'tax_query'           => $tax_query
				)
			);
		
			if( $r->have_posts() ) { ?>
			
				<h3>Representative Experience</h3>
				
				<div class="experience">
					<ul>
						<?php while ( $r->have_posts() ) : $r->the_post(); ?>
							<?php 
							$img_src = $iw = $ih = '';
							if( has_post_thumbnail() ){
								$image_obj = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full');
								$img_src = $image_obj[0];
								$iw = $image_obj[1];
								$ih = $image_obj[2];
							}; ?>
							<li>
								<a href="<?php the_permalink(); ?>">
									<img src="<?php echo $img_src;?>" class="background-cover" width="<?php echo $iw;?>" height="<?php echo $ih;?>" alt="" />
									<span class="title"><?php the_title(); ?></span>
								</a>
							</li>
						<?php endwhile; ?>
					</ul>
				</div>

			<?php }; ?>
		
			<?php wp_reset_postdata(); ?>
		
		<?php }; ?>
		
	</div><!-- /.right -->

</div> <!-- /.content -->


<?php
get_footer();