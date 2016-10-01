<?php
/**
 * Register Post Type and Taxonomies
 *
 * @package EDD\E-Course\PostTypes
 * @since   1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Lesson Post Type
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_post_type() {

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
		'labels'              => apply_filters( 'edd_ecourse_lesson_labels', $lesson_labels ),
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

	register_post_type( 'ecourse_lesson', apply_filters( 'edd_ecourse_lesson_args', $lesson_args ) );

}

add_action( 'init', 'edd_ecourse_post_type' );

/**
 * Register E-Course Taxonomy
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_register_taxonomy() {

	$labels = array(
		'name'                       => _x( 'Courses', 'Taxonomy General Name', 'edd-ecourse' ),
		'singular_name'              => _x( 'Course', 'Taxonomy Singular Name', 'edd-ecourse' ),
		'menu_name'                  => __( 'Manage Courses', 'edd-ecourse' ),
		'all_items'                  => __( 'All Courses', 'edd-ecourse' ),
		'parent_item'                => __( 'Parent Course', 'edd-ecourse' ),
		'parent_item_colon'          => __( 'Parent Course:', 'edd-ecourse' ),
		'new_item_name'              => __( 'New Course Name', 'edd-ecourse' ),
		'add_new_item'               => __( 'Add New Course', 'edd-ecourse' ),
		'edit_item'                  => __( 'Edit Course', 'edd-ecourse' ),
		'update_item'                => __( 'Update Course', 'edd-ecourse' ),
		'separate_items_with_commas' => __( 'Separate courses with commas', 'edd-ecourse' ),
		'search_items'               => __( 'Search Courses', 'edd-ecourse' ),
		'add_or_remove_items'        => __( 'Add or remove courses', 'edd-ecourse' ),
		'choose_from_most_used'      => __( 'Choose from the most used courses', 'edd-ecourse' ),
		'not_found'                  => __( 'Not Found', 'edd-ecourse' ),
	);

	$args = array(
		'labels'            => $labels,
		'hierarchical'      => true,
		'public'            => true,
		'show_ui'           => true,
		'show_admin_column' => true,
		'show_in_nav_menus' => true,
		'show_tagcloud'     => false,
		'rewrite'           => array(
			'slug'         => 'courses',
			'with_front'   => true,
			'hierarchical' => false,
		),
		'has_archive'       => true,
	);

	register_taxonomy( 'ecourse', array( 'ecourse_lesson' ), apply_filters( 'edd_ecourse_taxonomy_args', $args ) );

}

add_action( 'init', 'edd_ecourse_register_taxonomy' );