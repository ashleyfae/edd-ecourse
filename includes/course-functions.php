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
 * Get Course by ID
 *
 * @param int $course_id
 *
 * @since 1.0.0
 * @return object|false Course object or false on failure.
 */
function edd_ecourse_get_course( $course_id ) {
	return edd_ecourse_load()->courses->get_course_by( 'id', $course_id );
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
		'number' => - 1
	);

	$args = wp_parse_args( $args, $defaults );

	$courses = edd_ecourse_load()->courses->get_courses( $args );

	if ( ! is_array( $courses ) ) {
		return false;
	}

	return $courses;

}

/**
 * Insert a Course
 *
 * @param array $args Arguments, including `title` (required), `description`, `status`, `type`, `start_date`
 *
 * @since 1.0.0
 * @return int|bool Course ID on success or false on failure.
 */
function edd_ecourse_insert_course( $args = array() ) {
	// Auto create slug.
	if ( ! array_key_exists( 'id', $args ) && ! array_key_exists( 'slug', $args ) ) {
		$slug         = sanitize_title( $args['title'] );
		$args['slug'] = edd_ecourse_unique_course_slug( $slug );
	}

	$course_id = edd_ecourse_load()->courses->add( $args );

	return $course_id;
}

/**
 * Create a Unique Course Slug
 *
 * Checks to see if the given slug already exists. If so, numbers are appended
 * until the slug becomes available.
 *
 * @see   wp_unique_post_slug() - Based on this.
 *
 * @param string $slug Desired slug.
 *
 * @since 1.0.0
 * @return string Unique slug.
 */
function edd_ecourse_unique_course_slug( $slug ) {
	// Check if this slug already exists.
	$courses = edd_ecourse_load()->courses->get_courses( array( 'slug' => $slug ) );

	$new_slug = $slug;

	if ( $courses ) {
		$suffix = 2;

		do {
			$alt_slug = _truncate_post_slug( $slug, 200 - ( strlen( $suffix ) + 1 ) ) . "-$suffix";
			$courses  = edd_ecourse_load()->courses->get_courses( array( 'slug' => $alt_slug ) );
			$suffix ++;
		} while ( $courses );

		$new_slug = $alt_slug;
	}

	return apply_filters( 'edd_ecourse_unique_course_slug', $new_slug, $slug );
}

/**
 * Get Course URL
 *
 * Returns the public-facing URL to the course archive page. This is where
 * all the modules and lessons are listed for a given course.
 *
 * @param object|int|string $course Course object, ID, or slug.
 *
 * @since 1.0.0
 * @return string|false URL or false if there was an error.
 */
function edd_ecourse_get_course_url( $course ) {
	if ( is_object( $course ) ) {
		$slug = $course->slug;
	} elseif ( is_numeric( $course ) ) {
		$course_obj = edd_ecourse_get_course( $course );
		$slug       = is_object( $course_obj ) ? $course_obj->slug : false;
	} else {
		$slug = $course;
	}

	if ( $slug ) {
		$url = home_url( '/' . edd_ecourse_get_endpoint() . '/' . urlencode( $slug ) );
	} else {
		$url = false;
	}

	return apply_filters( 'edd_ecourse_get_course_url', $url, $slug, $course );
}

/**
 * Get Course Modules
 *
 * @param int $course_id ID of the course.
 *
 * @since 1.0.0
 * @return array|false Array of module objects or false on failure.
 */
function edd_ecourse_get_course_modules( $course_id, $args = array() ) {

	$defaults = array(
		'course' => $course_id,
		'number' => - 1
	);

	$args = wp_parse_args( $args, $defaults );

	$modules = edd_ecourse_load()->modules->get_modules( $args );

	if ( ! is_array( $modules ) || ! count( $modules ) ) {
		$modules = false;
	}

	return apply_filters( 'edd_ecourse_get_course_modules', $modules, $course_id, $args );

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
 * @return int|false The number of courses deleted, or false on error.
 */
function edd_ecourse_delete( $course_id ) {
	return edd_ecourse_load()->courses->delete( $course_id );
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
 * Get Course Permalink
 *
 * Returns the URL to the "public" facing course archive page.
 *
 * @param int|object|string $id_or_slug Course ID, object, or slug.
 *
 * @since 1.0.0
 * @return string|false URL or false on failure.
 */
function edd_ecourse_get_course_permalink( $id_or_slug ) {
	if ( is_numeric( $id_or_slug ) ) {
		$course = edd_ecourse_get_course( $id_or_slug );
		$slug   = is_object( $course ) ? $course->slug : false;
	} elseif ( is_object( $id_or_slug ) ) {
		$slug = $id_or_slug->slug;
	} else {
		$slug = wp_strip_all_tags( $id_or_slug );
	}

	if ( ! $slug ) {
		return false;
	}

	$url = sprintf( home_url( '/%s/%s/' ), edd_ecourse_get_endpoint(), urlencode( $slug ) );

	return apply_filters( 'edd_ecourse_course_permalink', $url, $slug, $id_or_slug );
}

/**
 * Get Current Course
 *
 * @since 1.0.0
 * @return object|false Course object or false on failure.
 */
function edd_ecourse_get_current_course() {
	$course_slug = get_query_var( edd_ecourse_get_endpoint() );
	$course      = edd_ecourse_load()->courses->get_course_by( 'slug', $course_slug );

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
	global $edd_ecourse;

	return is_object( $edd_ecourse ) ? $edd_ecourse->title : false;
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
	global $edd_ecourse;

	return is_object( $edd_ecourse ) ? $edd_ecourse->id : false;
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
	global $edd_ecourse;

	if ( ! is_object( $edd_ecourse ) ) {
		return;
	}

	$slug = $edd_ecourse->slug;

	$url = edd_ecourse_get_course_permalink( $slug );

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