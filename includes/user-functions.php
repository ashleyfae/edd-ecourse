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
 * @param int|object       $course Course ID or object.
 * @param bool|WP_User|int $user   User object/ID or leave blank to use current user.
 *
 * @since 1.0.0
 * @return array Array of lesson IDs.
 */
function edd_ecourse_get_started_lessons( $course, $user = false ) {

	$course_id = is_object( $course ) ? $course->id : $course;

	if ( is_numeric( $user ) ) {
		$user_id = $user;
	} elseif ( is_a( $user, 'WP_User' ) ) {
		$user_id = $user->ID;
	} else {
		$user    = wp_get_current_user();
		$user_id = $user->ID;
	}

	$started_lessons = get_user_meta( $user_id, 'started_course_lessons_' . $course_id, true );
	$started_lessons = is_array( $started_lessons ) ? $started_lessons : array();

	return apply_filters( 'edd_ecourse_started_lessons', $started_lessons, $course_id, $user_id );

}

/**
 * Get Completed Lessons
 *
 * Returns an array of lesson IDs that the user has completed.
 *
 * @param int|object       $course Course ID or object.
 * @param bool|WP_User|int $user   User object/ID, or leave blank to use current user.
 *
 * @since 1.0.0
 * @return array Array of lesson IDs.
 */
function edd_ecourse_get_completed_lessons( $course, $user = false ) {

	$course_id = is_object( $course ) ? $course->id : $course;

	if ( is_numeric( $user ) ) {
		$user_id = $user;
	} elseif ( is_a( $user, 'WP_User' ) ) {
		$user_id = $user->ID;
	} else {
		$user    = wp_get_current_user();
		$user_id = $user->ID;
	}

	$completed_lessons = get_user_meta( $user_id, 'completed_course_lessons_' . $course_id, true );
	$completed_lessons = is_array( $completed_lessons ) ? $completed_lessons : array();

	return apply_filters( 'edd_ecourse_completed_lessons', $completed_lessons, $course_id, $user_id );

}

/**
 * Has Completed Lesson
 *
 * Checks to see if a given user has completed a given lesson.
 *
 * @param int              $lesson_id Lesson ID to check.
 * @param bool|WP_User|int $user      User object or ID, or leave blank to use current user.
 *
 * @since 1.0.0
 * @return bool
 */
function edd_ecourse_user_has_completed_lesson( $lesson_id, $user = false ) {

	if ( is_numeric( $user ) ) {
		$user_id = $user;
	} elseif ( is_a( $user, 'WP_User' ) ) {
		$user_id = $user->ID;
	} else {
		$user    = wp_get_current_user();
		$user_id = $user->ID;
	}

	// Get the course associated with this lesson.
	$course = edd_ecourse_get_lesson_course( $lesson_id );

	$has_completed = false;

	if ( $course ) {

		$completed_lessons = edd_ecourse_get_completed_lessons( $course, $user_id );

		if ( array_key_exists( $lesson_id, $completed_lessons ) ) {
			$has_completed = true;
		}

	}

	return apply_filters( 'edd_ecourse_user_has_completed_course', $has_completed, $lesson_id, $user_id, $course );

}