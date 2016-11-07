<?php
/**
 * Misc. Functions
 *
 * @package   EDD\E-Course\Misc Functions
 * @copyright Copyright (c) 2016, Ashley Gibson
 * @license   GPL2+
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get Dashboard URL
 *
 * URL to the e-course dashboard page.
 *
 * @since 1.0.0
 * @return string
 */
function edd_ecourse_get_dashboard_url() {
	$dashboard = edd_get_option( 'ecourse_dashboard_page' );

	if ( $dashboard ) {
		$url = get_permalink( $dashboard );
	} else {
		$url = false;
	}

	return apply_filters( 'edd_ecourse_dashboard_url', $url, $dashboard );
}