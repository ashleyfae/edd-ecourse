<?php
/**
 * Plugin Name:     Easy Digital Downloads - E-Course
 * Plugin URI:      @todo
 * Description:     @todo
 * Version:         0.1.0
 * Author:          Ashley Gibson
 * Author URI:      https://www.nosegraze.com
 * Text Domain:     edd-ecourse
 *
 * @package         EDD\E-Course
 * @author          Ashley Gibson
 * @copyright       Copyright (c) Ashley Gibson
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'EDD_eCourse' ) ) {

	/**
	 * Main EDD_eCourse class
	 *
	 * @since 1.0.0
	 */
	class EDD_eCourse {

		/**
		 * @var EDD_eCourse $instance The one true EDD_eCourse
		 * @access private
		 * @since  1.0.0
		 */
		private static $instance;

		/**
		 * E-Course DB Object
		 *
		 * @var EDD_eCourse_DB
		 * @access public
		 * @since  1.0.0
		 */
		public $courses;

		/**
		 * Modules DB Object
		 *
		 * @var EDD_eCourse_Modules_DB
		 * @access public
		 * @since  1.0.0
		 */
		public $modules;


		/**
		 * Get active instance
		 *
		 * @access public
		 * @since  1.0.0
		 * @return EDD_eCourse self::$instance The one true EDD_eCourse
		 */
		public static function instance() {
			if ( ! self::$instance ) {
				self::$instance = new EDD_eCourse();
				self::$instance->setup_constants();
				self::$instance->includes();
				self::$instance->load_textdomain();
				self::$instance->hooks();

				self::$instance->courses = new EDD_eCourse_DB;
				self::$instance->modules = new EDD_eCourse_Modules_DB;
			}

			return self::$instance;
		}


		/**
		 * Setup plugin constants
		 *
		 * @access private
		 * @since  1.0.0
		 * @return void
		 */
		private function setup_constants() {
			// Plugin version
			define( 'EDD_ECOURSE_VER', '1.0.0' );

			// Plugin path
			define( 'EDD_ECOURSE_DIR', plugin_dir_path( __FILE__ ) );

			// Plugin URL
			define( 'EDD_ECOURSE_URL', plugin_dir_url( __FILE__ ) );
		}


		/**
		 * Include necessary files
		 *
		 * @access private
		 * @since  1.0.0
		 * @return void
		 */
		private function includes() {
			// Include scripts
			require_once EDD_ECOURSE_DIR . 'includes/class-ecourse-db.php';
			require_once EDD_ECOURSE_DIR . 'includes/class-modules-db.php';
			require_once EDD_ECOURSE_DIR . 'includes/edd-actions.php';
			require_once EDD_ECOURSE_DIR . 'includes/scripts.php';
			require_once EDD_ECOURSE_DIR . 'includes/functions.php';
			require_once EDD_ECOURSE_DIR . 'includes/course-functions.php';
			require_once EDD_ECOURSE_DIR . 'includes/lesson-functions.php';
			require_once EDD_ECOURSE_DIR . 'includes/module-functions.php';
			require_once EDD_ECOURSE_DIR . 'includes/post-types.php';
			require_once EDD_ECOURSE_DIR . 'includes/rewrite-functions.php';
			require_once EDD_ECOURSE_DIR . 'includes/shortcodes.php';
			require_once EDD_ECOURSE_DIR . 'includes/template-functions.php';
			require_once EDD_ECOURSE_DIR . 'includes/user-functions.php';

			if ( is_admin() ) {
				require_once EDD_ECOURSE_DIR . 'includes/admin/admin-pages.php';
				require_once EDD_ECOURSE_DIR . 'includes/admin/courses/actions.php';
				require_once EDD_ECOURSE_DIR . 'includes/admin/courses/course-functions.php';
				require_once EDD_ECOURSE_DIR . 'includes/admin/courses/courses.php';
				require_once EDD_ECOURSE_DIR . 'includes/admin/downloads/meta-boxes.php';
				require_once EDD_ECOURSE_DIR . 'includes/admin/lessons/actions.php';
				require_once EDD_ECOURSE_DIR . 'includes/admin/lessons/meta-boxes.php';
				require_once EDD_ECOURSE_DIR . 'includes/admin/users/user-profiles.php';
			}
		}


		/**
		 * Run action and filter hooks
		 *
		 * @access      private
		 * @since       1.0.0
		 * @return void
		 *
		 * @todo        Probably remove.
		 */
		private function hooks() {
			// Register settings
			add_filter( 'edd_settings_extensions', array( $this, 'settings' ), 1 );

			// Handle licensing
			// @todo        Replace the E-Course and Your Name with your data
			if ( class_exists( 'EDD_License' ) ) {
				$license = new EDD_License( __FILE__, 'E-Course', EDD_ECOURSE_VER, 'Your Name' );
			}
		}


		/**
		 * Internationalization
		 *
		 * @access      public
		 * @since       1.0.0
		 * @return      void
		 */
		public function load_textdomain() {
			// Set filter for language directory
			$lang_dir = EDD_ECOURSE_DIR . '/languages/';
			$lang_dir = apply_filters( 'edd_ecourse_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter
			$locale = apply_filters( 'plugin_locale', get_locale(), 'edd-ecourse' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'edd-ecourse', $locale );

			// Setup paths to current locale file
			$mofile_local  = $lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/edd-ecourse/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/edd-ecourse/ folder
				load_textdomain( 'edd-ecourse', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/edd-ecourse/languages/ folder
				load_textdomain( 'edd-ecourse', $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'edd-ecourse', false, $lang_dir );
			}
		}


		/**
		 * Add settings
		 *
		 * @param array $settings The existing EDD settings array
		 *
		 * @access public
		 * @since  1.0.0
		 * @return array The modified EDD settings array
		 */
		public function settings( $settings ) {
			$new_settings = array(
				array(
					'id'   => 'edd_ecourse_settings',
					'name' => '<strong>' . __( 'E-Course Settings', 'edd-ecourse' ) . '</strong>',
					'desc' => __( 'Configure E-Course Settings', 'edd-ecourse' ),
					'type' => 'header',
				),
				array(
					'id'          => 'ecourse_dashboard_page',
					'name'        => __( 'Dashboard Page', 'edd-ecourse' ),
					'desc'        => __( 'This is the course dashboard page that shows an overview of the student\'s courses and progress.', 'edd-ecourse' ),
					'type'        => 'select',
					'options'     => edd_get_pages(),
					'chosen'      => true,
					'placeholder' => __( 'Select a page', 'edd-ecourse' )
				),
				array(
					'id'   => 'ecourse_keep_out_of_admin',
					'name' => __( 'Keep out of wp-admin', 'edd-ecourse' ),
					'desc' => __( 'By default, all registered users can access portions of the WordPress admin area, like to edit their own profile. Check this to forcibly keep out e-course students.', 'edd-ecourse' ),
					'type' => 'checkbox',
					'std'  => true
				)
			);

			return array_merge( $settings, $new_settings );
		}
	}
} // End if class_exists check


