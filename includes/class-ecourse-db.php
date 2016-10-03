<?php

/**
 * E-Course DB Class
 *
 * For interacting with the e-course database table.
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
 * Class EDD_eCourse_DB
 *
 * @since 1.0.0
 */
class EDD_eCourse_DB extends EDD_DB {

	/**
	 * EDD_eCourse_DB constructor.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function __construct() {

		global $wpdb;

		$this->table_name  = $wpdb->prefix . 'edd_ecourses';
		$this->primary_key = 'id';
		$this->version     = '1.0';

	}

	/**
	 * Get Columns and Formats
	 *
	 * @todo   check this
	 *
	 * @access public
	 * @since  1.0.0
	 * @return array
	 */
	public function get_columns() {
		return array(
			'id'          => '%d',
			'title'       => '%s',
			'description' => '%s',
			'status'      => '%s',
			'type'        => '%s',
			'start_date'  => '%s'
		);
	}

	/**
	 * Get Default Column Values
	 *
	 * @access public
	 * @since  1.0.0
	 * @return array
	 */
	public function get_column_defaults() {
		return array(
			'title'       => '',
			'description' => '',
			'status'      => '', // @todo `active` ?
			'type'        => 'normal', // @todo hmm
			'start_date'  => date( 'Y-m-d H:i:s' )
		);
	}

	/**
	 * Add E-Course
	 *
	 * @param array $data
	 *
	 * @access public
	 * @since  1.0.0
	 * @return int|bool Row ID or false on failure.
	 */
	public function add( $data = array() ) {

		$defaults = array();

		$args = wp_parse_args( $data, $defaults );

		if ( empty( $args['title'] ) ) {
			return false;
		}

		$course = $this->get_course_by( 'id', $args['id'] );

		if ( $course ) {
			$this->update( $course->id, $args );

			return $course->id;
		}

		return $this->insert( $data, 'ecourse' );

	}

	/**
	 * Delete Course by ID
	 *
	 * NOTE: This should not be called directly. Use edd_ecourse_delete() instead.
	 *
	 * @param bool $id ID of the course to delete.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return bool|false|int
	 */
	public function delete( $id = false ) {

		if ( empty( $id ) ) {
			return false;
		}

		$course = $this->get_course_by( 'id', $id );

		if ( $course->id > 0 ) {
			global $wpdb;

			return $wpdb->delete( $this->table_name, array( 'id' => $course->id ), array( '%d' ) );
		}

		return false;

	}

	/**
	 * Course Exists
	 *
	 * Check if a course exists.
	 *
	 * @param string $value Field value.
	 * @param string $field Name of the field to check.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return bool
	 */
	public function exists( $value = '', $field = 'id' ) {

		$columns = $this->get_columns();
		if ( ! array_key_exists( $field, $columns ) ) {
			return false;
		}

		return (bool) $this->get_column_by( 'id', $field, $value );

	}

	/**
	 * Get Course By
	 *
	 * Retrieve a single course from the database.
	 *
	 * @param string $field Field to search - `id` or `title`.
	 * @param int    $value The course ID or title to search.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return object|false Course object or false on failure.
	 */
	public function get_course_by( $field = 'id', $value = 0 ) {

		global $wpdb;

		if ( empty( $field ) || empty( $value ) ) {
			return false;
		}

		if ( 'id' == $field ) {

			// Mak esure the value is numeric.
			if ( ! is_numeric( $value ) ) {
				return false;
			}

			$value = intval( $value );

			if ( $value < 1 ) {
				return false;
			}

		} elseif ( 'title' == $field ) {

			$value = trim( $value );

		}

		if ( ! $value ) {
			return false;
		}

		switch ( $field ) {
			case 'id' :
				$db_field = 'id';
				break;
			case 'title' :
				$value    = sanitize_text_field( $value );
				$db_field = 'title';
				break;
			default :
				return false;
		}

		if ( ! $course = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $this->table_name WHERE $db_field = %s LIMIT 1", $value ) ) ) {
			return false;
		}

		return $course;

	}

