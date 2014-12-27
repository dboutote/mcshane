<?php
/**
 * The Template for displaying all single team member profiles
 *
 * @package WordPress
 * @subpackage McShane
 * @since McShane 1.0
 */
global $post;
$queried_profile = $post;
get_header(); ?>


<div class="breadcrumbs">

<div class="container clearfix">
<ul>
<li><a href="<?php echo esc_url( home_url() );?>">Home</a></li> / 
<li><a href="<?php echo get_permalink( get_page_by_path( 'contact-us' ) ) ?>"><?php _e('Contact Us'); ?></a></li> / 
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
	
		<?php 
		// post meta
		$postid = get_the_ID();
		$_address_street = get_post_meta($postid, '_address_street', true);
		$_address_city = get_post_meta($postid, '_address_city', true);
		$_address_state = get_post_meta($postid, '_address_state', true);
		$_address_zip = get_post_meta($postid, '_address_zip', true);
		$_office_phone = get_post_meta($postid, '_office_phone', true);
		$_office_fax = get_post_meta($postid, '_office_fax', true);
		$_office_email = get_post_meta($postid, '_office_email', true);
		$_contact_person = get_post_meta($postid, '_contact_person', true);
		$_contact_position = get_post_meta($postid, '_contact_position', true);
		$_contact_email = get_post_meta($postid, '_contact_email', true);

		?>	

		<h1>Contact Us</h1>
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
		
		
	</div><!-- /.right -->

</div> <!-- /.content -->
<div id="map-canvas"></div>

<?php
get_footer();