<?php
/**
 * The template for displaying the footer
 *
 * Contains footer content and the closing of the #main and #page div elements.
 *
 * @package WordPress
 * @subpackage McShane
 * @since McShane 1.0
 */
 
$hide_footer = ( ! is_front_page() ) ? true : false ;
?>


<footer role="contentinfo">

	<div class="container"> 
	
		<a href="#" class="contact-btn"><img src="<?php echo get_stylesheet_directory_uri();?>/images/contact-btn.svg" /></a>
				
		<?php if( $hide_footer ) { ?> <br /> <a href="#" class="footer-toggle btn">Read More</a> <?php } ?>
		
		<?php if( $hide_footer ) { ?> <div class="hidden-footer"> <?php } ?>
		
			<h3>Have questions? Reach us at these offices:</h3>
			
			<div class="clearfix">
			
				<div class="top-col">
					<strong>Corporate Headquarters</strong> <br />
					Chicago, IL <br />
					847.692.8700
				</div>
				<div class="top-col">
					<strong>West Regional Office</strong><br />
					Irvine, CA<br />
					949.752.7646
				</div>
				<div class="top-col">
					<strong>Southwest Regional Office</strong><br />
					Pheonix, AZ<br />
					602.845.5200
				</div>
				<div class="top-col">
					<strong>Dallas Regional Office</strong><br/>
					Addison, TX<br />
					972.776.5455
				</div>
				<div class="top-col">
					<strong>Houston Regional Office</strong><br/>
					Houston, TX<br/>
					713.231.2995
				</div>
				
			</div> <!-- /.clearfix -->
			
			<hr />
			
			<div class="clearfix">
			
				<div class="left-col">
					<h4>Conor Commercial Connections</h4>
					<nav>
						<ul>
							<li><a href="#"><img src="<?php echo get_stylesheet_directory_uri();?>/images/footer-locations.svg" width="40" height="40"/><span>Locations / Office Maps</span></a></li>
							<li><a href="#"><img src="<?php echo get_stylesheet_directory_uri();?>/images/footer-team.svg" width="40" height="40"/><span>Team Members</span></a></li>
							<li><a href="#"><img src="<?php echo get_stylesheet_directory_uri();?>/images/footer-careers.svg" width="40" height="40"/><span>Careers</span></a></li>
							<li><a href="#"><img src="<?php echo get_stylesheet_directory_uri();?>/images/footer-awards.svg" width="40" height="40"/><span>Awards</span></a></li>
							<li><a href="#"><img src="<?php echo get_stylesheet_directory_uri();?>/images/footer-twitter.svg" width="40" height="40"/><span>Twitter</span></a></li>
						</ul>
					</nav>
				</div> <!-- /.left-col -->
				
				<div class="middle-col">
					<h4>Common Ground Newsletter</h4>
					<div class="clearfix">
						<a href="#"><img src="<?php echo get_stylesheet_directory_uri();?>/images/newsletter-thumb.jpg" class="alignleft newsletter-thumb"/></a>
						<h5>One Line Title Goes Here</h5>
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam commodo enim eu lectus imperdiet. Ut suscipit, dolor ac gravida ultricies, elementum aliquet enim nulla non.</p>
						<p>
							<a class="btn" href="#">Read More</a><br />
							<a class="btn" href="#">Subscribe to Newsletter</a>
						</p>
					</div>
				</div>  <!-- /.middle-col -->
				
				<div class="right-col">
					<h4>About Conor Commercial Real Estate</h4>
					<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam commodo enim eu lectus imperdiet porttitor. Ut suscipit, dolor ac gravida ultricies, neque justo viverra sapien, elementum aliquet enim nulla non sem. Nam venenatis, velit eu congue tempus, arcu leo interdum justo.</p>
					<p>Nunc iaculis leo a augue elementum placerat. Integer ac arcu ligula. Maecenas feugiat lacus a nulla sagittis aliquet. Fusce id dui sed enim tempus consectetur. Fusce felis ipsum, fermentum ut massa vel, aliquam euismod sem. Aenean pulvinar est luctus dui maximus, ac vulputate erat.</p>
				</div> <!-- /.right-col -->
				
			</div> <!-- /.clearfix -->
			
		<?php if( $hide_footer ) { ?> </div> <!-- /.hidden-footer --> <?php }; ?>	
		
	</div> <!-- /.container -->
	
	<div class="bottom">
		<div class="container">
			&copy; Conor Commercial Real Estate. All information contained herein is from sources deemed to be reliable. However, no representation is made to the accuracy thereof.
		</div>
	</div>

</footer>



<?php wp_footer(); ?>
</body>
</html>