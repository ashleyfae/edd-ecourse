<?php
/**
 * Functions for Users and Students
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
 * Get Started Lessons
 *
 * Returns an array of lesson IDs that the user has started.
 *
 * @param int|object   $course Course ID or object.
 * @param bool|WP_User $user   User object or leave blank to use current user.
 *
 * @since 1.0.0
 * @return array Array of lesson IDs.
 */
function edd_ecourse_get_started_lessons( $course, $user = false ) {

	$course_id = is_object( $course ) ? $course->id : $course;

	if ( ! $user ) {
		$user = wp_get_current_user();
	}

	$started_lessons = get_user_meta( $user->ID, 'started_course_lessons_' . $course_id, true );
	$started_lessons = is_array( $started_lessons ) ? $started_lessons : array();

	return apply_filters( 'edd_ecourse_started_lessons', $started_lessons, $course_id, $user );

}

/**
 * Get Completed Lessons
 *
 * Returns an array of lesson IDs that the user has completed.
 *
 * @param int|object   $course Course ID or object.
 * @param bool|WP_User $user   User object or leave blank to use current user.
 *
 * @since 1.0.0
 * @return array Array of lesson IDs.
 */
function edd_ecourse_get_completed_lessons( $course, $user = false ) {

	$course_id = is_object( $course ) ? $course->id : $course;

	if ( ! $user ) {
		$user = wp_get_current_user();
	}

	$completed_lessons = get_user_meta( $user->ID, 'completed_course_lessons_' . $course_id, true );
	$completed_lessons = is_array( $completed_lessons ) ? $completed_lessons : array();

	return apply_filters( 'edd_ecourse_completed_lessons', $completed_lessons, $course_id, $user );

}