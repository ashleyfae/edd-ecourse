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

			/**
			 * @todo        The following files are not included in the boilerplate, but
			 *              the referenced locations are listed for the purpose of ensuring
			 *              path standardization in EDD extensions. Uncomment any that are
			 *              relevant to your extension, and remove the rest.
			 */
			// require_once EDD_ECOURSE_DIR . 'includes/shortcodes.php';
			// require_once EDD_ECOURSE_DIR . 'includes/widgets.php';
		}


		/**
		 * Run action and filter hooks
		 *
		 * @access      private
		 * @since       1.0.0
		 * @return void
		 *
		 * @todo        The hooks listed in this section are a guideline, and
		 *              may or may not be relevant to your particular extension.
		 *              Please remove any unnecessary lines, and refer to the
		 *              WordPress codex and EDD documentation for additional
		 *              information on the included hooks.
		 *
		 *              This method should be used to add any filters or actions
		 *              that are necessary to the core of your extension only.
		 *              Hooks that are relevant to meta boxes, widgets and
		 *              the like can be placed in their respective files.
		 *
		 *              IMPORTANT! If you are releasing your extension as a
		 *              commercial extension in the EDD store, DO NOT remove
		 *              the license check!
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
 * @return EDD_eCourse The one true EDD_eCourse
 */
function EDD_eCourse_load() {
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

add_action( 'plugins_loaded', 'EDD_eCourse_load' );


/**
 * Activation Functions
 *
 * @since 1.0.0
 * @return void
 */
function edd_ecourse_activation() {
	/* Activation functions here */
}

register_activation_hook( __FILE__, 'edd_ecourse_activation' );
