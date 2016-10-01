<?php
/**
 * E-Course Functions
 *
 * @package   EDD\E-Course\Admin\Courses\Functions
 * @copyright Copyright (c) 2016, Ashley Gibson
 * @license   GPL2+
 * @since     1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get Add Course URL
 *
 * Returns the URL to the "Add Course" page.
 *
 * @since 1.0.0
 * @return string
 */
function edd_ecourse_get_add_course_url() {
	$url = add_query_arg( array(
		'page' => 'ecourses',
		'view' => 'add'
	), admin_url( 'admin.php' ) );

	return apply_filters( 'edd_ecourse_get_add_course_url', $url );
}