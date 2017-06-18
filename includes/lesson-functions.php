<?php
/**
 * Lesson Functions
 *
 * @package   edd-ecourse
 * @copyright Copyright (c) 2017, Ashley Gibson
 * @license   GPL2+
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get Lesson Course
 *
 * Returns the ID of the course this lesson is assigned to.
 *
 * @param WP_Post|int $lesson   Post object or ID.
 * @param bool        $fallback Whether or not to fall back to the `$_GET` variable.
 *
 * @since 1.0.0
 * @return int|false Course ID or false if none is selected.
 */
function edd_ecourse_get_lesson_course( $lesson, $fallback = true ) {

	$lesson_id = is_a( $lesson, 'WP_Post' ) ? $lesson->ID : $lesson;
	$course_id = get_post_meta( $lesson_id, 'course', true );

	if ( $fallback && ! $course_id && array_key_exists( 'course', $_GET ) ) {
		$course_id = $_GET['course'];
	}

	return apply_filters( 'edd_ecourse_get_lesson_course', $course_id, $lesson );

}

/**
 * Get Lesson Module
 *
 * Returns the ID of the module that this lesson is assigned to.
 *
 * @param WP_Post|int $lesson   Post object or ID.
 * @param bool        $fallback Whether or not to fall back to the `$_GET` variable.
 *
 * @since 1.0.0
 * @return int|false Module ID or false if none is selected.
 */
function edd_ecourse_get_lesson_module( $lesson, $fallback = true ) {

	$lesson_id = is_a( $lesson, 'WP_Post' ) ? $lesson->ID : $lesson;
	$module_id = get_post_meta( $lesson_id, 'module', true );

	if ( $fallback && ! $module_id && array_key_exists( 'module', $_GET ) ) {
		$module_id = $_GET['module'];
	}

	return apply_filters( 'edd_ecourse_get_lesson_module', $module_id, $lesson );

}

/**
 * Get Lesson Type
 *
 * Returns an array of selected lesson types for this lesson.
 *
 * @param WP_Post|int $lesson Post object or ID.
 *
 * @since 1.0.0
 * @return array|false Array of lesson types or false if none.
 */
function edd_ecourse_get_lesson_type( $lesson ) {

	$lesson_id = is_a( $lesson, 'WP_Post' ) ? $lesson->ID : $lesson;
	$type      = get_post_meta( $lesson_id, 'lesson_type', true );

	return apply_filters( 'edd_ecourse_get_lesson_type', $type, $lesson );

}

/**
 * Get Available Lesson Types
 *
 * Returns an array of all the available lesson type choices.
 * The icon should be the name of a Font Awesome icon (without the
 * `fa fa-` prefixes).
 *
 * @since 1.0.0
 * @return array
 */
function edd_ecourse_get_available_lesson_types() {
	$types = array(
		'audio'    => array(
			'name' => __( 'Audio', 'edd-ecourse' ),
			'icon' => 'microphone'
		),
		'document' => array(
			'name' => __( 'Document', 'edd-ecourse' ),
			'icon' => 'paperclip'
		),
		'text'     => array(
			'name' => __( 'Text', 'edd-ecourse' ),
			'icon' => 'file-text'
		),
		'video'    => array(
			'name' => __( 'Video', 'edd-ecourse' ),
			'icon' => 'video'
		)
	);

	return apply_filters( 'edd_ecourse_get_available_lesson_types', $types );
}

/**
 * Get Lesson Status
 *
 * This basically just looks at the `post_status` and tweaks the wording,
 * tense, and capitalization.
 *
 * @param WP_Post|int $lesson Post object or ID.
 *
 * @since 1.0.0
 * @return string|false
 */
