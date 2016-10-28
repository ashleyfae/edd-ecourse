<?php
/**
 * E-Course Functions
 *
 * @package   EDD\E-Course\Course\Functions
 * @copyright Copyright (c) 2016, Ashley Gibson
 * @license   GPL2+
 * @since     1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Insert Demo Course
 *
 * Adds an e-course and one associated lesson.
 *
 * @since 1.0.0
 * @return bool
 */
function edd_ecourse_insert_demo_course() {

	// Make sure we don't do this twice.
	if ( get_option( 'edd_ecourse_inserted_demo_content' ) ) {
		return false;
	}

	// Insert e-course.
	$course_id = edd_ecourse_load()->courses->add( array(
		'title' => esc_html__( 'My First Course', 'edd-ecourse' )
	) );

	if ( ! $course_id ) {
		return false;
	}

	$post_data = array(
		'post_title'   => __( 'Lesson #1', 'edd-ecourse' ),
		'post_content' => __( 'This is your first e-course lesson.', 'edd-ecourse' ),
		'post_status'  => 'publish',
		'post_type'    => 'ecourse_lesson',
		'tax_input'    => array(
			'ecourse' => array( intval( $course_id ) )
		)
	);

	$lesson_id = wp_insert_post( $post_data );

	if ( is_wp_error( $lesson_id ) || ! $lesson_id ) {
		return false;
	}

	return true;

}

/**
 * Get E-Courses
 *
 * @param array $args
 *
 * @since 1.0.0
 * @return array|false Array of course objects or false if none exist.
 */
function edd_ecourse_get_courses( $args = array() ) {

	$defaults = array(
		'post_status' => 'publish',
		'post_type'   => 'ecourse',
		'number'      => - 1
	);

	if ( current_user_can( 'manage_options' ) ) {
		$defaults['post_status'] = 'any';
	}

	$args = wp_parse_args( $args, $defaults );

	$courses = get_posts( $args );

	if ( ! is_array( $courses ) ) {
		return false;
	}

	return $courses;

}

/**
 * Insert a Course
 *
 * @param string $title Course title.
 * @param array  $args  Arguments to override the defaults.
 *
 * @since 1.0.0
 * @return int|bool Course ID on success or false on failure.
 */
function edd_ecourse_insert_course( $title, $args = array() ) {
	$defaults = array(
		'post_title'  => sanitize_text_field( wp_strip_all_tags( $title ) ),
		'post_name'   => sanitize_title( $title ),
		'post_type'   => 'ecourse',
		'post_status' => 'draft',
		'ping_status' => 'closed'
	);

	$args = wp_parse_args( $args, $defaults );

	$course_id = wp_insert_post( $args );

	return $course_id;
}

/**
 * Get Course Modules
 *
 * @param int $course_id ID of the course.
 *
 * @since 1.0.0
 * @return array|false Array of module objects or false on failure.
 */
function edd_ecourse_get_course_modules( $course_id ) {

	$modules = get_post_meta( $course_id, 'modules', true );

	if ( ! is_array( $modules ) || ! count( $modules ) ) {
		$modules = false;
	}

	return apply_filters( 'edd_ecourse_get_course_modules', $modules, $course_id );

}

/**
 * Get E-Course Lessons
 *
 * @param int   $course_id  ID of the course.
 * @param array $query_args WP_Query arguments to override the defaults.
 *
 * @since 1.0.0
 * @return array
 */
function edd_ecourse_get_course_lessons( $course_id, $query_args = array() ) {

	$default_args = array(
		'post_type'      => 'ecourse_lesson',
		'posts_per_page' => 500,
		'meta_query'     => array(
			array(
				'key'   => 'course',
				'value' => absint( $course_id ),
				'type'  => 'NUMERIC'
			)
		)
	);

	if ( current_user_can( 'manage_options' ) ) {
		$default_args['post_status'] = 'any';
	}

	$query_args = wp_parse_args( $query_args, $default_args );

	$lessons = get_posts( $query_args );

	return $lessons;

}

/**
 * Get Number of Lessons in an E-Course
 *
 * @param int   $course_id  ID of the course.
 * @param array $query_args WP_Query arguments to override the defaults.
 *
 * @uses  edd_ecourse_get_course_lessons()
 *
 * @since 1.0.0
 * @return int
 */
function edd_ecourse_get_number_course_lessons( $course_id, $query_args = array() ) {
	$default_args = array(
		'fields' => 'ids'
	);

	$query_args = wp_parse_args( $query_args, $default_args );

	$lessons        = edd_ecourse_get_course_lessons( $course_id, $query_args );
	$number_lessons = is_array( $lessons ) ? count( $lessons ) : 0;

	return apply_filters( 'edd_ecourse_number_course_lessons', $number_lessons, $lessons, $course_id, $query_args );
}

/**
 * Delete E-Course
 *
 * @param int $course_id ID of the course to delete.
 *
 * @since 1.0.0
 * @return WP_Post|false Post object of the deleted course or false on failure.
 */
