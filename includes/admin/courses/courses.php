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
					<div class="edd-ecourse">
						<div class="edd-course-inner">
							<h2><?php echo esc_html( $course->name ); ?></h2>
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