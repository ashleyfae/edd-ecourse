<?php
/**
 * Lesson Functions
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
 * @param $lesson
 *
 * @since 1.0.0
 * @return mixed|void
 */
function edd_ecourse_get_lesson_position( $lesson ) {

	$lesson_id = is_a( $lesson, 'WP_Post' ) ? $lesson->ID : $lesson;
	$position  = get_post_meta( $lesson_id, 'lesson_position', true );

	return apply_filters( 'edd_ecourse_get_lesson_position', $position, $lesson );

}

function edd_ecourse_lesson_completion() {

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