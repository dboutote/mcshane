<?php
/**
 * The template for displaying 404 pages (Not Found)
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */

get_header();

global $wp_query;

debug($wp_query);
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

		<h1><?php _e('Not Found'); ?></h1>
		
			
	</div> <!-- /.right -->

</div> <!-- /.content -->

<?php
get_footer();