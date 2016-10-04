<?php
/**
 * User Profiles
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
 * User Profile Fields
 *
 * Adds fields for managing e-course permissions.
 *
 * @param WP_User $user
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_show_user_profile( $user ) {

	if ( ! current_user_can( 'edit_users' ) ) {
		return;
	}

	$courses = edd_ecourse_get_courses();

	if ( ! is_array( $courses ) || ! count( $courses ) ) {
		return;
	}

	?>
	<hr>
	<h3><?php _e( 'E-Course Permissions', 'edd-ecourses' ); ?></h3>
	<p><?php _e( 'Uncheck to revoke access to a course. Check to grant access.', 'edd-ecourse' ); ?></p>
	<table class="form-table">
		<?php foreach ( $courses as $course ) :
			$capability = user_can( $user, 'view_course_' . $course->id );
			?>
			<tr>
				<th>
					<?php echo esc_html( $course->title ); ?>
				</th>
				<td>
					<label for="edd-ecourse-<?php echo esc_attr( $course->id ); ?>">
						<input type="checkbox" id="edd-ecourse-<?php echo esc_attr( $course->id ); ?>" name="edd_view_ecourse_<?php echo absint( $course->id ); ?>" value="1" <?php checked( $capability, true ); ?>>
						<?php _e( 'Grant access', 'edd-ecourse' ); ?>
					</label>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
	<?php

}

add_action( 'show_user_profile', 'edd_ecourse_show_user_profile' );
add_action( 'edit_user_profile', 'edd_ecourse_show_user_profile' );

/**
 * Save User Profile Details
 *
 * Adds or removes capabilities as per the checkbox options.
 *
 * @param int $user_id ID of the user being saved.
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_edit_user_profile( $user_id ) {

	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return;
	}

	$user    = new WP_User( $user_id );
	$courses = edd_ecourse_get_courses();

	if ( ! is_array( $courses ) || ! count( $courses ) ) {
		return;
	}

	do_action( 'edd_ecourse_save_user_profile', $user, $courses );

	foreach ( $courses as $course ) {
		$capability = user_can( $user, 'view_course_' . $course->id );

		// Get the value we saved.
		$permission = array_key_exists( 'edd_view_ecourse_' . absint( $course->id ), $_POST ) ? $_POST[ 'edd_view_ecourse_' . absint( $course->id ) ] : false;

		if ( $permission && ! user_can( $user, $capability ) ) {
			$user->add_cap( $capability );
		} elseif ( ! $permission && user_can( $user, $capability ) ) {
			$user->remove_cap( $capability );
		}
	}

}

add_action( 'personal_options_update', 'edd_ecourse_edit_user_profile' );
add_action( 'edit_user_profile_update', 'edd_ecourse_edit_user_profile' );