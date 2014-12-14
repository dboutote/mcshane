<?php 
/*
Plugin Name: Landing Page Meta Box
Version: 1.0
Plugin URI:
Description: Create a custom archive-like landing page
Author: Darrin Boutote
Author URI: http://darrinb.com
*/

/**
 * No direct access
 */
defined( 'ABSPATH' ) or die( 'Nothing here!' );

/**
 * Define some constants
 */
define('LP_URL', plugin_dir_url( __FILE__ ) );
define('LP_DIR', dirname( __FILE__ ) . '/');
define('LP_JS_URL', LP_URL . 'js');

# Setup files
if( ! class_exists( 'MetaBox_LandingPage' ) ) {
	require(dirname(__FILE__) . '/class/class.landing-page.php');
	require(dirname(__FILE__) . '/class/class.lp-meta-boxes.php');
	require(dirname(__FILE__) . '/class/class.lp-meta-boxes-2.php');
}
global $MetaBox_LandingPage;
$MetaBox_LandingPage = new MetaBox_LandingPage();
$MetaBox_LandingPageTwo = new MetaBox_LandingPageTwo();
$MetaBox_LandingPageThree = new MetaBox_LandingPageThree();