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

							<a href="<?php echo esc_url( edd_ecourse_get_edit_course_url( $course->term_id ) ); ?>" class="button edd-ecourse-tip edd-ecourse-action-edit" title="<?php esc_attr_e( 'Edit Course', 'edd-ecourse' ); ?>">
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

	if ( ! isset( $_GET['course'] ) ) {
		wp_die( __( 'Missing course ID.', 'edd-ecourse' ) );
	}

	$course = get_term( absint( $_GET['course'] ), 'ecourse' );

	if ( is_wp_error( $course ) ) {
		wp_die( __( 'Invalid course ID.', 'edd-ecourse' ) );
	}

	?>
	<h1>
		<?php printf( __( 'Edit %s', 'edd-ecourse' ), esc_html( $course->name ) ); ?>
	</h1>

	<form id="edd-ecourse-edit-course" method="POST">
		<div id="poststuff">
			<div id="edd-ecourse-dashboard-widgets-wrap">
				<div id="post-body" class="metabox-holder columns-2">

					<div id="post-body-content">
						<div id="titlediv">
							<div id="titlewrap">
								<label class="screen-reader-text" for="title"><?php _e( 'Enter title here', 'edd-ecourse' ); ?></label>
								<input type="text" name="course_title" id="title" size="30" value="<?php echo esc_attr( $course->name ); ?>" spellcheck="true" autocomplete="off">
							</div>
						</div>

						<div id="postdivrich" class="postarea">
							<?php // @todo tinymce description ?>
						</div>
					</div>

					<div id="postbox-container-1" class="postbox-container">
						<div id="side-sortables" class="meta-box-sortables ui-sortable">

							<!-- Save -->
							<div id="edd-ecourse-update" class="postbox">
								<h3 class="hndle"><?php _e( 'Update Course', 'edd-ecourse' ); ?></h3>
								<div class="inside">
									<?php submit_button( __( 'Save Course', 'edd-ecourse' ), 'primary', 'submit', false ); ?>
								</div>
							</div>

							<!-- Details -->
							<div id="edd-ecourse-details" class="postbox">
								<h3 class="hndle"><?php _e( 'Course Details', 'edd-ecourse' ); ?></h3>
								<div class="inside">
									<div class="edd-admin-box">
										<div class="edd-admin-box-inside">
											<p>
												<label for="course-start-date" class="label"><?php _e( 'Start Date', 'edd-ecourse' ); ?></label>
												<span class="edd-help-tip dashicons dashicons-editor-help" title="<?php esc_attr_e( 'Enter a start date if you wish to pre-sell the course. People will be able to buy the course but won\'t get access to the lessons until the start date.', 'edd-ecourse' ); ?>"></span>
												<input type="text" id="course-start-date" name="course_start_date" class="large-text">
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
						<div id="normal-sortables" class="meta-box-sortables ui-sortable">

							<!-- Modules -->
							<div id="edd-ecourse-modules" class="postbox">
								<h3 class="hndle"><?php _e( 'Modules', 'edd-ecourse' ); ?></h3>
								<div class="inside">
									<?php
									$modules = edd_ecourse_get_course_modules( $course->term_id );

									// @todo remove these
									$modules = array(
										'Introduction',
										'Design',
										'Marketing'
									);
									?>

									<ul id="edd-ecourse-module-list">
										<?php
										if ( is_array( $modules ) ) {
											foreach ( $modules as $name ) {
												?>
												<li data-module-name="<?php echo esc_attr( $name ); ?>">
													<?php echo esc_html( $name ); ?>
													<span class="dashicons dashicons-trash delete-module"></span>
												</li>
												<?php
											}
										}
										?>
									</ul>

									<form id="edd-ecourse-add-module">
										<label for="edd-ecourse-module-name" class="screen-reader-text"><?php _e( 'Enter module name', 'edd-ecourse' ); ?></label>
										<input type="text" id="edd-ecourse-module-name" placeholder="<?php esc_attr_e( 'Module name', 'edd-ecourse' ); ?>" required>
										<button type="submit" class="button"><?php _e( 'Add Module', 'edd-ecourse' ); ?></button>
									</form>
								</div>
							</div>

						</div>
					</div>

				</div>
			</div>
		</div>

		<?php wp_nonce_field( 'edd_update_payment_details_nonce' ); ?>
		<input type="hidden" name="edd_ecourses_course_id" value="<?php echo esc_attr( $course->term_id ); ?>">
		<input type="hidden" name="edd_action" value="ecourse_update_course">
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

	$modules = edd_ecourse_get_course_modules( $course->term_id );

	// @todo remove these
	/*$modules = array(
		'Introduction',
		'Design',
		'Marketing'
	);*/
	?>
	<h1>
		<?php printf( __( 'Lessons: %s', 'edd-ecourse' ), esc_html( $course->name ) ); ?>
		<a href="<?php echo esc_url( edd_ecourse_get_add_lesson_url( $course->term_id ) ); ?>" class="page-title-action"><?php _e( 'Add Lesson', 'edd-ecourse' ); ?></a>
	</h1>

	<div id="poststuff">
		<div id="edd-ecourse-dashboard-widgets-wrap">
			<div id="post-body" class="metabox-holder columns-1">

				<div id="postbox-container-1" class="postbox-container">
					<div id="normal-sortables" class="meta-box-sortables ui-sortable">

						<!-- Modules -->
						<?php if ( is_array( $modules ) ) : ?>

							<?php foreach ( $modules as $key => $name ) : ?>
								<div class="postbox edd-ecourse-module-group">
									<h3 class="hndle"><?php echo esc_html( $name ); ?></h3>
									<div class="inside">

										<!-- lessons here -->

									</div>
								</div>
							<?php endforeach; ?>

						<?php else : ?>

							<div id="edd-ecourse-add-first-module" class="postbox">
								<h3 class="hndle"><?php _e( 'Add Your First Module', 'edd-ecourse' ) ?></h3>
								<div class="inside">
									<p><?php _e('Your first step is to create'); ?></p>
								</div>
							</div>

						<?php endif; ?>

					</div>
				</div>

			</div>
		</div>
	</div>
	<?php

}

add_action( 'edd_ecourse_render_course_list', 'edd_ecourse_render_course_lesson_list' );