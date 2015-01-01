<?php
/**
 * The Sub-footer Sidebar
 *
 * @package WordPress
 * @subpackage McShane
 * @since McShane 1.0
 */
?>

<div id="sub-footer-sidebar" class="sub-footer-sidebar-wrap widget-area" role="complementary">

	<?php if ( is_active_sidebar( 'sidebar-footer-sub' ) ) { ?>
		<div class="sub-footer-sidebar-inner sidebar-footer-sub">
			<?php dynamic_sidebar( 'sidebar-footer-sub' ); ?>
		</div>
	<?php } ?>

</div><!-- /#sub-footer-sidebar -->

