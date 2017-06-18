<?php
/**
 * Download Meta Boxes
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
 * Register Meta Boxes
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_register_download_meta_boxes() {

	// Course Module Meta Box
	add_meta_box( 'ecourse', __( 'E-Course', 'edd-ecourse' ), 'edd_ecourse_render_download_course_box', 'download', 'side', 'default' );

}

add_action( 'add_meta_boxes', 'edd_ecourse_register_download_meta_boxes' );

/**
 * Render Download Course Box
 *
 * @param WP_Post $post
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_render_download_course_box( $post ) {

	wp_nonce_field( 'edd_ecourse_save_lesson_details', 'save_lesson_details_nonce' );

	do_action( 'edd_ecourse_download_course_meta_box_before', $post );

	$courses         = edd_ecourse_get_courses();
	$selected_course = get_post_meta( $post->ID, 'ecourse', true );

	// Fallback for direct link with query arg.
	if ( ! $selected_course && ! empty( $_GET['course'] ) ) {
		$selected_course = absint( $_GET['course'] );
	}

	if ( is_array( $courses ) && count( $courses ) ) : ?>
		<p>
			<label for="ecourse"><strong><?php _e( 'Associated Course:', 'edd-ecourse' ); ?></strong>
				<span class="edd-help-tip dashicons dashicons-editor-help" title="<?php esc_attr_e( '<strong>Associated Course:</strong> Link this product to an e-course and when someone purchases the product they will receive access to the chosen course.', 'edd-ecourse' ); ?>"></span>
			</label>
		</p>
		<p>
			<select id="ecourse" name="ecourse">
				<option value="" <?php selected( $selected_course, false ); ?>><?php _e( 'None', 'edd-ecourse' ); ?></option>
				<?php foreach ( $courses as $course ) : ?>
					<option value="<?php echo esc_attr( $course->ID ); ?>" <?php selected( $selected_course, $course->ID ); ?>><?php echo esc_html( $course->post_title ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
	<?php endif;

	do_action( 'edd_ecourse_download_course_meta_box_after', $post );

}

/**
 * Save Meta
 *
 * @param int     $post_id ID of the post.
 * @param WP_Post $post    Post object.
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_save_download( $post_id, $post ) {

	$course = ( isset( $_POST['ecourse'] ) && is_numeric( $_POST['ecourse'] ) ) ? $_POST['ecourse'] : false;

	if ( $course ) {
		update_post_meta( $post_id, 'ecourse', absint( $course ) );
	} else {
		delete_post_meta( $post_id, 'ecourse' );
	}

}

add_filter( 'edd_save_download', 'edd_ecourse_save_download', 10, 2 );