	/**
	 * Get Courses
	 *
	 * @param array $args Query arguments to override the defaults.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return array|false Array of course objects or false on failure.
	 */
	public function get_courses( $args = array() ) {

		global $wpdb;

		$defaults = array(
			'number'  => 20,
			'offset'  => 0,
			'orderby' => 'title',
			'order'   => 'ASC'
		);

		$args = wp_parse_args( $args, $defaults );

		if ( $args['number'] < 1 ) {
			$args['number'] = 999999999999;
		}

		$join  = '';
		$where = ' WHERE 1=1 ';

		// Specific courses
		if ( array_key_exists( 'id', $args ) && ! empty( $args['id'] ) ) {

			if ( is_array( $args['id'] ) ) {
				$ids = implode( ',', array_map( 'intval', $args['id'] ) );
			} else {
				$ids = intval( $args['id'] );
			}

			$where .= " AND `id` IN( {$ids} ) ";

		}

		// Statuses
		if ( array_key_exists( 'status', $args ) && ! empty( $args['status'] ) ) {

			if ( is_array( $args['status'] ) ) {
				$statuses = implode( ',', array_map( 'sanitize_text_field', $args['status'] ) );
			} else {
				$statuses = sanitize_text_field( $args['status'] );
			}

			$where .= " AND `status` IN( {$statuses} ) ";

		}

		// Types
		if ( array_key_exists( 'type', $args ) && ! empty( $args['type'] ) ) {

			if ( is_array( $args['type'] ) ) {
				$types = implode( ',', array_map( 'sanitize_text_field', $args['type'] ) );
			} else {
				$types = sanitize_text_field( $args['type'] );
			}

			$where .= " AND `type` IN( {$types} ) ";

		}

		// Courses by name.
		if ( array_key_exists( 'title', $args ) && ! empty( $args['title'] ) ) {
			$where .= $wpdb->prepare( " AND `title` LIKE '%%%%" . '%s' . "%%%%' ", sanitize_text_field( $args['name'] ) );
		}

		// Within a specific start date.
		if ( array_key_exists( 'date', $args ) && ! empty( $args['date'] ) ) {

			if ( is_array( $args['date'] ) ) {

				if ( ! empty( $args['date']['start'] ) ) {

					$start = date( 'Y-m-d 00:00:00', strtotime( $args['date']['start'] ) );
					$where .= " AND `start_date` >= '{$start}'";

				}

				if ( ! empty( $args['date']['end'] ) ) {

					$end = date( 'Y-m-d 23:59:59', strtotime( $args['date']['end'] ) );
					$where .= " AND `start_date` <= '{$end}'";

				}

			} else {

				$year  = date( 'Y', strtotime( $args['date'] ) );
				$month = date( 'm', strtotime( $args['date'] ) );
				$day   = date( 'd', strtotime( $args['date'] ) );

				$where .= " AND $year = YEAR ( start_date ) AND $month = MONTH ( start_date ) AND $day = DAY ( start_date )";

			}

		}

		// Orderby.
		$args['orderby'] = ! array_key_exists( $args['orderby'], $this->get_columns() ) ? 'id' : $args['orderby'];

		$cache_key = md5( 'edd_ecourses_' . serialize( $args ) );

		$courses = wp_cache_get( $cache_key, 'customers' );

		$args['orderby'] = esc_sql( $args['orderby'] );
		$args['order']   = esc_sql( $args['order'] );

		if ( $courses === false ) {
			$query   = $wpdb->prepare( "SELECT * FROM  $this->table_name $join $where GROUP BY $this->primary_key ORDER BY {$args['orderby']} {$args['order']} LIMIT %d,%d;", absint( $args['offset'] ), absint( $args['number'] ) );
			$courses = $wpdb->get_results( $query );
			wp_cache_set( $cache_key, $courses, 'courses', 3600 );
		}

		return $courses;

	}

	/**
	 * Get Number of Courses
	 *
	 * @param array $args
	 *
	 * @access public
	 * @since  1.0.0
	 * @return int
	 */
	public function count( $args = array() ) {
		// @todo
	}

	/**
	 * Create Table
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function create_table() {

		global $wpdb;

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$sql = "CREATE TABLE " . $this->table_name . " (
		id bigint(20) NOT NULL AUTO_INCREMENT,
		name mediumtext NOT NULL,
		description longtext NOT NULL,
		status mediumtext NOT NULL,
		type mediumtext NOT NULL,
		start_date datetime NOT NULL,
		PRIMARY KEY  (id)
		) CHARACTER SET utf8 COLLATE utf8_general_ci;";

		dbDelta( $sql );

		update_option( $this->table_name . '_db_version', $this->version );

	}

}