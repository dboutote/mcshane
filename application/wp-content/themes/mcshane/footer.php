<?php
/**
 * The template for displaying the footer
 *
 * Contains footer content
 *
 * @package WordPress
 * @subpackage McShane
 * @since McShane 1.0
 */
 
$hide_footer = ( ! is_front_page() ) ? true : false ; ?>

<footer role="contentinfo">

	<div class="container"> 
	
		<a href="#" class="contact-btn"><img src="<?php echo get_stylesheet_directory_uri();?>/images/contact-btn.svg" /></a>
				
		<?php if( $hide_footer ) { ?> <br /> <a href="#" class="footer-toggle btn">Read More</a> <?php } ?>
		
		<?php if( $hide_footer ) { ?> <div class="hidden-footer"> <?php } ?>
		
		
			<?php 
			$r = new WP_Query(
				array(
					'post_type'           => 'cpt_office',
					'posts_per_page'      => '-1',
					'no_found_rows'       => true,
					'post_status'         => 'publish',
					'ignore_sticky_posts' => true,
					'orderby'             => 'menu_order',
					'order'               => 'asc',
				)
			); ?>

			<?php if ($r->have_posts()) : ?>

				<h3>Have questions? Reach us at these offices:</h3>

				<div class="clearfix">

					<?php while ( $r->have_posts() ) : $r->the_post(); ?>
					
						<?php 
						$postid = get_the_ID();
						$_address_city = get_post_meta($postid, '_address_city', true); 
						$_address_state = get_post_meta($postid, '_address_state', true);
						$_office_phone = get_post_meta($postid, '_office_phone', true);
						?>
					
						<div class="top-col">
							<strong><?php the_title(); ?></strong> <br />
							<?php echo ( $_address_city ) ? $_address_city . ', ' : ''; ?>
							<?php echo ( $_address_state ) ? $_address_state . '<br />' : ''; ?>
							<?php echo ( $_office_phone ) ? $_office_phone : ''; ?>
						</div>

					<?php endwhile; ?>

				</div> <!-- /.clearfix -->
				
				<hr />
				
			<?php endif; ?>
			
			
			
			<div class="clearfix">
			
				<?php get_sidebar( 'footer' ); ?>
								
			</div> <!-- /.clearfix -->
			
		<?php if( $hide_footer ) { ?> </div> <!-- /.hidden-footer --> <?php }; ?>	
		
	</div> <!-- /.container -->
	
	<div class="bottom">
		<div class="container">
			<?php get_sidebar( 'footer-sub' ); ?>
		</div>
	</div>

</footer>

<?php wp_footer(); ?>
</body>
</html>