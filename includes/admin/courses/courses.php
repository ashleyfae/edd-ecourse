<?php
/**
 * E-Courses Page
 *
 * @package   EDD\E-Course\Admin\Courses
 * @copyright Copyright (c) 2017, Ashley Gibson
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
		<a href="<?php echo esc_url( edd_ecourse_get_add_course_url() ); ?>" class="page-title-action" data-nonce="<?php echo esc_attr( wp_create_nonce( 'edd_ecourse_add_course' ) ); ?>"><?php _e( 'Add New', 'edd-ecourse' ); ?></a>
	</h1>

	<div id="edd-ecourse-grid">
	<?php
	if ( is_array( $courses ) ) {

		foreach ( $courses as $course ) {
			$download       = edd_ecourse_get_course_download( $course->ID );
			$number_lessons = edd_ecourse_get_number_course_lessons( $course->ID );
			$sales          = $download ? edd_get_download_earnings_stats( $download->ID ) : 0;
			$students       = $download ? edd_get_download_sales_stats( $download->ID ) : 0;
			?>
			<div class="edd-ecourse" data-course-id="<?php echo esc_attr( $course->ID ); ?>">
				<div class="edd-ecourse-inner">
					<h2><?php echo esc_html( $course->post_title ); ?></h2>

					<div class="edd-ecourse-stats">
						<div class="edd-ecourse-lessons">
							<?php printf( _n( '%s Lesson', '%s Lessons', $number_lessons, 'edd-ecourse' ), '<strong>' . $number_lessons . '</strong>' ); ?>
						</div>
						<div class="edd-ecourse-sales">
							<?php printf( __( '%s Sales', 'edd-ecourse' ), '<strong>' . edd_currency_filter( $sales ) . '</strong>' ); ?>
						</div>
						<div class="edd-ecourse-students">
							<?php printf( __( '%s Students', 'edd-ecourse' ), '<strong>' . $students . '</strong>' ); ?>
						</div>
					</div>

					<div class="edd-ecourse-product">
						<?php if ( $download ) : ?>
							<p><?php printf( __( 'Product: %s', 'edd-ecourse' ), '<a href="' . esc_url( get_edit_post_link( $download->ID ) ) . '">' . esc_html( $download->post_title ) . '</a>' ); ?></p>
						<?php else : ?>
							<p><?php printf( __( 'This course doesn\'t have an associated product yet. Would you like to <a href="%s">create one?</a>', 'edd-ecourse' ), esc_url( admin_url( 'post-new.php?post_type=download&course=' . $course->ID ) ) ); ?></p>
						<?php endif; ?>
					</div>

					<div class="edd-ecourse-actions">
						<a href="<?php echo esc_url( edd_ecourse_get_manage_course_url( $course->ID ) ); ?>" class="button edd-ecourse-tip edd-ecourse-action-edit" title="<?php esc_attr_e( 'Manage Course', 'edd-ecourse' ); ?>">
							<span class="dashicons dashicons-edit"></span>
						</a>

						<a href="<?php echo esc_url( get_permalink( $course ) ); ?>" class="button edd-ecourse-tip edd-ecourse-action-view" title="<?php esc_attr_e( 'View Course', 'edd-ecourse' ); ?>" target="_blank">
							<span class="dashicons dashicons-visibility"></span>
						</a>

						<button href="#" class="button edd-ecourse-tip edd-ecourse-action-delete" title="<?php esc_attr_e( 'Delete Course', 'edd-ecourse' ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'delete_course_' . $course->ID ) ); ?>">
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

	global $post;

	if ( ! isset( $_GET['course'] ) ) {
		wp_die( __( 'Missing course ID.', 'edd-ecourse' ) );
	}

	$course = get_post( absint( $_GET['course'] ) );
	$post   = $course;

	if ( ! $course ) {
		wp_die( __( 'Invalid course ID.', 'edd-ecourse' ) );
	}

	$modules = edd_ecourse_get_course_modules( $course->ID );
	?>
	<h1 id="edd-ecourse-title" data-course="<?php echo esc_attr( $course->ID ); ?>">
		<span><?php echo esc_html( $course->post_title ); ?></span>
		<button id="edd-ecourse-edit-course-title" class="page-title-action"><?php _e( 'Edit Title', 'edd-ecourse' ); ?></button>
	</h1>

	<div id="poststuff">
		<div id="edd-ecourse-dashboard-widgets-wrap">
			<div id="post-body" class="metabox-holder columns-2">

				<div id="post-body-content">
					<div id="edit-slug-box" class="hide-if-no-js">
						<strong><?php _e( 'Permalink:', 'edd-ecourse' ); ?></strong>
						<span id="sample-permalink">
							<?php // @todo Make this editable ?>
							<a href="<?php echo esc_url( get_permalink( $course ) ); ?>" target="_blank">
								<?php echo home_url( '/course/' ) . '<span id="editable-post-name">' . esc_html( $course->post_name ) . '</span>/'; ?><?php // @todo fix this ?>
							</a>
						</span>
						&lrm;
						<span id="edit-slug-buttons">
							<button type="button" class="edit-slug button button-small hide-if-no-js" aria-label="<?php esc_attr_e( 'Edit permalink', 'edd-ecourse' ); ?>"><?php _e( 'Edit', 'edd-ecourse' ); ?></button>
						</span>
					</div>

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
											<label for="course_status" class="label"><?php _e( 'Status:', 'edd-ecourse' ); ?></label>
											<select id="course_status" name="course_status">
												<option value="draft" <?php selected( $course->post_status, 'draft' ); ?>><?php _e( 'Draft', 'edd-ecourse' ); ?></option>
												<option value="publish" <?php selected( $course->post_status, 'publish' ); ?>><?php _e( 'Published', 'edd-ecourse' ); ?></option>
												<option value="future" <?php selected( $course->post_status, 'future' ); ?>><?php _e( 'Scheduled', 'edd-ecourse' ); ?></option>
											</select>
											<span class="edd-help-tip dashicons dashicons-editor-help" title="<?php esc_attr_e( '"Draft" to make all lessons private, "Published" to make the course available for purchase, "Scheduled" to schedule the release in the future for pre-selling.', 'edd-ecourse' ); ?>"></span>
										</p>

										<div id="ecourse-start-date-wrap"<?php echo 'future' != $course->post_status ? ' style="display: none;"' : ''; ?>>
											<p>
												<label for="course-start-date" class="label"><?php _e( 'Start Date', 'edd-ecourse' ); ?></label>
												<span class="edd-help-tip dashicons dashicons-editor-help" title="<?php esc_attr_e( 'Enter a start date if you wish to pre-sell the course. People will be able to buy the course but won\'t get access to the lessons until the start date.', 'edd-ecourse' ); ?>"></span>
												<input type="text" id="course-start-date" name="course_start_date" class="large-text" value="<?php echo esc_attr( edd_ecourse_get_readable_course_date( $course ) ); ?>">
												<span class="description"><?php printf( __( 'Sample format: %s', 'edd-ecourse' ), date( 'F jS Y', strtotime( 'first day of next month' ) ) ); ?></span>
											</p>
										</div>

										<div id="major-publishing-actions">
											<p id="ecourse-save">
												<button type="button" id="ecourse-save-status" class="button button-primary"><?php _e( 'Save', 'edd-ecourse' ); ?></button>
											</p>
										</div>
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
						<?php if ( is_array( $modules ) ) { ?>

							<?php foreach ( $modules as $module ) { ?>
								<div class="postbox edd-ecourse-module-group" data-module="<?php echo esc_attr( $module->id ); ?>">
									<h3 class="hndle">
										<span class="edd-ecourse-module-title"><?php echo esc_html( $module->title ); ?></span>
										<button class="button edd-ecourse-edit-module-title"><?php _e( 'Edit', 'edd-ecourse' ); ?></button>
										<a href="<?php echo esc_url( edd_ecourse_get_add_lesson_url( $course->id, $module->id ) ); ?>" class="button edd-ecourse-add-module-lesson"><?php _e( 'Add Lesson', 'edd-ecourse' ); ?></a>
										<a href="#" class="edd-ecourse-delete-module"><span class="dashicons dashicons-trash"></span></a>
									</h3>
									<div class="inside">

										<?php
										$lessons = edd_ecourse_get_module_lessons( $module->id );

										if ( is_array( $lessons ) ) {
											?>
											<ul class="edd-ecourse-lesson-list">
												<?php foreach ( $lessons as $lesson ) { ?>
													<li data-id="<?php echo esc_attr( $lesson->ID ); ?>" data-position="<?php echo esc_attr( 1 ); ?>">
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
												<?php } ?>
											</ul>
											<?php
										}
										?>

									</div>
								</div>
							<?php } ?>

						<?php } ?>

						<div class="edd-ecourse-add-module">
							<input type="hidden" id="edd-ecourse-id" value="<?php echo esc_attr( $course->ID ); ?>">
							<input type="hidden" id="edd-ecourse-module-nonce" value="<?php echo esc_attr( wp_create_nonce( 'edd_ecourse_add_module' ) ); ?>">
							<button type="button" class="button button-primary"><?php _e( 'Add Module', 'edd-ecourse' ); ?></button>
						</div>

					</div>
				</div>

				<?php wp_nonce_field( 'edd_ecourse_manage_course', 'edd_ecourse_manage_course_nonce' ); ?>

			</div>
		</div>
	</div>
	<?php

}

add_action( 'edd_ecourse_render_course_edit', 'edd_ecourse_render_course_edit' );