<?php
/**
 * E-Courses Page
 *
 * @package   EDD\E-Course\Admin\Courses
 * @copyright Copyright (c) 2016, Ashley Gibson
 * @license   GPL2+
 * @since     1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render E-Course Page
 *
 * Displays a list of all available e-courses as well as an option to add a new one.
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_render_page() {

	$view = isset( $_GET['view'] ) ? wp_strip_all_tags( $_GET['view'] ) : 'overview';

	?>
	<div class="wrap">
		<?php do_action( 'edd_ecourse_render_course_' . $view ); ?>
	</div>
	<?php

}

/**
 * Render All E-Courses Grid
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_render_course_overview() {

	$courses = edd_ecourse_get_courses();
	?>
	<h1>
		<?php _e( 'E-Courses', 'edd-ecourse' ); ?>
		<a href="<?php echo esc_url( edd_ecourse_get_add_course_url() ); ?>" class="page-title-action"><?php _e( 'Add New', 'edd-ecourse' ); ?></a>
	</h1>

	<div id="edd-ecourse-add" class="metabox-holder">
		<div class="postbox">
			<h3 class="hndle"><?php _e( 'New E-Course', 'edd-ecourse' ); ?></h3>

			<form class="inside">
				<label for="edd-ecourse-name-new" class="screen-reader-text"><?php _e( 'E-Course Name', 'edd-ecourse' ); ?></label>
				<input type="text" id="edd-ecourse-name-new" placeholder="<?php esc_attr_e( 'Course name', 'edd-ecourse' ); ?>" required>
				<button type="submit" class="button"><?php _e( 'Create', 'edd-ecourse' ); ?></button>
				<?php wp_nonce_field( 'edd_ecourse_add_course', 'edd_ecourse_add_course_nonce' ); ?>
			</form>
		</div>
	</div>

	<div id="edd-ecourse-grid">
		<?php
		if ( is_array( $courses ) ) {

			foreach ( $courses as $course ) {

				?>
				<div class="edd-ecourse" data-course-id="<?php echo esc_attr( $course->term_id ); ?>">
					<div class="edd-ecourse-inner">
						<h2><?php echo esc_html( $course->name ); ?></h2>

						<div class="edd-ecourse-actions">
							<a href="<?php echo esc_url( edd_ecourse_get_view_lessons_url( $course->term_id ) ); ?>" class="button edd-ecourse-tip edd-ecourse-action-lessons" title="<?php esc_attr_e( 'View Lessons', 'edd-ecourse' ); ?>">
								<span class="dashicons dashicons-list-view"></span>
							</a>

							<a href="#" class="button edd-ecourse-tip edd-ecourse-action-edit" title="<?php esc_attr_e( 'Edit Course', 'edd-ecourse' ); ?>">
								<span class="dashicons dashicons-edit"></span>
							</a>

							<button href="#" class="button edd-ecourse-tip edd-ecourse-action-delete" title="<?php esc_attr_e( 'Delete Course', 'edd-ecourse' ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'delete_course_' . $course->term_id ) ); ?>">
								<span class="dashicons dashicons-trash"></span>
							</button>
						</div>
					</div>
				</div>
				<?php

			}

		} else {

		}
		?>
	</div>
	<?php

}

add_action( 'edd_ecourse_render_course_overview', 'edd_ecourse_render_course_overview' );

/**
 * Render Add E-Course Page
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_render_course_edit() {

	?>
	<h1>
		<?php _e( 'Edit E-Course', 'edd-ecourse' ); ?>
	</h1>

	<form id="edd-ecourse-edit-course" method="POST">
		<div id="poststuff">
			<div id="edd-ecourse-dashboard-widgets-wrap">
				<div id="post-body" class="metabox-holder columns-2">
					<div id="side-sortables" class="meta-box-sortables ui-sortable">

					</div>
				</div>
			</div>
		</div>
	</form>
	<?php

}

add_action( 'edd_ecourse_render_course_edit', 'edd_ecourse_render_course_edit' );

/**
 * Render View E-Course Lesson List
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_render_course_lesson_list() {

	if ( ! isset( $_GET['course'] ) ) {
		wp_die( __( 'Missing course ID.', 'edd-ecourse' ) );
	}

	$course = get_term( absint( $_GET['course'] ), 'ecourse' );

	if ( is_wp_error( $course ) ) {
		wp_die( __( 'Invalid course ID.', 'edd-ecourse' ) );
	}

	?>
	<h1>
		<?php printf( __( 'Lessons: %s', 'edd-ecourse' ), esc_html( $course->name ) ); ?>
		<a href="<?php echo esc_url( edd_ecourse_get_add_lesson_url( $course->term_id ) ); ?>" class="page-title-action"><?php _e( 'Add Lesson', 'edd-ecourse' ); ?></a>
	</h1>
	<?php

}

add_action( 'edd_ecourse_render_course_list', 'edd_ecourse_render_course_lesson_list' );