function edd_ecourse_delete( $course_id ) {
	return wp_delete_post( $course_id, true );
}

/**
 * Get Course Download
 *
 * Returns the EDD product associated with an e-course.
 *
 * @param int    $course_id ID of the course to get the downlaod for.
 * @param string $format    Format for the return value: `object` or `id`
 *
 * @since 1.0.0
 * @return int|WP_Post
 */
function edd_ecourse_get_course_download( $course_id, $format = 'object' ) {

	$args = array(
		'post_type'      => 'download',
		'post_status'    => 'any',
		'posts_per_page' => 1,
		'meta_query'     => array(
			array(
				'key'   => 'ecourse',
				'value' => absint( $course_id ),
				'type'  => 'NUMERIC'
			)
		)
	);

	if ( 'object' != $format ) {
		$args['fields'] = 'ids';
	}

	$downloads = get_posts( $args );

	if ( is_array( $downloads ) && array_key_exists( 0, $downloads ) ) {
		$download = $downloads[0];
	} else {
		$download = false;
	}

	return apply_filters( 'edd_ecourse_get_course_download', $download, $course_id, $format );

}

/**
 * Get Current Course
 *
 * @since 1.0.0
 * @return object|false Course object or false on failure.
 */
function edd_ecourse_get_current_course() {
	global $post;

	if ( is_object( $post ) && 'ecourse' == $post->post_type ) {
		$course = $post;
	} else {
		$course = false;
	}

	return $course;
}

/**
 * Get Current E-Course Title
 *
 * @global object $edd_ecourse
 *
 * @since 1.0.0
 * @return string|false
 */
function edd_ecourse_get_title() {
	$course = edd_ecourse_get_current_course();

	return is_object( $course ) ? $course->post_title : false;
}

/**
 * Display Current E-Course Title
 *
 * @uses  edd_ecourse_get_title()
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_title() {
	echo edd_ecourse_get_title();
}

/**
 * Get Current E-Course ID
 *
 * @since 1.0.0
 * @return int|false
 */
function edd_ecourse_get_id() {
	$course = edd_ecourse_get_current_course();

	return is_object( $course ) ? $course->ID : false;
}

/**
 * Display Current E-Course Permalink
 *
 * @param bool $escape Whether or not to escape the URL.
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_permalink( $escape = true ) {
	$course = edd_ecourse_get_current_course();

	if ( ! is_object( $course ) ) {
		return;
	}

	$url = get_permalink( $course );

	if ( $escape ) {
		echo esc_url( $url );
	} else {
		echo $url;
	}
}

/**
 * Display Current E-Course ID
 *
 * @uses  edd_ecourse_get_id()
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_id() {
	echo edd_ecourse_get_id();
}

/**
 * Get Modules
 *
 * Returns an array of modules that are in the current course.
 *
 * @since 1.0.0
 * @return array|false
 */
function edd_ecourse_get_modules() {
	$course_id = edd_ecourse_get_id();

	if ( ! $course_id ) {
		return false;
	}

	$modules = edd_ecourse_get_course_modules( $course_id );

	return is_array( $modules ) ? $modules : array();
}

/**
 * Get Edit Course URL
 *
 * Returns the URL to the "Edit Course" page.
 *
 * @since 1.0.0
 * @return string
 */
function edd_ecourse_get_manage_course_url( $course_id = 0 ) {
	$url = add_query_arg( array(
		'page'   => 'ecourses',
		'view'   => 'edit',
		'course' => absint( $course_id )
	), admin_url( 'admin.php' ) );

	return apply_filters( 'edd_ecourse_get_manage_course_url', $url );
}

/**
 * Get Add Course URL
 *
 * Returns the URL to the "Add Course" page.
 *
 * @param int $course_id ID of the course to add the lesson to.
 * @param int $module_id ID of the module to add the lesson to.
 *
 * @since 1.0.0
 * @return string
 */
function edd_ecourse_get_add_lesson_url( $course_id = 0, $module_id = 0 ) {
	$args = array(
		'post_type' => 'ecourse_lesson',
		'course'    => absint( $course_id ),
		'module'    => absint( $module_id )
	);

	$url = add_query_arg( $args, admin_url( 'post-new.php' ) );

	return apply_filters( 'edd_ecourse_get_add_lesson_url', $url );
}

/**
 * Admin Bar Node
 *
 * Adds a new node to the admin bar to "Manage Course". This only appears
 * on course archive pages and single lesson pages.
 *
 * @param WP_Admin_Bar $wp_admin_bar
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_admin_bar_node( $wp_admin_bar ) {
	if ( edd_ecourse_is_course_page() ) {
		$course_id = edd_ecourse_get_id();

		if ( $course_id ) {
			$args = array(
				'id'    => 'ecourse_edit',
				'title' => __( 'Manage Course', 'edd-ecourse' ),
				'href'  => edd_ecourse_get_manage_course_url( $course_id )
			);

			$wp_admin_bar->add_node( $args );
		}
	}
}

add_action( 'admin_bar_menu', 'edd_ecourse_admin_bar_node', 999 );