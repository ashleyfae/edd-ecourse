<?php
/**
 * Course Actions
 *
 * @package   EDD\E-Course\Admin\Courses\Actions
 * @copyright Copyright (c) 2016, Ashley Gibson
 * @license   GPL2+
 * @since     1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add E-Course
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_add_course() {

	// Security check.
	check_ajax_referer( 'edd_ecourse_add_course', 'nonce' );

	// Permission check.
	if ( ! current_user_can( 'manage_terms' ) ) {
		wp_die( __( 'You don\'t have permission to add courses.', 'edd-ecourse' ) );
	}

	$course_name = wp_strip_all_tags( $_POST['course_name'] );

	if ( ! $course_name ) {
		wp_die( __( 'A course name is required.', 'edd-ecourse' ) );
	}

	$term = wp_insert_term( $course_name, 'ecourse' );

	if ( is_wp_error( $term ) || ! is_array( $term ) ) {
		wp_die( __( 'An error occurred while creating the e-course.', 'edd-ecourse' ) );
	}

	wp_send_json_success( $term['term_id'] );

}

add_action( 'wp_ajax_edd_ecourse_add_course', 'edd_ecourse_add_course' );

/**
 * Delete E-Course
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_delete_course() {

	$course_id = absint( $_POST['course_id'] );

	// Security check.
	check_ajax_referer( 'delete_course_' . $course_id, 'nonce' );

	// Permission check.
	if ( ! current_user_can( 'delete_terms', $course_id ) ) {
		wp_die( __( 'You don\'t have permission to delete this course.', 'edd-ecourse' ) );
	}

	$course = get_term( $course_id, 'ecourse' );

	if ( ! $course || is_wp_error( $course ) || ! is_object( $course ) ) {
		wp_die( __( 'Error: Not a valid e-course.', 'edd-ecourse' ) );
	}

	// Delete all lessons in this e-course.
	$lessons = edd_ecourse_get_course_lessons( $course_id, array( 'post_status' => 'any', 'fields' => 'ids' ) );

	if ( is_array( $lessons ) ) {
		foreach ( $lessons as $lesson_id ) {
			wp_delete_post( $lesson_id, true );
		}
	}

	// Now delete the term itself.
	wp_delete_term( $course_id, 'ecourse' );

	wp_send_json_success();

}

add_action( 'wp_ajax_edd_ecourse_delete_course', 'edd_ecourse_delete_course' );