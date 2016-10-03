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
				<div class="edd-ecourse" data-course-id="<?php echo esc_attr( $course->id ); ?>">
					<div class="edd-ecourse-inner">
						<h2><?php echo esc_html( $course->title ); ?></h2>

						<div class="edd-ecourse-actions">
							<a href="<?php echo esc_url( edd_ecourse_get_manage_course_url( $course->id ) ); ?>" class="button edd-ecourse-tip edd-ecourse-action-edit" title="<?php esc_attr_e( 'Manage Course', 'edd-ecourse' ); ?>">
								<span class="dashicons dashicons-edit"></span>
							</a>

							<button href="#" class="button edd-ecourse-tip edd-ecourse-action-delete" title="<?php esc_attr_e( 'Delete Course', 'edd-ecourse' ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'delete_course_' . $course->id ) ); ?>">
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
 * Render View E-Course Lesson List
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_render_course_edit() {

	if ( ! isset( $_GET['course'] ) ) {
		wp_die( __( 'Missing course ID.', 'edd-ecourse' ) );
	}

	$course = edd_ecourse_get_course( absint( $_GET['course'] ) );

	if ( ! $course ) {
		wp_die( __( 'Invalid course ID.', 'edd-ecourse' ) );
	}

	$modules = edd_ecourse_get_course_modules( $course->id );
	?>
	<h1 id="edd-ecourse-title" data-course="<?php echo esc_attr( $course->id ); ?>">
		<span><?php echo esc_html( $course->title ); ?></span>
		<button id="edd-ecourse-edit-course-title" class="page-title-action"><?php _e( 'Edit Title', 'edd-ecourse' ); ?></button>
	</h1>

	<div id="poststuff">
		<div id="edd-ecourse-dashboard-widgets-wrap">
			<div id="post-body" class="metabox-holder columns-2">

				<div id="post-body-content">
					<div id="postdivrich" class="postarea">
						<?php // @todo tinymce description ?>
					</div>
				</div>

				<div id="postbox-container-1" class="postbox-container">
					<div id="side-sortables" class="meta-box-sortables ui-sortable">

						<!-- Details -->
						<div id="edd-ecourse-details" class="postbox">
							<h3 class="hndle"><?php _e( 'Course Details', 'edd-ecourse' ); ?></h3>
							<div class="inside">
								<div class="edd-admin-box">
									<div class="edd-admin-box-inside">
										<p>
											<label for="course-start-date" class="label"><?php _e( 'Start Date', 'edd-ecourse' ); ?></label>
											<span class="edd-help-tip dashicons dashicons-editor-help" title="<?php esc_attr_e( 'Enter a start date if you wish to pre-sell the course. People will be able to buy the course but won\'t get access to the lessons until the start date.', 'edd-ecourse' ); ?>"></span>
											<input type="text" id="course-start-date" name="course_start_date" class="large-text" value="<?php echo esc_attr( $course->start_date ); ?>">
										</p>
										<p class="description"><?php printf( __( 'Sample format: %s', 'edd-ecourse' ), date( 'F jS Y', strtotime( 'first day of next month' ) ) ); ?></p>
									</div>
								</div>
							</div>
						</div>

						<?php // @todo maybe featured image ?>

					</div>
				</div>

				<div id="postbox-container-2" class="postbox-container">
					<div id="edd-ecourse-module-sortables" class="meta-box-sortables ui-sortable">

						<!-- Modules -->
						<?php if ( is_array( $modules ) ) : ?>

							<?php foreach ( $modules as $module ) : ?>
								<div class="postbox edd-ecourse-module-group" data-module="<?php echo esc_attr( $module->id ); ?>">
									<h3 class="hndle">
										<span class="edd-ecourse-module-title"><?php echo esc_html( $module->title ); ?></span>
										<button class="button edd-ecourse-edit-module-title"><?php _e( 'Edit', 'edd-ecourse' ); ?></button>
										<a href="<?php echo esc_url( edd_ecourse_get_add_lesson_url( $course->id, $module->id ) ); ?>" class="button button-primary edd-ecourse-add-module-lesson"><?php _e( 'Add Lesson', 'edd-ecourse' ); ?></a>
									</h3>
									<div class="inside">

										<!-- lessons here -->

									</div>
								</div>
							<?php endforeach; ?>

						<?php endif; ?>

						<div class="postbox edd-ecourse-add-module">
							<h3 class="hndle"><?php _e( 'Add Module', 'edd-ecourse' ) ?></h3>
							<div class="inside">
								<form id="edd-ecourse-add-module-form" method="POST">
									<label for="edd-ecourse-module-name" class="screen-reader-text"><?php _e( 'Enter module name', 'edd-ecourse' ); ?></label>
									<input type="text" id="edd-ecourse-module-name" placeholder="<?php esc_attr_e( 'Module name', 'edd-ecourse' ); ?>" required>
									<button type="submit" class="button"><?php _e( 'Add Module', 'edd-ecourse' ); ?></button>
									<input type="hidden" id="edd-ecourse-id" value="<?php echo esc_attr( $course->id ); ?>">
									<?php wp_nonce_field( 'edd_ecourse_add_module', 'edd_ecourse_add_module_nonce' ); ?>
								</form>
							</div>
						</div>

					</div>
				</div>

			</div>
		</div>
	</div>
	<?php

}

add_action( 'edd_ecourse_render_course_edit', 'edd_ecourse_render_course_edit' );