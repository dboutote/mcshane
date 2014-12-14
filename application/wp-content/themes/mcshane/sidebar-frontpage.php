<?php
/**
 * The Front Page Sidebar
 *
 * @package WordPress
 * @subpackage McShane
 * @since McShane 1.0
 */
?>

<div id="frontpage-sidebar" class="frontpage-sidebar-wrap widget-area" role="complementary">
		
	<?php if ( is_active_sidebar( 'sidebar-1' ) ) { ?>
		<div class="frontpage-sidebar-inner sidebar-1">
			<?php dynamic_sidebar( 'sidebar-1' ); ?>
		</div>
	<?php } ?>
		
	<?php if ( is_active_sidebar( 'sidebar-2' ) ) { ?>
		<div class="frontpage-sidebar-inner sidebar-2">
			<?php dynamic_sidebar( 'sidebar-2' ); ?>
		</div>
	<?php } ?>
	
</div><!-- /#frontpage-sidebar -->

