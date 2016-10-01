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

	$courses = edd_ecourse_get_courses();
	?>
	<div class="wrap">
		<h1><?php _e( 'E-Courses', 'edd-ecourse' ); ?></h1>

		<div id="edd-course-grid">
			<?php
			if ( is_array( $courses ) ) {

				foreach ( $courses as $course ) {

					?>
					<div class="edd-ecourse" data-course-id="<?php echo esc_attr( $course->term_id ); ?>">
						<div class="edd-course-inner">
							<h2><?php echo esc_html( $course->name ); ?></h2>

							<div class="edd-course-actions">
								<a href="#" class="edd-ecourse-tip edd-course-action-lessons" title="<?php esc_attr_e( 'View Lessons', 'edd-ecourse' ); ?>">
									<span class="dashicons dashicons-list-view"></span>
								</a>

								<a href="#" class="edd-ecourse-tip edd-course-action-edit" title="<?php esc_attr_e( 'Edit Course', 'edd-ecourse' ); ?>">
									<span class="dashicons dashicons-edit"></span>
								</a>

								<a href="#" class="edd-ecourse-tip edd-course-action-delete" title="<?php esc_attr_e( 'Delete Course', 'edd-ecourse' ); ?>">
									<span class="dashicons dashicons-trash"></span>
								</a>
							</div>
						</div>
					</div>
					<?php

				}

			} else {

			}
			?>
		</div>
	</div>
	<?php

}