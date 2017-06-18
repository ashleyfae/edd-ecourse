<?php
/**
 * Misc. Functions
 *
 * @package   EDD\E-Course\Misc Functions
 * @copyright Copyright (c) 2017, Ashley Gibson
 * @license   GPL2+
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get Dashboard URL
 *
 * URL to the e-course dashboard page.
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
 * Register Widget Areas
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_register_widget_areas() {

	// Dashboard
	register_sidebar( array(
		'name'          => esc_html__( 'E-Course - Dashboard', 'edd-ecourse' ),
		'id'            => 'ecourse-dashboard',
		'description'   => esc_html__( 'Widgets to appear on the EDD E-Course dashboard page.', 'edd-ecourse' ),
		'before_widget' => '<section id="%1$s" class="box %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>'
	) );

}

add_action( 'widgets_init', 'edd_ecourse_register_widget_areas', 100 );