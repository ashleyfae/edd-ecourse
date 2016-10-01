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
		 * @since 1.0.0
		 */
		private static $instance;


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
			require_once EDD_ECOURSE_DIR . 'includes/scripts.php';
			require_once EDD_ECOURSE_DIR . 'includes/functions.php';
			require_once EDD_ECOURSE_DIR . 'includes/course-functions.php';
			require_once EDD_ECOURSE_DIR . 'includes/post-types.php';
			require_once EDD_ECOURSE_DIR . 'includes/shortcodes.php';

			if ( is_admin() ) {
				require_once EDD_ECOURSE_DIR . 'includes/admin/admin-pages.php';
				require_once EDD_ECOURSE_DIR . 'includes/admin/courses/course-functions.php';
				require_once EDD_ECOURSE_DIR . 'includes/admin/courses/courses.php';
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

	// Register post type.
	edd_ecourse_post_type();

	// Register taxonomy.
	edd_ecourse_register_taxonomy();

	// Insert demo content.
	edd_ecourse_insert_demo_course();

	// Flush rewrite rules.
	flush_rewrite_rules( false );

}

register_activation_hook( __FILE__, 'edd_ecourse_activation' );
