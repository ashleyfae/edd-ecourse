<?php
/**
 * Lesson Actions
 *
 * Mostly AJAX callbacks.
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
 * Update Course Module List
 *
 * @since 1.0
 * @return void
 */
function edd_ecourse_update_course_module_list() {
	check_ajax_referer( 'edd_ecourse_save_lesson_details', 'nonce' );

	// Permission check.
	if ( ! current_user_can( 'manage_options' ) ) { // @todo change this
		wp_die( __( 'You don\'t have permission to add courses.', 'edd-ecourse' ) );
	}

	$course_id = $_POST['course'];

	if ( ! is_numeric( $course_id ) ) {
		wp_die( __( 'Invalid course ID.', 'edd-ecourse' ) );
	}

	$modules = edd_ecourse_get_course_modules( absint( $course_id ) );

	if ( is_array( $modules ) ) {

		$modules_final = array();

		foreach ( $modules as $module ) {
			$modules_final[ $module->id ] = $module->title;
		}

	} else {
		$modules_final = false;
	}

	wp_send_json_success( $modules_final );
}

add_action( 'wp_ajax_edd_ecourse_update_course_module_list', 'edd_ecourse_update_course_module_list' );