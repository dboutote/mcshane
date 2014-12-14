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
$post_type = get_post_type_object( get_post_type() );
get_header(); ?>


<div class="breadcrumbs">

<div class="container clearfix">
<ul>
<li><a href="#">Home</a></li>
<li><a href="#">[Gr. Parent: Page that matches the Post Type Label below]</a></li>
<li><a href="#">[Parent: Page that matches the Taxonomy Term name below]</a></li>
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
	<nav>
		<ul>
			<li class="active"><span class="toggle">+</span><a href="#">Gr. Parent (Team Members)</a>
				<ul>
					<li><span class="toggle">+</span><a href="#">Uncle (Exec Management)</a>
						<ul>
							<li><a href="#">Cousin</a></li>
							<li><a href="#">Cousin</a></li>
							<li><a href="#">Cousin</a></li>
						</ul>
					</li>
					<li class="active"><span class="toggle">+</span><a href="#">Parent (Financial/Legal)</a>
						<ul>
							<li class="active"><a href=""><?php echo apply_filters('the_title', $queried_profile->post_title); ?></a></li>
							<li><a href="#">Sibling</a></li>
							<li><a href="#">Sibling</a></li>
						</ul>
					</li>
					<li><span class="toggle">+</span><a href="#">Uncle (Development Team)</a>
						<ul>
							<li><a href="#">Cousin</a></li>
							<li><a href="#">Cousin</a></li>
							<li><a href="#">Cousin</a></li>
						</ul>
					</li>			
				</ul>
			</li>
			
	<li><span class="toggle">+</span><a href="#">Gr. Uncle (History)</a>
	<ul>
	<li><span class="toggle">+</span><a href="#">Second Level Link</a>
	<ul>
	<li><a href="#">Third Level Link</a></li>
	<li><a href="#">Third Level Link</a></li>
	</ul>
	</li>
	<li><span class="toggle">+</span><a href="#">Second Level Link</a>
	<ul>
	<li><a href="#">Third Level Link</a></li>
	<li><a href="#">Third Level Link</a></li>
	</ul>
	</li>
	<li><span class="toggle">+</span><a href="#">Second Level Link</a>
	<ul>
	<li><a href="#">Third Level Link</a></li>
	<li><a href="#">Third Level Link</a></li>
	</ul>
	</li>
	</ul>
	</li>
	<li><span class="toggle">+</span><a href="#">Gr. Uncle (Giving Back)</a>
	<ul>
	<li><span class="toggle">+</span><a href="#">Second Level Link</a>
	<ul>
	<li><a href="#">Third Level Link</a></li>
	</ul>
	</li>
	<li><span class="toggle">+</span><a href="#">Second Level Link</a>
	<ul>
	<li><a href="#">Third Level Link</a></li>
	<li><a href="#">Third Level Link</a></li>
	<li><a href="#">Third Level Link</a></li>
	</ul>
	</li>
	</ul>
	</li>
	</ul>
	</nav>
	</div><!-- /.left -->
	
	<div class="right">
	
		<?php 
		// post meta
		$postid = get_the_ID();
		$_job_title = get_post_meta($postid, '_job_title', true);
		$_job_phone = get_post_meta($postid, '_job_phone', true);
		$_job_email = get_post_meta($postid, '_job_email', true);
		$_vcard_url = get_post_meta($postid, '_vcard_url', true);
		$_prop_tag = get_post_meta($postid, '_prop_tag', true);
		?>	

		<h1>Gr. Parent [Post Type Label] <?php echo $post_type->labels->name;?></h1>

		<hr /> <br /> <br />

		<?php 
		$profile_terms = wp_get_object_terms( get_the_ID(),  'ctax_teamdepartment' );
		if( !is_wp_error($profile_terms) ) { ?>
			<h3>Parent [Taxonomy Term] <?php echo $profile_terms[0]->name; ?></h3>
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
				</div> <!-- /.team-page-left -->

				<div class="team-page-right">
					<div class="quote">
						<p>"Our team wanted to reach inside this corridor and help fill the coming demand for bay area distribution ahead of the curve. Hayward provided us that strategic point of market entry."</p>
						<p><em>— John Dobrott,<br>
						&emsp;President-Industrial Division</em></p>
					</div>
				</div> <!-- /.team-page-right -->

			</div> <!-- /.clearfix -->


		<?php endwhile; ?>
		
		<?php if($_prop_tag) { ?>
		
			<?php 
			$prop_tax = 'post_tag';
			$posttype = 'post';
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