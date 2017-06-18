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

	$course_name = wp_strip_all_tags( wp_unslash( $_POST['course_name'] ) );

	if ( ! $course_name ) {
		wp_die( __( 'A course name is required.', 'edd-ecourse' ) );
	}

	$course_id = edd_ecourse_insert_course( $course_name );

	if ( ! $course_id ) {
		wp_die( __( 'An error occurred while creating the e-course.', 'edd-ecourse' ) );
	}

	$data = array(
		'ID'              => $course_id,
		'name'            => $course_name,
		'edit_course_url' => edd_ecourse_get_manage_course_url( $course_id ),
		'view_course_url' => get_permalink( $course_id ),
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

	$result = edd_ecourse_delete( $course_id, true, true );

	if ( false === $result ) {
		wp_die( __( 'There was a problem deleting the course.', 'edd-ecourse' ) );
	}

	wp_send_json_success();

	exit;

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

	$title     = wp_strip_all_tags( wp_unslash( $_POST['title'] ) );
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
 * Delete Module
 *
 * @since 1.0
 * @return void
 */
function edd_ecourse_delete_module_cb() {

	// Security check.
	check_ajax_referer( 'edd_ecourse_add_module', 'nonce' );

	$module_id = absint( $_POST['module_id'] );

	if ( empty( $module_id ) ) {
		wp_die( __( 'Missing module ID.', 'edd-ecourse' ) );
	}

	$success = edd_ecourse_delete_module( $module_id );

	if ( $success ) {
		wp_send_json_success();
	} else {
		wp_send_json_error();
	}

	exit;

}

add_action( 'wp_ajax_edd_ecourse_delete_module', 'edd_ecourse_delete_module_cb' );

/**
 * Update E-Course Title
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_update_course_title() {

	// Permission check.
	if ( ! current_user_can( 'manage_options' ) ) { // @todo change this
		wp_die( __( 'You don\'t have permission to edit this course.', 'edd-ecourse' ) );
	}

	$course_id = $_POST['course'];

	if ( ! is_numeric( $course_id ) || $course_id < 1 ) {
		wp_die( __( 'Error: Not a valid course.', 'edd-ecourse' ) );
	}

	$course_data = array(
		'ID'         => absint( $course_id ),
		'post_title' => sanitize_text_field( $_POST['title'] )
	);

	$result = wp_update_post( $course_data );

	if ( ! $result ) {
		wp_die( __( 'An unexpected error occurred while trying to update this course.', 'edd-ecourse' ) );
	}

	wp_send_json_success();

}

add_action( 'wp_ajax_edd_ecourse_update_course_title', 'edd_ecourse_update_course_title' );

/**
 * Update E-Course Slug
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_update_course_slug() {

	// Permission check.
	if ( ! current_user_can( 'manage_options' ) ) { // @todo change this
		wp_die( __( 'You don\'t have permission to edit this course.', 'edd-ecourse' ) );
	}

	$course_id = $_POST['course'];

	if ( ! is_numeric( $course_id ) || $course_id < 1 ) {
		wp_die( __( 'Error: Not a valid course.', 'edd-ecourse' ) );
	}

	$course_data = array(
		'ID'        => absint( $course_id ),
		'post_name' => sanitize_title( $_POST['slug'] )
	);

	$result = wp_update_post( $course_data );

	if ( ! $result ) {
		wp_die( __( 'An unexpected error occurred while trying to update this course.', 'edd-ecourse' ) );
	}

	wp_send_json_success();

}

add_action( 'wp_ajax_edd_ecourse_update_course_slug', 'edd_ecourse_update_course_slug' );

/**
 * Update E-Course
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_update_course() {

	// Permission check.
	if ( ! current_user_can( 'manage_options' ) ) { // @todo change this
		wp_die( __( 'You don\'t have permission to delete this module.', 'edd-ecourse' ) );
	}

	$args = $_POST['args'];

	if ( ! is_array( $args ) ) {
		wp_die( __( 'Error: Invalid arguments.', 'edd-ecourse' ) );
	}

	$sanitized_args = array_map( 'wp_strip_all_tags', $args );

	if ( ! array_key_exists( 'ID', $sanitized_args ) || empty( $sanitized_args['ID'] ) || ! is_numeric( $sanitized_args['ID'] ) ) {
		wp_die( __( 'Error: Invalid course ID.', 'edd-ecourse' ) );
	}

	$course = get_post( $sanitized_args['ID'] );
	if ( ! $course || ! is_a( $course, 'WP_Post' ) || 'ecourse' != $course->post_type ) {
		wp_die( __( 'Error: Not a valid e-course.', 'edd-ecourse' ) );
	}

	/* Okay we can start saving. */

	if ( array_key_exists( 'post_date', $sanitized_args ) && $sanitized_args['post_date'] ) {

		$date_string                 = $sanitized_args['post_date'];
		$sanitized_args['post_date'] = date( 'Y-m-d H:i:s', strtotime( $date_string ) );

		// Add post date GMT
		if ( ! array_key_exists( 'post_date_gmt', $sanitized_args ) ) {
			$sanitized_args['post_date_gmt'] = get_gmt_from_date( $sanitized_args['post_date'] );
		}

		// Maybe change post status.
		if ( strtotime( $date_string ) > time() ) {
			$sanitized_args['post_status'] = 'future';
		}

	}

	$course_data = $sanitized_args;

	$result = wp_update_post( $course_data );

	if ( ! $result ) {
		wp_die( __( 'An unexpected error occurred while trying to update this course.', 'edd-ecourse' ) );
	}

	wp_send_json_success();

}

