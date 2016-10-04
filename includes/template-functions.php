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
 * @todo  actually make this work
 *
 * @since 1.0.0
 * @return bool
 */
function edd_ecourse_is_course_page() {
	return false;
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

	foreach ( $wp_styles->queue as $handle ) {
		if ( ! array_key_exists( $handle, $wp_styles->registered ) ) {
			continue;
		}

		// Only add to new queue if the style isn't from the theme.
		if ( false === strpos( $wp_styles->registered[ $handle ]->src, get_theme_root_uri() ) ) {
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

	$dashboard = edd_get_option( 'ecourse_dashboard_page' );

	// Dashboard page.
	if ( is_object( $post ) && $dashboard == $post->ID ) {

	} elseif ( $course_slug = get_query_var( edd_ecourse_get_endpoint() ) ) {

		// Course lesson list.

	} elseif ( is_singular( 'ecourse_lesson' ) ) {

		// Lesson page.
		$lesson_template = edd_get_template_part( 'content', 'lesson', false );

		if ( $lesson_template ) {
			$template = $lesson_template;
		}

	}

	return apply_filters( 'edd_ecourse_template_include', $template, $post );

}

add_action( 'template_include', 'edd_ecourse_template_include' );