/**
 * The main function responsible for returning the one true EDD_eCourse
 * instance to functions everywhere
 *
 * @since 1.0.0
 * @return EDD_eCourse|void The one true EDD_eCourse
 */
function edd_ecourse_load() {
	if ( ! class_exists( 'Easy_Digital_Downloads' ) ) {
		if ( ! class_exists( 'EDD_Extension_Activation' ) ) {
			require_once 'includes/class.extension-activation.php';
		}

		$activation = new EDD_Extension_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
		$activation->run();
	} else {
		return EDD_eCourse::instance();
	}
}

add_action( 'plugins_loaded', 'edd_ecourse_load' );


/**
 * Activation Functions
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_activation() {

	if ( ! function_exists( 'edd_ecourse_post_type' ) ) {
		include_once 'includes/post-types.php';
	}

	if ( ! function_exists( 'edd_ecourse_insert_demo_course' ) ) {
		include_once 'includes/course-functions.php';
	}

	if ( ! function_exists( 'edd_ecourse_add_endpoint' ) ) {
		include_once 'includes/rewrite-functions.php';
	}

	// Register post type.
	edd_ecourse_post_type();

	// Insert demo content.
	//edd_ecourse_insert_demo_course();

	// Add course endpoint.
	edd_ecourse_add_endpoint();

	// Only add DB tables if EDD_DB class exists.
	if ( class_exists( 'EDD_DB' ) ) {
		if ( ! class_exists( 'EDD_eCourse_DB' ) ) {
			require_once 'includes/class-ecourse-db.php';
		}

		$db = new EDD_eCourse_DB();
		@$db->create_table();

		if ( ! class_exists( 'EDD_eCourse_Modules_DB' ) ) {
			require_once 'includes/class-modules-db.php';
		}

		$db = new EDD_eCourse_Modules_DB();
		@$db->create_table();
	}

	// Flush rewrite rules.
	flush_rewrite_rules( false );

	// Populate some default values.
	$existing_settings = get_option( 'edd_settings', array() );
	$options           = array();

	if ( ! array_key_exists( 'ecourse_dashboard_page', $existing_settings ) ) {
		$dashboard = wp_insert_post( array(
			'post_title'     => __( 'Dashboard', 'edd-ecourse' ),
			'post_content'   => '[course-dashboard]',
			'post_status'    => 'publish',
			'post_author'    => 1,
			'post_type'      => 'page',
			'comment_status' => 'closed'
		) );

		// Store the page ID.
		$options['ecourse_dashboard_page'] = $dashboard;
	}

	if ( ! array_key_exists( 'ecourse_keep_out_of_admin', $existing_settings ) ) {
		$options['ecourse_keep_out_of_admin'] = 1;
	}

	$merged_options = array_merge( $existing_settings, $options );
	update_option( 'edd_settings', $merged_options );

}

register_activation_hook( __FILE__, 'edd_ecourse_activation' );

/**
 * On EDD Activation
 *
 * This function exists because some of the installation functions use
 * EDD functions/classes. This is to catch an edge case if this plugin is
 * activated before EDD is. Then we need to run the activation again once
 * EDD is finally activated.
 *
 * @todo  Actually test this edge case.
 *
 * @uses  edd_ecourse_activation()
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_on_edd_activation() {
	edd_ecourse_activation();
}

add_action( 'edd_after_install', 'edd_ecourse_on_edd_activation' );