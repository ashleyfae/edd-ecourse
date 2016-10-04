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
function edd_ecourse_add_course_cb() {

	// Security check.
	check_ajax_referer( 'edd_ecourse_add_course', 'nonce' );

	// Permission check.
	if ( ! current_user_can( 'manage_options' ) ) { // @todo change this
		wp_die( __( 'You don\'t have permission to add courses.', 'edd-ecourse' ) );
	}

	$course_name = wp_strip_all_tags( $_POST['course_name'] );

	if ( ! $course_name ) {
		wp_die( __( 'A course name is required.', 'edd-ecourse' ) );
	}

	$course_id = edd_ecourse_insert_course( array( 'title' => $course_name ) );

	if ( false === $course_id ) {
		wp_die( __( 'An error occurred while creating the e-course.', 'edd-ecourse' ) );
	}

	$data = array(
		'ID'              => $course_id,
		'name'            => $course_name,
		'edit_course_url' => edd_ecourse_get_manage_course_url( $course_id ),
		'nonce'           => wp_create_nonce( 'delete_course_' . $course_id )
	);

	wp_send_json_success( apply_filters( 'edd_ecourse_add_course_data', $data ) );

}

add_action( 'wp_ajax_edd_ecourse_add_course', 'edd_ecourse_add_course_cb' );

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
	if ( ! current_user_can( 'manage_options' ) ) { // @todo change this
		wp_die( __( 'You don\'t have permission to delete this course.', 'edd-ecourse' ) );
	}

	if ( ! is_numeric( $course_id ) || $course_id < 1 ) {
		wp_die( __( 'Error: Not a valid e-course.', 'edd-ecourse' ) );
	}

	// Delete all lessons in this e-course.
	$lessons = edd_ecourse_get_course_lessons( $course_id, array( 'post_status' => 'any', 'fields' => 'ids' ) );

	if ( is_array( $lessons ) ) {
		foreach ( $lessons as $lesson_id ) {
			wp_delete_post( $lesson_id, true );
		}
	}

	// Now delete the course itself.
	$result = edd_ecourse_delete( $course_id );

	if ( false === $result ) {
		wp_die( __( 'There was a problem deleting the course.', 'edd-ecourse' ) );
	}

	wp_send_json_success();

}

add_action( 'wp_ajax_edd_ecourse_delete_course', 'edd_ecourse_delete_course' );

/**
 * Add Module
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_add_module_cb() {

	// Security check.
	check_ajax_referer( 'edd_ecourse_add_module', 'nonce' );

	$title     = wp_strip_all_tags( $_POST['title'] );
	$course_id = $_POST['course_id'];
	$position  = $_POST['position'];

	if ( ! $title ) {
		wp_die( __( 'A title is required.', 'edd-ecourse' ) );
	}

	if ( ! is_numeric( $course_id ) || $course_id < 1 ) {
		wp_die( __( 'Invalid course ID', 'edd-ecourse' ) );
	}

	$module_id = edd_ecourse_insert_module( array(
		'title'    => $title,
		'course'   => absint( $course_id ),
		'position' => intval( $position )
	) );

	if ( false === $module_id ) {
		wp_die( __( 'An unexpected error occurred while trying to add this module.', 'edd-ecourse' ) );
	}

	$data = array(
		'ID'         => $module_id,
		'title'      => $title,
		'lesson_url' => esc_url( edd_ecourse_get_add_lesson_url( $course_id, $module_id ) )
	);

	wp_send_json_success( $data );

	exit;

}

add_action( 'wp_ajax_edd_ecourse_add_module', 'edd_ecourse_add_module_cb' );

/**
 * Update E-Course Title
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_update_course_title() {

	// Permission check.
	if ( ! current_user_can( 'manage_options' ) ) { // @todo change this
		wp_die( __( 'You don\'t have permission to delete this module.', 'edd-ecourse' ) );
	}

	$course_id = $_POST['course'];

	if ( ! is_numeric( $course_id ) || $course_id < 1 ) {
		wp_die( __( 'Error: Not a valid course.', 'edd-ecourse' ) );
	}

	$course_id = absint( $course_id );

	$result = edd_ecourse_load()->courses->update( $course_id, array( 'title' => sanitize_text_field( $_POST['title'] ) ) );

	if ( false === $result ) {
		wp_die( __( 'An unexpected error occurred while trying to update this course.', 'edd-ecourse' ) );
	}

	wp_send_json_success();

}

add_action( 'wp_ajax_edd_ecourse_update_course_title', 'edd_ecourse_update_course_title' );

/**
 * Update Module Title
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_update_module_title() {

	// Permission check.
	if ( ! current_user_can( 'manage_options' ) ) { // @todo change this
		wp_die( __( 'You don\'t have permission to delete this module.', 'edd-ecourse' ) );
	}

	$module_id = $_POST['module'];

	if ( ! is_numeric( $module_id ) || $module_id < 1 ) {
		wp_die( __( 'Error: Not a valid module.', 'edd-ecourse' ) );
	}

	$module_id = absint( $module_id );

	$result = edd_ecourse_load()->modules->update( $module_id, array( 'title' => sanitize_text_field( $_POST['title'] ) ) );

	if ( false === $result ) {
		wp_die( __( 'An unexpected error occurred while trying to update this module.', 'edd-ecourse' ) );
	}

	wp_send_json_success();

}

add_action( 'wp_ajax_edd_ecourse_update_module_title', 'edd_ecourse_update_module_title' );

/**
 * Save Module Positions
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_save_module_positions() {

	// Security check.
	check_ajax_referer( 'edd_ecourse_manage_course', 'nonce' );

	// Permission check.
	if ( ! current_user_can( 'manage_options' ) ) { // @todo change this
		wp_die( __( 'You don\'t have permission to delete this module.', 'edd-ecourse' ) );
	}

	$modules = $_POST['modules'];

	if ( ! is_array( $modules ) ) {
		wp_die();
	}

	foreach ( $modules as $position => $module_id ) {
		if ( is_numeric( $position ) ) {
			$result = edd_ecourse_load()->modules->update( $module_id, array( 'position' => absint( $position ) ) );
		}
	}

	wp_send_json_success();

}

add_action( 'wp_ajax_edd_ecourse_save_module_positions', 'edd_ecourse_save_module_positions' );

/**
 * Load Underscore.js Course Template
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_load_course_js_templates() {
	if ( ! isset( $_GET['page'] ) || 'ecourses' != $_GET['page'] ) {
		return;
	}

	$view = isset( $_GET['view'] ) ? wp_strip_all_tags( $_GET['view'] ) : 'overview';

	if ( 'overview' == $view ) {
		include_once EDD_ECOURSE_DIR . 'includes/admin/courses/template-new-course.php';
	}

	if ( 'edit' == $view ) {
		include_once EDD_ECOURSE_DIR . 'includes/admin/courses/template-new-module.php';
	}
}

add_action( 'admin_footer', 'edd_ecourse_load_course_js_templates' );