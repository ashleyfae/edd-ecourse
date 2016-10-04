<?php
/**
 * Rewrite Functions
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
 * Get Endpoint
 *
 * @since 1.0.0
 * @return string
 */
function edd_ecourse_get_endpoint() {
	return apply_filters( 'edd_ecourse_endpoint', 'course' );
}

/**
 * Add Endpoint
 *
 * @uses  edd_ecourse_get_endpoint()
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_add_endpoint() {
	add_rewrite_endpoint( edd_ecourse_get_endpoint(), EP_ALL );
}

add_action( 'init', 'edd_ecourse_add_endpoint' );