add_action( 'wp_ajax_edd_ecourse_update_course', 'edd_ecourse_update_course' );

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
 * Save Lesson Positions
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_save_lesson_positions() {

	// Security check.
	check_ajax_referer( 'edd_ecourse_manage_course', 'nonce' );

	// Permission check.
	if ( ! current_user_can( 'manage_options' ) ) { // @todo change this
		wp_die( __( 'You don\'t have permission to delete this module.', 'edd-ecourse' ) );
	}

	$lessons = $_POST['lessons'];

	if ( ! is_array( $lessons ) ) {
		wp_die();
	}

	foreach ( $lessons as $position => $lesson_id ) {
		if ( is_numeric( $position ) ) {
			wp_update_post( array(
				'ID'         => $lesson_id,
				'menu_order' => absint( $position )
			) );
		}
	}

	wp_send_json_success();

}

add_action( 'wp_ajax_edd_ecourse_save_lesson_positions', 'edd_ecourse_save_lesson_positions' );

/**
 * Add Lesson
 *
 * @since 1.0
 * @return void
 */
function edd_ecourse_add_lesson_cb() {

	// Security check.
	check_ajax_referer( 'edd_ecourse_do_ajax', 'nonce' );

	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_die( __( 'You do not have permission to add lessons.', 'edd-course' ) );
	}

	$title     = wp_unslash( $_POST['title'] );
	$course_id = absint( $_POST['course_id'] );
	$module_id = absint( $_POST['module_id'] );
	$position  = absint( $_POST['position'] );

	if ( empty( $title ) ) {
		wp_die( __( 'A lesson title is required.', 'edd-ecourse' ) );
	}

	$post_data = array(
		'post_title'   => wp_strip_all_tags( $title ),
		'post_content' => '',
		'post_status'  => 'draft',
		'post_type'    => 'ecourse_lesson',
		'menu_order'   => $position
	);

	$lesson_id = wp_insert_post( $post_data );

	if ( empty( $lesson_id ) ) {
		wp_die( __( 'Error creating lesson.', 'edd-ecourse' ) );
	}

	update_post_meta( $lesson_id, 'course', absint( $course_id ) );
	update_post_meta( $lesson_id, 'module', absint( $module_id ) );
	update_post_meta( $lesson_id, 'lesson_type', 'text' );

	$lesson = get_post( $lesson_id );

	ob_start();
	?>
	<li data-id="<?php echo esc_attr( $lesson->ID ); ?>" data-position="<?php echo esc_attr( $position ); ?>">
		<span class="edd-ecourse-lesson-title">
			<a href="<?php echo esc_url( get_edit_post_link( $lesson->ID ) ); ?>"><?php echo esc_html( $lesson->post_title ); ?></a>
		</span>
		<span class="edd-ecourse-lesson-status edd-ecourse-lesson-status-<?php echo sanitize_html_class( $lesson->post_status ); ?>"><?php echo esc_html( edd_ecourse_get_lesson_status( $lesson ) ); ?></span>
		<span class="edd-ecourse-lesson-actions">
			<a href="<?php echo esc_url( get_edit_post_link( $lesson->ID ) ); ?>" class="edd-ecourse-lesson-edit-link edd-ecourse-tip" title="<?php esc_attr_e( 'Edit', 'edd-ecourse' ); ?>"><span class="dashicons dashicons-edit"></span></a>
			<a href="<?php echo esc_url( get_permalink( $lesson->ID ) ); ?>" target="_blank" class="edd-ecourse-lesson-preview-link edd-ecourse-tip" title="<?php esc_attr_e( 'View', 'edd-ecourse' ); ?>"><span class="dashicons dashicons-visibility"></span></a>
			<a href="#" class="edd-ecourse-lesson-delete edd-ecourse-tip" title="<?php esc_attr_e( 'Delete', 'edd-ecourse' ); ?>"><span class="dashicons dashicons-trash"></span></a>
		</span>
	</li>
	<?php
	wp_send_json_success( ob_get_clean() );

	exit;

}

add_action( 'wp_ajax_edd_ecourse_add_lesson', 'edd_ecourse_add_lesson_cb' );

/**
 * Delete Lesson
 *
 * @since 1.0
 * @return void
 */
function edd_ecourse_delete_lesson_cb() {

	// Security check.
	check_ajax_referer( 'edd_ecourse_do_ajax', 'nonce' );

	if ( ! current_user_can( 'delete_posts' ) ) {
		wp_die( __( 'You do not have permission to delete lessons.', 'edd-course' ) );
	}

	$lesson_id = absint( $_POST['lesson_id'] );

	wp_delete_post( $lesson_id, true );

	wp_send_json_success();

	exit;

}

add_action( 'wp_ajax_edd_ecourse_delete_lesson', 'edd_ecourse_delete_lesson_cb' );

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