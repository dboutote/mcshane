<?php
/**
 * The Footer Sidebar
 *
 * @package WordPress
 * @subpackage McShane
 * @since McShane 1.0
 */
?>

<div id="footer-sidebar" class="footer-sidebar-wrap widget-area" role="complementary">

	<?php if ( is_active_sidebar( 'sidebar-footer-left' ) ) { ?>
		<div class="footer-sidebar-inner sidebar-footer-left left-col">
			<?php dynamic_sidebar( 'sidebar-footer-left' ); ?>
		</div>
	<?php } ?>

	<?php if ( is_active_sidebar( 'sidebar-footer-middle' ) ) { ?>
		<div class="footer-sidebar-inner sidebar-footer-middle middle-col">
			<?php dynamic_sidebar( 'sidebar-footer-middle' ); ?>
		</div>
	<?php } ?>

	<?php if ( is_active_sidebar( 'sidebar-footer-right' ) ) { ?>
		<div class="footer-sidebar-inner sidebar-footer-right right-col">
			<?php dynamic_sidebar( 'sidebar-footer-right' ); ?>
		</div>
	<?php } ?>

</div><!-- /#footer-sidebar -->

