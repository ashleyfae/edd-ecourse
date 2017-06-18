<?php
/**
 * Register Post Type and Taxonomies
 *
 * @package   EDD\E-Course\PostTypes
 * @copyright Copyright (c) 2017, Ashley Gibson
 * @license   GPL2+
 * @since     1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Course & Lesson Post Types
 *
 * @todo  Custom capabilities at some point.
 *
 * @since 1.0
 * @return void
 */
function edd_ecourse_post_type() {

	/* Ecourse */

	$course_labels = array(
		'name'               => _x( 'E-Courses', 'post type general name', 'edd-ecourse' ),
		'singular_name'      => _x( 'E-Course', 'post type singular name', 'edd-ecourse' ),
		'add_new'            => __( 'Add New', 'edd-ecourse' ),
		'add_new_item'       => __( 'Add New E-Course', 'edd-ecourse' ),
		'edit_item'          => __( 'Edit E-Course', 'edd-ecourse' ),
		'new_item'           => __( 'New E-Course', 'edd-ecourse' ),
		'all_items'          => __( 'All E-Courses', 'edd-ecourse' ),
		'view_item'          => __( 'View E-Course', 'edd-ecourse' ),
		'search_items'       => __( 'Search E-Courses', 'edd-ecourse' ),
		'not_found'          => __( 'No e-courses found', 'edd-ecourse' ),
		'not_found_in_trash' => __( 'No e-courses found in Trash', 'edd-ecourse' ),
		'parent_item_colon'  => '',
		'menu_name'          => __( 'E-Courses', 'edd-ecourse' )
	);

	$course_args = array(
		'labels'              => apply_filters( 'edd_ecourse_cpt_labels', $course_labels ),
		'public'              => true,
		'show_in_menu'        => false,
		'show_in_nav_menu'    => false,
		'show_in_admin_bar'   => true,
		'show_ui'             => false,
		'rewrite'             => array(
			'slug'       => 'course',
			'with_front' => true,
			'pages'      => true,
			'feeds'      => false,
		),
		'capability_type'     => 'post',
		'has_archive'         => false,
		'exclude_from_search' => true,
		'hierarchical'        => false,
		'supports'            => array( 'title' )
	);

	register_post_type( 'ecourse', apply_filters( 'edd_ecourse_cpt_args', $course_args ) );

	/* Lessons */

	$lesson_labels = array(
		'name'               => _x( 'Lessons', 'post type general name', 'edd-ecourse' ),
		'singular_name'      => _x( 'Lesson', 'post type singular name', 'edd-ecourse' ),
		'add_new'            => __( 'Add New', 'edd-ecourse' ),
		'add_new_item'       => __( 'Add New Lesson', 'edd-ecourse' ),
		'edit_item'          => __( 'Edit Lesson', 'edd-ecourse' ),
		'new_item'           => __( 'New Lesson', 'edd-ecourse' ),
		'all_items'          => __( 'All Lessons', 'edd-ecourse' ),
		'view_item'          => __( 'View Lesson', 'edd-ecourse' ),
		'search_items'       => __( 'Search Lessons', 'edd-ecourse' ),
		'not_found'          => __( 'No lessons found', 'edd-ecourse' ),
		'not_found_in_trash' => __( 'No lessons found in Trash', 'edd-ecourse' ),
		'parent_item_colon'  => '',
		'menu_name'          => __( 'Lessons', 'edd-ecourse' )
	);

	$lesson_args = array(
		'labels'              => apply_filters( 'edd_ecourse_lesson_cpt_labels', $lesson_labels ),
		'public'              => true,
		'show_in_menu'        => false,
		'show_in_nav_menu'    => false,
		'show_in_admin_bar'   => true,
		'rewrite'             => array(
			'slug'       => 'lessons',
			'with_front' => true,
			'pages'      => true,
			'feeds'      => false,
		),
		'capability_type'     => 'post',
		'has_archive'         => false,
		'exclude_from_search' => true,
		'hierarchical'        => true,
		'supports'            => array( 'title', 'editor', 'comments' ),
		'taxonomies'          => array( 'course-topics' ),
	);

	register_post_type( 'ecourse_lesson', apply_filters( 'edd_ecourse_lesson_cpt_args', $lesson_args ) );

}

add_action( 'init', 'edd_ecourse_post_type' );