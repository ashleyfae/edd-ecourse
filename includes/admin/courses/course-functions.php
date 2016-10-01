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

/**
 * Get Edit Course URL
 *
 * Returns the URL to the "Edit Course" page.
 *
 * @since 1.0.0
 * @return string
 */
function edd_ecourse_get_edit_course_url( $course_id = 0 ) {
	$url = add_query_arg( array(
		'page'   => 'ecourses',
		'view'   => 'edit',
		'course' => absint( $course_id )
	), admin_url( 'admin.php' ) );

	return apply_filters( 'edd_ecourse_get_edit_course_url', $url );
}

/**
 * Checks to see if we're currently on the Edit Course page.
 *
 * @since 1.0.0
 * @return bool
 */
function edd_ecourse_is_edit_course_page() {
	$is_course_page = false;

	if ( isset( $_GET['page'] ) && 'ecourses' == $_GET['page'] && isset( $_GET['view'] ) && 'edit' == $_GET['view'] ) {
		$is_course_page = true;
	}

	return apply_filters( 'edd_ecourses_is_edit_course_page', $is_course_page );
}

/**
 * Get Add Course URL
 *
 * Returns the URL to the "Add Course" page.
 *
 * @since 1.0.0
 * @return string
 */
function edd_ecourse_get_add_lesson_url( $course_id = false ) {
	$args = array(
		'post_type' => 'ecourse_lesson'
	);

	if ( $course_id ) {
		$args['course'] = absint( $course_id );
	}

	$url = add_query_arg( $args, admin_url( 'post-new.php' ) );

	return apply_filters( 'edd_ecourse_get_add_lesson_url', $url );
}

/**
 * Get View Lessons URL
 *
 * Returns the URL to the page that lists all the lessons in an e-course.
 *
 * @param int $course_id
 *
 * @since 1.0.0
 * @return string
 */
function edd_ecourse_get_view_lessons_url( $course_id = 0 ) {
	$url = add_query_arg( array(
		'page'   => 'ecourses',
		'view'   => 'list',
		'course' => $course_id
	), admin_url( 'admin.php' ) );

	return apply_filters( 'edd_ecourse_get_add_course_url', $url );
}