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
 * Delete E-Course
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_delete_course() {

	$course_id = absint( $_POST['course_id'] );

	// Security check.
	check_ajax_referer( 'delete_course_' . $course_id );

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