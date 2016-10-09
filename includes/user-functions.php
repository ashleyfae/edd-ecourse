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

/**
 * Has Course Access
 *
 * Checks whether or not a given user has access to a given course.
 *
 * @param int              $course_id ID of the course to grant access to.
 * @param bool|WP_User|int $user      User object/ID or leave false to use current user.
 *
 * @since 1.0.0
 * @return bool
 */
function edd_ecourse_has_course_access( $course_id, $user = false ) {

	if ( is_numeric( $user ) ) {
		$user = new WP_User( $user );
	} elseif ( ! $user ) {
		$user = wp_get_current_user();
	}

	if ( ! is_a( $user, 'WP_User' ) ) {
		return false;
	}

	$capability = 'view_course_' . absint( $course_id );

	$has_access = user_can( $user, $capability );

	return apply_filters( 'edd_ecourse_has_course_access', $has_access, $course_id, $user, $capability );

}

/**
 * Grant Access to Course
 *
 * @param int              $course_id ID of the course to grant access to.
 * @param bool|WP_User|int $user      User object/ID or leave false to use current user.
 *
 * @since 1.0.0
 * @return bool
 */
function edd_ecourse_grant_course_access( $course_id, $user = false ) {

	if ( is_numeric( $user ) ) {
		$user = new WP_User( $user );
	} elseif ( ! $user ) {
		$user = wp_get_current_user();
	}

	if ( ! is_a( $user, 'WP_User' ) ) {
		return false;
	}

	// If they already have access, bail.
	if ( edd_ecourse_has_course_access( $course_id, $user ) ) {
		return true;
	}

	$capability = 'view_course_' . absint( $course_id );

	$user->add_cap( $capability );

	do_action( 'edd_ecourse_grant_course_access', $course_id, $user );

	return true;

}

/**
 * Revoke Access to Course
 *
 * @param int               $course_id ID of the course to grant access to.
 * @param WP_User|int|false $user      User object/ID or leave false to use current user.
 *
 * @since 1.0.0
 * @return bool
 */
function edd_ecourse_revoke_course_access( $course_id, $user = false ) {

	if ( is_numeric( $user ) ) {
		$user = new WP_User( $user );
	} elseif ( ! $user ) {
		$user = wp_get_current_user();
	}

	if ( ! is_a( $user, 'WP_User' ) ) {
		return false;
	}

	// If they don't have access, bail.
	if ( ! edd_ecourse_has_course_access( $course_id, $user ) ) {
		return true;
	}

	$capability = 'view_course_' . absint( $course_id );

	$user->remove_cap( $capability );

	do_action( 'edd_ecourse_revoke_course_access', $course_id, $user );

	return true;

}

/**
 * User Can View Current Page
 *
 * Checks whether or not a user is allowed to view the current e-course page.
 *
 * @todo  :
 *      Check for extra EDD pricing restrictions.
 *
 * @param WP_User|int|false $user User object/ID or leave false to use current user.
 *
 * @since 1.0.0
 * @return bool Whether or not the user can view this e-course page.
 */
function edd_ecourse_user_can_view_page( $user = false ) {

	if ( is_numeric( $user ) ) {
		$user_id = $user;
	} elseif ( is_a( $user, 'WP_User' ) ) {
		$user_id = $user->ID;
	} else {
		$user    = wp_get_current_user();
		$user_id = $user->ID;
	}

	$can_view_page     = false;
	$current_course_id = edd_ecourse_get_id();

	if ( $current_course_id ) {
		// Can view only if they have access to the course.
		$can_view_page = edd_ecourse_has_course_access( $current_course_id, $user_id );

		// Extra checks for single lesson pages.
		if ( is_singular( 'ecourse_lesson' ) ) {
			if ( edd_ecourse_is_free_preview( get_post() ) ) {
				$can_view_page = true;
			}
		}
	} else {
		// Check to see if we're on the dashboard page and grant access to all logged in users.
		if ( edd_ecourse_is_dashboard_page() && $user_id > 0 ) {
			$can_view_page = true;
		}
	}

	return apply_filters( 'edd_ecourse_user_can_view_page', $can_view_page, $current_course_id, $user_id );

}

/**
 * Grant All Admins Access to E-Course Pages
 *
 * @param bool      $can_view_page     Whether or not the user can view the page.
 * @param int|false $current_course_id ID of the current course.
 * @param int       $user_id           ID of the user to check.
 *
 * @since 1.0.0
 * @return bool
 */
function edd_ecourse_grant_admin_access_to_pages( $can_view_page, $current_course_id, $user_id ) {

	if ( user_can( $user_id, 'manage_options' ) ) {
		return true;
	}

	return $can_view_page;

}

add_filter( 'edd_ecourse_user_can_view_page', 'edd_ecourse_grant_admin_access_to_pages', 10, 3 );