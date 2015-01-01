<?php
/**
 * The Template for displaying all single cpt_office (Office) post types.
 *
 * @package WordPress
 * @subpackage McShane
 * @since McShane 1.0
 */
 
global $post;
$postid = get_the_ID();
$site_section = false;
$_site_section = get_post_meta($postid, '_site_section', true);
if($_site_section){
	$site_section = get_post($_site_section);
}

// office meta
$_address_street   = get_post_meta($postid, '_address_street', true);
$_address_city     = get_post_meta($postid, '_address_city', true);
$_address_state    = get_post_meta($postid, '_address_state', true);
$_address_zip      = get_post_meta($postid, '_address_zip', true);
$_office_phone     = get_post_meta($postid, '_office_phone', true);
$_office_fax       = get_post_meta($postid, '_office_fax', true);
$_office_email     = get_post_meta($postid, '_office_email', true);
$_contact_person   = get_post_meta($postid, '_contact_person', true);
$_contact_position = get_post_meta($postid, '_contact_position', true);
$_contact_email    = get_post_meta($postid, '_contact_email', true);
		
get_header(); ?>


<div class="breadcrumbs">

	<div class="container clearfix">
		<ul>
			<li><a href="<?php echo esc_url( home_url() );?>">Home</a></li>			
			<?php if( $site_section ) { ?>			
				/ <li><a href="<?php echo get_permalink( $site_section->ID ) ?>"><?php _e($site_section->post_title); ?></a></li>
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

		<h1><?php _e($site_section->post_title); ?></h1>
		
		<h2><?php the_title();?></h2>

		<?php while ( have_posts() ) : the_post(); ?>

			<blockquote class="clearfix">

				<div class="col">
					<h4>Office</h4>
					<p>
						<?php if($_address_street) { echo $_address_street . '<br />'; } ?>
						<?php if($_address_city) { echo $_address_city . ', '; } ?>
						<?php if($_address_state) { echo $_address_state . ' '; } ?>
						<?php if($_address_zip) { echo $_address_zip . '<br />'; } ?>
						<?php if($_office_phone) { echo 'P: ' . $_office_phone . '<br />'; } ?>
						<?php if($_office_fax) { echo 'F: ' . $_office_fax . '<br />'; } ?>
						<?php if($_office_email) { ?>
							<a href="mailto:<?php echo antispambot( $_office_email );?>"><?php echo antispambot( $_office_email );?></a>
						<?php } ?>
					</p>
				</div>

				<div class="col">
					<h4>Point of Contact</h4>
					<p>
						<?php if($_contact_person) { echo $_contact_person . '<br />'; } ?>
						<?php if($_contact_position) { echo $_contact_position . '<br />'; } ?>
						<?php if($_contact_email) { ?>
							<a href="mailto:<?php echo antispambot( $_contact_email );?>"><?php echo antispambot( $_contact_email );?></a>
						<?php } ?>
					</p>
				</div>

			</blockquote>

		<?php endwhile; ?>

		<?php edit_post_link( __( 'Edit', 'mcshane' ), '<span class="edit-link">', '</span>' ); ?>

	</div><!-- /.right -->

</div> <!-- /.content -->

<div id="map-canvas"></div>

<?php get_footer();