<?php
/**
 * Template Functions
 *
 * For modifying the front-end of the site.
 *
 * @package   edd-ecourse
 * @copyright Copyright (c) 2016, Ashley Gibson
 * @license   GPL2+
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check: Is Course Page
 *
 * Checks whether or not we're on an official e-course page.
 *
 * @todo  actually make this work
 *
 * @since 1.0.0
 * @return bool
 */
function edd_ecourse_is_course_page() {
	return false;
}

/**
 * Modify Styles Queue
 *
 * Removes all theme CSS from the stylesheet queue. This is so we
 * can add our own styles without interference.
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_modify_styles_queue() {

	// Bail if not on an e-course page.
	if ( ! edd_ecourse_is_course_page() ) {
		return;
	}

	global $wp_styles;

	$new_queue = array();

	foreach ( $wp_styles->queue as $handle ) {
		if ( ! array_key_exists( $handle, $wp_styles->registered ) ) {
			continue;
		}

		// Only add to new queue if the style isn't from the theme.
		if ( false === strpos( $wp_styles->registered[ $handle ]->src, get_theme_root_uri() ) ) {
			$new_queue[] = $handle;
		}
	}

	$wp_styles->queue = $new_queue;

}

add_action( 'wp_print_styles', 'edd_ecourse_modify_styles_queue' );