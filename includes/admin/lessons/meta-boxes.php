<?php
/**
 * Lesson Meta Boxes
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
 * Register Meta boxes
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_register_lesson_meta_boxes() {

	// Course Module Meta Box
	add_meta_box( 'lesson_details', __( 'Lesson Details', 'edd-ecourse' ), 'edd_ecourse_render_lesson_details_box', 'ecourse_lesson', 'side', 'default' );

	// Lesson Permission Meta Box
	add_meta_box( 'lesson_permission', __( 'Permissions', 'edd-ecourse' ), 'edd_ecourse_render_lesson_permissions_box', 'ecourse_lesson', 'side', 'default' );

}

add_action( 'add_meta_boxes', 'edd_ecourse_register_lesson_meta_boxes' );

/**
 * Render Lesson Details
 *
 * @param WP_Post $post
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_render_lesson_details_box( $post ) {

	wp_nonce_field( 'edd_ecourse_save_lesson_details', 'save_lesson_details_nonce' );

	do_action( 'edd_ecourse_lesson_details_meta_box_before', $post );

	$courses = edd_ecourse_get_courses();

	// Previously saved values
	$selected_course = edd_ecourse_get_lesson_course( $post );
	$selected_module = edd_ecourse_get_lesson_module( $post );
	$lesson_type     = edd_ecourse_get_lesson_type( $post );
	$lesson_type     = is_array( $lesson_type ) ? $lesson_type : array();

	$modules = is_numeric( $selected_course ) ? edd_ecourse_get_course_modules( $selected_course ) : false;

	if ( is_array( $courses ) && count( $courses ) ) : ?>
		<p>
			<label for="course"><?php _e( 'Course', 'edd-ecourse' ); ?></label>
			<select id="course" name="course">
				<?php foreach ( $courses as $course ) : ?>
					<option value="<?php echo esc_attr( $course->id ); ?>" <?php selected( $selected_course, $course->id ); ?>><?php echo esc_html( $course->title ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<?php if ( false !== $modules ) : ?>
			<p>
				<label for="module"><?php _e( 'Module', 'edd-ecourse' ); ?></label>
				<select id="module" name="module">
					<?php foreach ( $modules as $module ) : ?>
						<option value="<?php echo esc_attr( $module->id ); ?>" <?php selected( $selected_module, $module->id ); ?>><?php echo esc_html( $module->title ); ?></option>
					<?php endforeach; ?>
				</select>
			</p>
		<?php endif; ?>
	<?php endif; ?>

	<p>
		<label for="lesson_type"><?php _e( 'Lesson Type', 'edd-ecourse' ); ?></label>
		<select id="lesson_type" name="lesson_type[]" multiple>
			<?php foreach ( edd_ecourse_get_available_lesson_types() as $id => $options ) : ?>
				<option value="<?php echo esc_attr( $id ); ?>"<?php echo in_array( $id, $lesson_type ) ? ' selected' : ''; ?>><?php echo esc_html( $options['name'] ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>
	<?php

	do_action( 'edd_ecourse_lesson_details_meta_box_after', $post );

}

/**
 * Render Lesson Permissions
 *
 * @param WP_Post $post
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_render_lesson_permissions_box( $post ) {

	wp_nonce_field( 'edd_ecourse_save_lesson_permissions', 'save_lesson_permissions_nonce' );

	do_action( 'edd_ecourse_lesson_permissions_meta_box_before', $post );

	do_action( 'edd_ecourse_lesson_permissions_meta_box_after', $post );

}

/**
 * Save Lesson Meta
 *
 * @param int     $post_id
 * @param WP_Post $post
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_save_lesson_meta( $post_id, $post ) {

	// Nonce doesn't exist - bail.
	if ( ! isset( $_POST['save_lesson_details_nonce'] ) ) {
		return;
	}

	// Nonce can't be verified - bail.
	if ( ! wp_verify_nonce( $_POST['save_lesson_details_nonce'], 'edd_ecourse_save_lesson_details' ) ) {
		return;
	}

	// Autosave or form isn't submitted - bail.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Permission check.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	/** Let's saaaave! */

	do_action( 'edd_ecourse_save_lesson_meta', $post );

	// Course ID
	$course_id = array_key_exists( 'course', $_POST ) ? $_POST['course'] : false;
	if ( $course_id && is_numeric( $course_id ) ) {
		update_post_meta( $post_id, 'course', absint( $course_id ) );
	} else {
		delete_post_meta( $post_id, 'course' );
	}

	// Module ID
	$module_id = array_key_exists( 'module', $_POST ) ? $_POST['module'] : false;
	if ( $module_id && is_numeric( $module_id ) ) {
		update_post_meta( $post_id, 'module', absint( $module_id ) );
	} else {
		delete_post_meta( $post_id, 'module' );
	}

	// Lesson type
	$type = array_key_exists( 'lesson_type', $_POST ) ? $_POST['lesson_type'] : false;
	if ( $type && is_array( $type ) ) {
		$sanitized_type = array_map( 'sanitize_text_field', $type );
		update_post_meta( $post_id, 'lesson_type', $sanitized_type );
	} else {
		delete_post_meta( $post_id, 'lesson_type' );
	}

	// Set the lesson position in the module.
	$position = edd_ecourse_get_lesson_position( $post_id );
	if ( empty( $position ) ) {
		$new_position = ( $module_id && is_numeric( $module_id ) ) ? $module_id : 1;
		update_post_meta( $post_id, 'lesson_position', edd_ecourse_get_number_module_lessons( absint( $new_position ) ) );
	}

}

add_action( 'save_post', 'edd_ecourse_save_lesson_meta', 10, 2 );