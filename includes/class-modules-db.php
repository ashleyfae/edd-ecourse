<?php

/**
 * E-Course Modules DB Class
 *
 * For interacting with the modules database table.
 *
 * @package   edd-ecourse
 * @copyright Copyright (c) 2016, Ashley Gibson
 * @license   GPL2+
 * @since     1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class EDD_eCourse_Modules_DB
 *
 * @since 1.0.0
 */
class EDD_eCourse_Modules_DB extends EDD_eCourse_DB {

	/**
	 * EDD_eCourse_Modules_DB constructor.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function __construct() {

		global $wpdb;

		$this->table_name  = $wpdb->prefix . 'edd_ecourse_modules';
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
			'position'    => '%d',
			'course'      => '%d'
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
			'position'    => 0,
			'course'      => 0
		);
	}

	/**
	 * Add Module
	 *
	 * @param array $data
	 *
	 * @access public
	 * @since  1.0.0
	 * @return int|bool Row ID or false on failure.
	 */
	public function add( $data = array() ) {

		$defaults = array(
			'position' => 0,
			'course'   => 0
		);

		$args = wp_parse_args( $data, $defaults );

		if ( empty( $args['title'] ) || empty( $args['course'] ) || $args['course'] < 1 ) {
			return false;
		}

		$module = $this->get_module_by( 'id', $args['id'] );

		if ( $module ) {
			$this->update( $module->id, $args );

			return $module->id;
		}

		return $this->insert( $data, 'ecourse_module' );

	}

	/**
	 * Delete Module by ID
	 *
	 * NOTE: This should not be called directly. Use edd_ecourse_delete_module() instead.
	 * @see    edd_ecourse_delete_module()
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

		$module = $this->get_module_by( 'id', $id );

		if ( $module->id > 0 ) {
			global $wpdb;

			return $wpdb->delete( $this->table_name, array( 'id' => $module->id ), array( '%d' ) );
		}

		return false;

	}

	/**
	 * Delete all modules in a course
	 *
	 * @param int $course_id Course ID
	 *
	 * @access public
	 * @since  1.0
	 * @return int|false Number of rows deleted or false on error
	 */
	public function delete_course_modules( $course_id ) {

		global $wpdb;

		$query  = $wpdb->prepare( "DELETE FROM {$this->table_name} WHERE course = %d", absint( $course_id ) );
		$result = $wpdb->query( $query );

		return $result;

	}

	/**
	 * Module Exists
	 *
	 * Check if a module exists.
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
	 * Get Module By
	 *
	 * Retrieve a single module from the database.
	 *
	 * @param string $field Field to search - `id`, `title`, or `course`.
	 * @param int    $value The module ID or title to search.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return object|false Module object or false on failure.
	 */
	public function get_module_by( $field = 'id', $value = 0 ) {

		global $wpdb;

		if ( empty( $field ) || empty( $value ) ) {
			return false;
		}

		if ( 'id' == $field || 'course' == $field ) {

			// Make sure the value is numeric.
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
			case 'course' :
				$db_field = 'course';
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
	 * Get Modules
	 *
	 * @param array $args Query arguments to override the defaults.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return array|false Array of module objects or false on failure.
	 */
	public function get_modules( $args = array() ) {

		global $wpdb;

		$defaults = array(
			'number'  => 20,
			'offset'  => 0,
			'orderby' => 'position',
			'order'   => 'ASC'
		);

		$args = wp_parse_args( $args, $defaults );

		if ( $args['number'] < 1 ) {
			$args['number'] = 999999999999;
		}

		$join  = '';
		$where = ' WHERE 1=1 ';

		// Specific modules
		if ( array_key_exists( 'id', $args ) && ! empty( $args['id'] ) ) {

			if ( is_array( $args['id'] ) ) {
				$ids = implode( ',', array_map( 'intval', $args['id'] ) );
			} else {
				$ids = intval( $args['id'] );
			}

			$where .= " AND `id` IN( {$ids} ) ";

		}

		// By course.
		if ( array_key_exists( 'course', $args ) && ! empty( $args['course'] ) ) {

			if ( is_array( $args['course'] ) ) {
				$ids = implode( ',', array_map( 'intval', $args['course'] ) );
			} else {
				$ids = intval( $args['course'] );
			}

			$where .= " AND `course` IN( {$ids} ) ";

		}

		// Module by name.
		if ( array_key_exists( 'title', $args ) && ! empty( $args['title'] ) ) {
			$where .= $wpdb->prepare( " AND `title` LIKE '%%%%" . '%s' . "%%%%' ", sanitize_text_field( $args['name'] ) );
		}

		// Orderby.
		$args['orderby'] = ! array_key_exists( $args['orderby'], $this->get_columns() ) ? 'id' : $args['orderby'];

		$cache_key = md5( 'edd_ecourse_modules_' . serialize( $args ) );

		$modules = wp_cache_get( $cache_key, 'course_modules' );

		$args['orderby'] = esc_sql( $args['orderby'] );
		$args['order']   = esc_sql( $args['order'] );

		if ( $modules === false ) {
			$query   = $wpdb->prepare( "SELECT * FROM  $this->table_name $join $where GROUP BY $this->primary_key ORDER BY {$args['orderby']} {$args['order']} LIMIT %d,%d;", absint( $args['offset'] ), absint( $args['number'] ) );
			$modules = $wpdb->get_results( $query );
			wp_cache_set( $cache_key, $modules, 'course_modules', 3600 );
		}

		return $modules;

	}

	/**
	 * Get Number of Modules
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
		id BIGINT(20) NOT NULL AUTO_INCREMENT,
		title MEDIUMTEXT NOT NULL,
		description LONGTEXT NOT NULL,
		position BIGINT(20) NOT NULL,
		course BIGINT(20) NOT NULL,
		PRIMARY KEY  (id),
		KEY course (course)
		) CHARACTER SET utf8 COLLATE utf8_general_ci;";

		dbDelta( $sql );

		update_option( $this->table_name . '_db_version', $this->version );

	}

}