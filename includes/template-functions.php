<?php
/**
 * Template Functions
 *
 * For modifying the front-end of the site.
 *
 * @todo      Plan for these pages:
 *       Dashboard - widgetized
 *       Course list (including ones not purchased)
 *       Single course page with list of modules/lessons
 *       Single lesson view
 *
 * @package   edd-ecourse
 * @copyright Copyright (c) 2016, Ashley Gibson
 * @license   GPL2+
 */

/**
 * Get Templates Directory
 *
 * @since 1.0.0
 * @return string
 */
function edd_ecourse_get_templates_dir() {
	return EDD_ECOURSE_DIR . 'templates';
}

/**
 * Get Templates URL
 *
 * @since 1.0.0
 * @return string
 */
function edd_ecourse_get_templates_url() {
	return EDD_ECOURSE_URL . 'templates';
}

/**
 * Add Path for E-Course Templates
 *
 * @param array $paths
 *
 * @since 1.0.0
 * @return array
 */
function edd_ecourse_template_paths( $paths ) {
	$paths[96] = edd_ecourse_get_templates_dir();

	return $paths;
}

add_filter( 'edd_template_paths', 'edd_ecourse_template_paths' );

/**
 * Check: Is Course Page
 *
 * Checks whether or not we're on an official e-course page.
 *
 * @uses  edd_ecourse_is_dashboard_page()
 *
 * @since 1.0.0
 * @return bool
 */
function edd_ecourse_is_course_page() {

	$is_course_page = false;

	// Dashboard page.
	if ( edd_ecourse_is_dashboard_page() ) {
		$is_course_page = true;
	}

	// Course lesson list.
	if ( edd_ecourse_is_course_archive() ) {
		$is_course_page = true;
	}

	// Single lesson.
	if ( is_singular( 'ecourse_lesson' ) ) {
		$is_course_page = true;
	}

	return apply_filters( 'edd_ecourse_is_course_page', $is_course_page );

}

/**
 * Is Course Archive Page
 *
 * Checks to see if we're on a public course archive page.
 *
 * @since 1.0.0
 * @return bool
 */
function edd_ecourse_is_course_archive() {
	$course_slug       = get_query_var( edd_ecourse_get_endpoint() );
	$is_course_archive = $course_slug ? true : false;

	return apply_filters( 'edd_ecourse_is_course_archive', $is_course_archive, $course_slug );
}

/**
 * Is Dashboard Page
 *
 * Returns true if the current page is the chosen dashboard page.
 *
 * @since 1.0.0
 * @return bool
 */
function edd_ecourse_is_dashboard_page() {
	global $post;

	$dashboard         = edd_get_option( 'ecourse_dashboard_page' );
	$is_dashboard_page = is_object( $post ) && $post->ID == $dashboard;

	return apply_filters( 'edd_ecourse_is_dashboard_page', $is_dashboard_page, $dashboard, $post );
}

/**
 * Modify Styles Queue
 *
 * Removes all theme CSS from the stylesheet queue. This is so we
 * can add our own styles without interference.
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_modify_styles_queue() {

	// Bail if not on an e-course page.
	if ( ! edd_ecourse_is_course_page() ) {
		return;
	}

	global $wp_styles;

	$new_queue = array();

	$upload_dir = wp_get_upload_dir();

	foreach ( $wp_styles->queue as $handle ) {
		if ( ! array_key_exists( $handle, $wp_styles->registered ) ) {
			continue;
		}

		// Only add to new queue if the style isn't from the theme.
		if ( false === strpos( $wp_styles->registered[ $handle ]->src, get_theme_root_uri() ) && false === strpos( $wp_styles->registered[ $handle ]->src, $upload_dir['baseurl'] ) ) {
			$new_queue[] = $handle;
		}
	}

	$wp_styles->queue = $new_queue;

}

add_action( 'wp_print_styles', 'edd_ecourse_modify_styles_queue' );

/**
 * Template Include
 *
 * Overrides which template gets displayed. Checks to see if the current
 * post ID matches one of the selected pages in the settings panel. If so,
 * a specific template is loaded. If not, the default template is used.
 *
 * @param string $template
 *
 * @since 1.0.0
 * @return string
 */
function edd_ecourse_template_include( $template ) {

	global $post;

	if ( edd_ecourse_is_course_page() ) {
		$course_template = edd_get_template_part( 'ecourse', 'index', false );

		if ( $course_template ) {
			$template = $course_template;
		}
	}

	// Dashboard page.
	if ( edd_ecourse_is_dashboard_page() ) {

	} elseif ( edd_ecourse_is_course_archive() ) {

		// Course archive page.

		// Set global variable.
		global $edd_ecourse;

		$course = edd_ecourse_get_current_course();

		if ( $course ) {
			$edd_ecourse = $course;
		}

	} elseif ( is_singular( 'ecourse_lesson' ) ) {

		// Lesson page.

		// Set global variable.
		global $edd_ecourse;

		$course_id = edd_ecourse_get_lesson_course( $post );

		if ( $course_id ) {
			$course = edd_ecourse_load()->courses->get_course_by( 'id', absint( $course_id ) );

			if ( $course ) {
				$edd_ecourse = $course;
			}
		}

	}

	return apply_filters( 'edd_ecourse_template_include', $template, $post );

}

add_action( 'template_include', 'edd_ecourse_template_include' );

/**
 * Get Sidebar
 *
 * Includes a sidebar file, depending on which page we're on.
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_get_sidebar() {

	if ( is_singular( 'ecourse_lesson' ) ) {
		edd_get_template_part( 'ecourse', 'sidebar-lesson' );
	} else {
		edd_get_template_part( 'ecourse', 'sidebar' );
	}

}

/**
 * Get Dashboard URL
 *
 * URL to the e-course dashboard page.
 *
 * @todo  Maybe move to misc. functions.
 *
 * @since 1.0.0
 * @return string
 */
function edd_ecourse_get_dashboard_url() {
	$dashboard = edd_get_option( 'ecourse_dashboard_page' );

	if ( $dashboard ) {
		$url = get_permalink( $dashboard );
	} else {
		$url = false;
	}

	return apply_filters( 'edd_ecourse_dashboard_url', $url, $dashboard );
}

/**
 * Load Page Template
 *
 * Loads the correct e-course page template for the current page.
 * This gets run after permissions are confirmed.
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_load_page_template() {

	if ( edd_ecourse_is_dashboard_page() ) {

		edd_get_template_part( 'ecourse', 'dashboard' );

	} elseif ( $course_slug = get_query_var( edd_ecourse_get_endpoint() ) ) {

		edd_get_template_part( 'ecourse', 'archive' );

	} elseif ( is_singular( 'ecourse_lesson' ) ) {

		edd_get_template_part( 'ecourse', 'lesson' );

	}

}