function edd_ecourse_get_lesson_status( $lesson ) {
	$post = is_a( $lesson, 'WP_Post' ) ? $lesson : get_post( $lesson );

	if ( ! is_a( $post, 'WP_Post' ) ) {
		return false;
	}

	switch ( $post->post_status ) {

		case 'publish' :
			$status = __( 'Published', 'edd-ecourse' );
			break;

		case 'draft' :
			$status = __( 'Draft', 'edd-ecourse' );
			break;

		case 'private' :
			$status = __( 'Private', 'edd-ecourse' );
			break;

		case 'pending' :
			$status = __( 'Pending', 'edd-ecourse' );
			break;

		default :
			$status = false;
			break;

	}

	return apply_filters( 'edd_ecourse_get_lesson_status', $status, $post );
}

/**
 * Get the position of a lesson.
 *
 * @param WP_Post|int $lesson Lesson ID or object.
 *
 * @since 1.0.0
 * @return int
 */
function edd_ecourse_get_lesson_position( $lesson ) {

	$lesson_id = is_a( $lesson, 'WP_Post' ) ? $lesson->ID : $lesson;
	$position  = get_post_meta( $lesson_id, 'lesson_position', true );

	return apply_filters( 'edd_ecourse_get_lesson_position', $position, $lesson );

}

/**
 * Get Lesson Completion
 *
 * Determines whether a given user has completed a lesson, not started a lesson, or is
 * in the middle of one.
 *
 * @param WP_Post|int  $lesson Post object or ID.
 * @param bool|WP_User $user   User object or leave blank to use current user.
 *
 * @since 1.0.0
 * @return string|false Will return `complete`, `in-progress`, or `not-started`. False if not logged in.
 */
function edd_ecourse_get_lesson_completion( $lesson, $user = false ) {

	if ( ! $user ) {
		if ( ! is_user_logged_in() ) {
			return false;
		}

		$user = wp_get_current_user();
	}

	$lesson_id = is_a( $lesson, 'WP_Post' ) ? $lesson->ID : $lesson;

	$status = 'not-started'; // Default
	$course = edd_ecourse_get_lesson_course( $lesson, false );

	if ( $course ) {
		$started_lessons   = edd_ecourse_get_started_lessons( $course, $user );
		$completed_lessons = edd_ecourse_get_completed_lessons( $course, $user );

		if ( array_key_exists( $lesson_id, $completed_lessons ) ) {
			$status = 'complete';
		} elseif ( array_key_exists( $lesson_id, $started_lessons ) ) {
			$status = 'in-progress';
		} else {
			$status = 'not-started';
		}
	}

	return apply_filters( 'edd_ecourse_lesson_completion', $status, $lesson_id, $course );

}

/**
 * Get Lesson Completion Icon
 *
 * Will return a different icon based on the completion status:
 *      + Completed
 *      + In Progress
 *      + Not Started
 *
 * @param WP_Post|int  $lesson Post object or ID.
 * @param bool|WP_User $user   User object or leave blank to use current user.
 *
 * @since 1.0.0
 * @return string
 */
function edd_ecourse_get_lesson_completion_icon( $lesson, $user = false ) {

	$status = edd_ecourse_get_lesson_completion( $lesson, $user );

	switch ( $status ) {

		case 'complete' :
			$icon = apply_filters( 'edd_ecourse_lesson_complete_icon', 'check-circle' );
			$html = '<i class="fa fa-' . sanitize_html_class( $icon ) . '"></i>';
			break;

		case 'in-progress' :
			$icon = apply_filters( 'edd_ecourse_lesson_complete_in_progress', 'adjust' );
			$html = '<i class="fa fa-' . sanitize_html_class( $icon ) . '"></i>';
			break;

		default :
			$icon = apply_filters( 'edd_ecourse_lesson_complete_not_started', 'circle-o' );
			$html = '<i class="fa fa-' . sanitize_html_class( $icon ) . '"></i>';

	}

	return apply_filters( 'edd_ecourse_lesson_completion_icon_html', $html, $icon, $status, $lesson, $user );

}

