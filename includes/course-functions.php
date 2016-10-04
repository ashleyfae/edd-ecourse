<?php
/**
 * E-Course Functions
 *
 * @package   EDD\E-Course\Course\Functions
 * @copyright Copyright (c) 2016, Ashley Gibson
 * @license   GPL2+
 * @since     1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Insert Demo Course
 *
 * Adds an e-course and one associated lesson.
 *
 * @since 1.0.0
 * @return bool
 */
function edd_ecourse_insert_demo_course() {

	// Make sure we don't do this twice.
	if ( get_option( 'edd_ecourse_inserted_demo_content' ) ) {
		return false;
	}

	// Insert e-course.
	$course_id = edd_ecourse_load()->courses->add( array(
		'title' => esc_html__( 'My First Course', 'edd-ecourse' )
	) );

	if ( ! $course_id ) {
		return false;
	}

	$post_data = array(
		'post_title'   => __( 'Lesson #1', 'edd-ecourse' ),
		'post_content' => __( 'This is your first e-course lesson.', 'edd-ecourse' ),
		'post_status'  => 'publish',
		'post_type'    => 'ecourse_lesson',
		'tax_input'    => array(
			'ecourse' => array( intval( $course_id ) )
		)
	);

	$lesson_id = wp_insert_post( $post_data );

	if ( is_wp_error( $lesson_id ) || ! $lesson_id ) {
		return false;
	}

	return true;

}

/**
 * Get Course by ID
 *
 * @param int $course_id
 *
 * @since 1.0.0
 * @return object|false Course object or false on failure.
 */
function edd_ecourse_get_course( $course_id ) {
	return edd_ecourse_load()->courses->get_course_by( 'id', $course_id );
}

/**
 * Get E-Courses
 *
 * @param array $args
 *
 * @since 1.0.0
 * @return array|false Array of course objects or false if none exist.
 */
function edd_ecourse_get_courses( $args = array() ) {

	$defaults = array(
		'number' => - 1
	);

	$args = wp_parse_args( $args, $defaults );

	$courses = edd_ecourse_load()->courses->get_courses( $args );

	if ( ! is_array( $courses ) ) {
		return false;
	}

	return $courses;

}

/**
 * Insert a Course
 *
 * @param array $args Arguments, including `title` (required), `description`, `status`, `type`, `start_date`
 *
 * @since 1.0.0
 * @return int|bool Course ID on success or false on failure.
 */
function edd_ecourse_insert_course( $args = array() ) {
	$course_id = edd_ecourse_load()->courses->add( $args );

	return $course_id;
}

/**
 * Get Course Modules
 *
 * @param int $course_id ID of the course.
 *
 * @since 1.0.0
 * @return array|false Array of module objects or false on failure.
 */
function edd_ecourse_get_course_modules( $course_id, $args = array() ) {

	$defaults = array(
		'course' => $course_id,
		'number' => - 1
	);

	$args = wp_parse_args( $args, $defaults );

	$modules = edd_ecourse_load()->modules->get_modules( $args );

	if ( ! is_array( $modules ) || ! count( $modules ) ) {
		$modules = false;
	}

	return apply_filters( 'edd_ecourse_get_course_modules', $modules, $course_id, $args );

}

/**
 * Get E-Course Lessons
 *
 * @param int   $course_id  ID of the course.
 * @param array $query_args WP_Query arguments to override the defaults.
 *
 * @since 1.0.0
 * @return array
 */
function edd_ecourse_get_course_lessons( $course_id, $query_args = array() ) {

	$default_args = array(
		'post_type'      => 'ecourse_lesson',
		'posts_per_page' => 500,
		'meta_query'     => array(
			array(
				'key'   => 'ecourse',
				'value' => $course_id
			)
		)
	);

	if ( current_user_can( 'manage_options' ) ) {
		$default_args['post_status'] = 'any';
	}

	$query_args = wp_parse_args( $query_args, $default_args );

	$lessons = get_posts( $query_args );

	return $lessons;

}

/**
 * Delete E-Course
 *
 * @param int $course_id ID of the course to delete.
 *
 * @since 1.0.0
 * @return int|false The number of courses deleted, or false on error.
 */
function edd_ecourse_delete( $course_id ) {
	return edd_ecourse_load()->courses->delete( $course_id );
}