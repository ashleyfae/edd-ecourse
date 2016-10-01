<?php
/**
 * Scripts
 *
 * @package   EDD\E-Course\Scripts
 * @copyright Copyright (c) 2016, Ashley Gibson
 * @license   GPL2+
 * @since     1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Load frontend scripts
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_scripts( $hook ) {
	// Use minified libraries if SCRIPT_DEBUG is turned off
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	wp_enqueue_script( 'edd_ecourse_js', EDD_ECOURSE_URL . '/assets/js/scripts' . $suffix . '.js', array( 'jquery' ) );
	wp_enqueue_style( 'edd_ecourse_css', EDD_ECOURSE_URL . '/assets/css/styles' . $suffix . '.css' );
}

add_action( 'wp_enqueue_scripts', 'edd_ecourse_scripts' );