/**
 * Display Lesson Completion Icon
 *
 * Will display a different icon based on the completion status:
 *      + Completed
 *      + In Progress
 *      + Not Started
 *
 * @param WP_Post|int  $lesson Post object or ID.
 * @param bool|WP_User $user   User object or leave blank to use current user.
 *
 * @uses  edd_ecourse_get_lesson_completion_icon()
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_lesson_completion_icon( $lesson, $user = false ) {
	echo edd_ecourse_get_lesson_completion_icon( $lesson, $user );
}

/**
 * Get Lesson Type Icon(s)
 *
 * @param WP_Post|int $lesson Post object or ID.
 *
 * @since 1.0.0
 * @return string
 */
function edd_ecourse_get_lesson_type_icon( $lesson ) {

	$types     = edd_ecourse_get_lesson_type( $lesson );
	$icons     = array();
	$all_types = edd_ecourse_get_available_lesson_types();

	if ( is_array( $types ) ) {
		foreach ( $types as $type ) {
			if ( array_key_exists( $type, $all_types ) && isset( $all_types[ $type ]['icon'] ) ) {
				$icons[ $type ] = '<i class="fa fa-' . sanitize_html_class( $all_types[ $type ]['icon'] ) . '"></i>';
			}
		}
	}

	$icons = apply_filters( 'edd_ecourse_lesson_type_icon_array', $icons, $lesson, $types, $all_types );

	$html = implode( '', $icons );

	return apply_filters( 'edd_ecourse_lesson_type_icon_html', $html, $icons, $lesson, $types, $all_types );

}

/**
 * Display Lesson Type Icon(s)
 *
 * @param WP_Post|int $lesson Post object or ID.
 *
 * @uses  edd_ecourse_get_lesson_type_icon()
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_lesson_type_icon( $lesson ) {
	echo edd_ecourse_get_lesson_type_icon( $lesson );
}

/**
 * Get Lesson Classes
 *
 * Returns an array of HTML classes for a given lesson. Includes
 * class names that indicate tehe lesson type and view status.
 *
 * @param int|WP_Post $lesson Post object or ID.
 *
 * @since 1.0.0
 * @return array
 */
function edd_ecourse_get_lesson_classes( $lesson ) {

	$classes = array( 'ecourse-lesson' );

	// Completion status.
	$status = edd_ecourse_get_lesson_completion( $lesson );
	if ( $status ) {
		$classes[] = 'lesson-completion-' . $status;
	}

	// Status.
	$status    = edd_ecourse_get_lesson_status( $lesson );
	$classes[] = 'lesson-status-' . $status;

	// Type(s)
	$types = edd_ecourse_get_lesson_type( $lesson );
	if ( is_array( $types ) ) {
		foreach ( $types as $type ) {
			$classes[] = 'lesson-type-' . $type;
		}
	}

	// Sanitize them all.
	$sanitized_classes = array();
	foreach ( $classes as $class ) {
		$sanitized_classes[] = sanitize_html_class( strtolower( $class ) );
	}

	return apply_filters( 'edd_ecourse_lesson_classes', $sanitized_classes, $lesson );

}

/**
 * Display Lesson Class Attribute
 *
 * @param int|WP_Post $lesson
 * @param string      $extra_classes Additional class name(s) to add.
 *
 * @uses  edd_ecourse_get_lesson_classes()
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_lesson_class( $lesson, $extra_classes = '' ) {
	$classes = edd_ecourse_get_lesson_classes( $lesson );

	if ( $extra_classes ) {
		$classes[] = $extra_classes;
	}

	if ( count( $classes ) ) {
		echo ' class="' . esc_attr( implode( ' ', $classes ) ) . '"';
	}
}

/**
 * Is Free Preview
 *
 * Whether or not the lesson is a free preview and should be publicly accessible.
 *
 * @param WP_Post|int $lesson Post object or ID.
 *
 * @since 1.0.0
 * @return bool
 */
function edd_ecourse_is_free_preview( $lesson ) {
	$lesson_id  = is_object( $lesson ) ? $lesson->ID : $lesson;
	$preview    = get_post_meta( $lesson_id, 'free_lesson_preview', true );
	$is_preview = $preview ? true : false;

	return apply_filters( 'edd_ecourse_is_free_lesson_preview', $is_preview, $lesson_id );
}