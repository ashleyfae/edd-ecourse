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
	$course = wp_insert_term( __( 'My First Course', 'edd-ecourse' ), 'ecourse' );

	if ( is_wp_error( $course ) ) {
		return false;
	}

	$post_data = array(
		'post_title'   => __( 'Lesson #1', 'edd-ecourse' ),
		'post_content' => __( 'This is your first e-course lesson.', 'edd-ecourse' ),
		'post_status'  => 'publish',
		'post_type'    => 'ecourse_lesson',
		'tax_input'    => array(
			'ecourse' => array( $course['term_id'] )
		)
	);

	$lesson_id = wp_insert_post( $post_data );

	if ( is_wp_error( $lesson_id ) || ! $lesson_id ) {
		return false;
	}

	return true;

}

/**
 * Get E-Courses
 *
 * @param array $args
 *
 * @since 1.0.0
 * @return array|false Array of WP_Term objects or false if none exist.
 */
function edd_ecourse_get_courses( $args = array() ) {

	$defaults = array(
		'hide_empty' => false,
		'taxonomy'   => 'ecourse'
	);

	$args = wp_parse_args( $args, $defaults );

	$courses = get_terms( $args );

	if ( ! is_array( $courses ) ) {
		return false;
	}

	return $courses;

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
		'post_type'      => 'ecourse',
		'posts_per_page' => 500,
		'tax_query'      => array(
			array(
				'taxonomy' => 'ecourse',
				'field'    => 'id',
				'terms'    => $course_id
			)
		)
	);

	$query_args = wp_parse_args( $query_args, $default_args );

	$lessons = get_posts( $query_args );

	return $lessons;

}