<?php
/**
 * Plugin Name: Opal Estate Pro
 * Plugin URI: https://wpdocs.gitbook.io/opal-estate/
 * Description: Opal Real Estate Plugin is an ideal solution and brilliant choice for you to set up a professional estate website.
 * Version: 1.6.2
 * Author: WPOPAL
 * Author URI: http://www.wpopal.com
 * Requires at least: 4.9
 * Tested up to: 5.3.2
 * Text Domain: opalestate-pro
 * Domain Path: languages/
 *
 * @package  opalestate-pro
 * @category Plugins
 * @author   WPOPAL
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'OpalEstate' ) ) {

	final class OpalEstate {

		/**
		 * @var Opalestate The one true Opalestate
		 * @since 1.0
		 */
		private static $instance;

		/**
		 * Opalestate Roles Object
		 *
		 * @var object
		 * @since 1.0
		 */
		public $roles;

		/**
		 * Opalestate Settings Object
		 *
		 * @var object
		 * @since 1.0
		 */
		public $opalestate_settings;

		/**
		 * Opalestate Session Object
		 *
		 * This holds donation data for user's session
		 *
		 * @var object
		 * @since 1.0
		 */
		public $session;

		/**
		 * Opalestate HTML Element Helper Object
		 *
		 * @var object
		 * @since 1.0
		 */
		public $html;


		/**
		 * Opalestate Emails Object
		 *
		 * @var object
		 * @since 1.0
		 */
		public $emails;

		/**
		 * Opalestate Email Template Tags Object
		 *
		 * @var object
		 * @since 1.0
		 */
		public $email_tags;

		/**
		 * Opalestate Customers DB Object
		 *
		 * @var object
		 * @since 1.0
		 */
		public $customers;

		/**
		 * Opalestate API Object
		 *
		 * @var object
		 * @since 1.1
		 */
		public $api;

		/**
		 *
		 */
		public function __construct() {

		}

		/**
		 * Main Opalestate Instance
		 *
		 * Insures that only one instance of Opalestate exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @return    Opalestate
		 * @uses      Opalestate::setup_constants() Setup the constants needed
		 * @uses      Opalestate::includes() Include the required files
		 * @uses      Opalestate::load_textdomain() load the language files
		 * @see       OpalEstate()
		 * @static
		 * @staticvar array $instance
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Opalestate ) ) {
				self::$instance = new OpalEstate;
				self::$instance->setup_constants();

				register_activation_hook( OPALESTATE_PLUGIN_FILE, [ 'Opalestate_Install', 'install' ] );
				register_deactivation_hook( OPALESTATE_PLUGIN_FILE, [ 'Opalestate_Deactivator', 'deactivate' ] );

				add_action( 'plugins_loaded', [ self::$instance, 'load_textdomain' ] );
				self::$instance->setup();
				self::$instance->roles   = new Opalestate_Roles();
				self::$instance->html    = new Opalestate_HTML_Elements();
				self::$instance->api     = new Opalestate_API();
				self::$instance->session = new Opalestate_Session();

				Opalestate_Install::init();

				/**
				 *
				 */
				add_filter( 'opalestate_google_map_api', [ __CLASS__, 'load_google_map_api' ] );
				add_action( 'cli_init', [ self::$instance, 'init_cli' ] );
			}

			return self::$instance;
		}

		/**
		 * Throw error on object clone
		 *
		 * The whole idea of the singleton design pattern is that there is a single
		 * object, therefore we don't want the object to be cloned.
		 *
		 * @access protected
		 */
		public function __clone() {
			// Cloning instances of the class is forbidden
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'opalestate-pro' ), '1.6.2' );
		}

		/**
		 *
		 */
		public function setup_constants() {
			// Plugin version
			if ( ! defined( 'OPALESTATE_VERSION' ) ) {
				define( 'OPALESTATE_VERSION', '1.6.2' );
			}

			// Plugin Folder Path
			if ( ! defined( 'OPALESTATE_PLUGIN_DIR' ) ) {
				define( 'OPALESTATE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

			// Plugin Folder URL
			if ( ! defined( 'OPALESTATE_PLUGIN_URL' ) ) {
				define( 'OPALESTATE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}

			// Plugin Root File
			if ( ! defined( 'OPALESTATE_PLUGIN_FILE' ) ) {
				define( 'OPALESTATE_PLUGIN_FILE', __FILE__ );
			}

			// Plugin Root File
			if ( ! defined( 'OPALESTATE_THEMER_WIDGET_TEMPLATES' ) ) {
				define( 'OPALESTATE_THEMER_WIDGET_TEMPLATES', get_stylesheet_directory() . '/' );
			}

			if ( ! defined( 'OPALMEMBERSHIP_PACKAGES_PREFIX' ) ) {
				define( 'OPALMEMBERSHIP_PACKAGES_PREFIX', 'opalestate_package_' );
			}

			if ( ! defined( "OPALESTATE_CLUSTER_ICON_URL" ) ) {
				define( 'OPALESTATE_CLUSTER_ICON_URL', apply_filters( 'opalestate_cluster_icon_url', OPALESTATE_PLUGIN_URL . 'assets/cluster-icon.png' ) );
			}

			/// define;
			define( 'OPALESTATE_AGENT_PREFIX', 'opalestate_agt_' );
			define( 'OPALESTATE_PROPERTY_PREFIX', 'opalestate_ppt_' );
			define( 'OPALESTATE_AGENCY_PREFIX', 'opalestate_ofe_' );
		}

		public static function load_google_map_api( $key ) {
			if ( opalestate_options( 'google_map_api_keys' ) ) {
				$key = '//maps.googleapis.com/maps/api/js?sensor=false&amp;libraries=places&amp;key=' . opalestate_options( 'google_map_api_keys' );
			}

			return $key;
		}

		public function init_cli() {
			$this->includes(
				[
					'cli/export.php',
				]
			);
		}

		public function setup() {
			global $opalestate_options;

			/**
			 * Get the CMB2 bootstrap!
			 *
			 * @description: Checks to see if CMB2 plugin is installed first the uses included CMB2; we can still use it even it it's not active. This prevents fatal error conflicts with other themes and users of the CMB2 WP.org plugin
			 *
			 */
			require_once OPALESTATE_PLUGIN_DIR . 'inc/vendors/cmb2-plugins/init.php';
			require_once OPALESTATE_PLUGIN_DIR . 'inc/admin/register-settings.php';
			require_once OPALESTATE_PLUGIN_DIR . 'inc/admin/functions.php';

			if ( is_admin() ) {
				require_once OPALESTATE_PLUGIN_DIR . 'inc/admin/class-admin.php';
			}

			$opalestate_options = opalestate_get_settings();

			$this->includes(
				[
					'class-template-loader.php',
					'query-functions.php',
					'mixes-functions.php',
					'hook-functions.php',
					'class-opalestate-roles.php',
					'classes/class-opalestate-session.php',
					'classes/class-opalestate-abs-query.php',
					'classes/class-opalestate-metabox-user.php',
					'classes/class-opalestate-geolocation.php',
					'classes/class-opalestate-yelp.php',
					'classes/class-opalestate-walkscore.php',
					'classes/class-opalestate-multilingual.php',
					//'classes/metabox/class-metabox-agency.php',
				]
			);

			// rating
			$this->includes(
				[
					'rating/class-opalestate-rating.php',
				]
			);

			// message
			$this->includes(
				[
					'message/class-opalestate-message.php',
					'message/class-opalestate-request-reviewing.php',
					'message/functions.php',
				]
			);

			// agent
			$this->includes(
				[
					'agent/class-opalestate-agent-posttype.php',
					'agent/class-opalestate-agent.php',
					'agent/class-opalestate-agent-query.php',
					'agent/class-opalestate-agent-front.php',
					'agent/class-opalestate-agent-metabox.php',
				]
			);

			// agent
			$this->includes(
				[
					'agency/class-opalestate-agency-posttype.php',
					'agency/class-opalestate-agency.php',
					'agency/class-opalestate-agency-query.php',
					'agency/class-opalestate-agency-front.php',
					'agency/class-opalestate-agency-metabox.php',
				]
			);

			/// property ///
			$this->includes(
				[
					'property/class-metabox-property-admin.php',
					'property/class-opalestate-posttype.php',
					'property/class-opalestate-property-query.php',
					'property/class-opalestate-query.php',
					'property/class-opalestate-favorite.php',
					'property/class-opalestate-property.php',
					'property/class-opalestate-shortcodes.php',
					'property/class-opalestate-search.php',
					'property/class-opalestate-view-stats.php',
					'property/functions.php',
				]
			);

			/// user ///
			$this->includes(
				[
					'user/functions.php',
					'user/class-opalestate-user.php',
					'user/class-opalestate-user-form-handler.php',
					'user/class-opalestate-user-search.php',
					'user/class-user-statistics.php',
				]
			);

			$this->includes(
				[
					'taxonomies/class-taxonomy-categories.php',
					'taxonomies/class-taxomony-amenities.php',
					'taxonomies/class-taxonomy-labels.php',
					'taxonomies/class-taxonomy-status.php',
					'taxonomies/class-taxonomy-types.php',
					'taxonomies/class-taxonomy-locations.php',
					'taxonomies/class-taxonomy-city.php',
					'taxonomies/class-taxonomy-state.php',
				]
			);

			require_once OPALESTATE_PLUGIN_DIR . 'inc/api/class-opalestate-api.php';

			$this->includes(
				[
					'template-functions.php',
				]
			);

			///
			$this->includes(
				[
					'class-opalestate-enqueue.php',
				]
			);

			//// enable or disable submission ////
			if ( opalestate_options( 'enable_submission', 'on' ) == 'on' ) {
				$this->includes(
					[
						'submission/class-metabox-property-submission.php',
						'submission/class-opalestate-submission.php',
					]
				);

			}

			$this->includes(
				[
					'class-no-captcha-recaptcha.php',
					'class-opalestate-email.php',
				]
			);

			require_once OPALESTATE_PLUGIN_DIR . 'inc/class-opalestate-install.php';
			require_once OPALESTATE_PLUGIN_DIR . 'inc/class-opalestate-deactivator.php';

			require_once OPALESTATE_PLUGIN_DIR . 'inc/class-opalestate-html.php';
			require_once OPALESTATE_PLUGIN_DIR . 'inc/function-search-fields.php';

			add_action( 'widgets_init', [ $this, 'widgets_init' ] );

			add_action( 'init', [ $this, 'set_location_actived' ] );

			require_once OPALESTATE_PLUGIN_DIR . 'inc/ajax-functions.php';
			require_once OPALESTATE_PLUGIN_DIR . 'inc/template-hook-functions.php';

			add_action( 'plugins_loaded', [ $this, 'load_exts' ] );
			$this->load_vendors();
		}

		/**
		 * Include list of collection files
		 *
		 * @var array $files
		 */
		public function load_vendors() {
			if ( defined( 'ELEMENTOR_VERSION' ) ) {
				require_once OPALESTATE_PLUGIN_DIR . 'inc/vendors/elementor/class-opalestate-elementor-extended.php';
			}

			require_once OPALESTATE_PLUGIN_DIR . 'inc/vendors/social-login/class-opalestate-social-login.php';
		}

		/**
		 * Include list of collection files
		 *
		 * @var array $files
		 */
		public function includes( $files ) {
			foreach ( $files as $file ) {
				$this->_include( $file );
			}
		}

		/**
		 * include single file if found
		 *
		 * @var string $file
		 */
		private function _include( $file = '' ) {
			$file = OPALESTATE_PLUGIN_DIR . 'inc/' . $file;
			//if ( file_exists( $file ) ) {
			include_once $file;
			//}
		}

		/**
		 * Load extensions.
		 */
		public function load_exts() {
			if ( class_exists( 'OpalMembership' ) ) {
				require_once OPALESTATE_PLUGIN_DIR . 'inc/vendors/opalmembership/membership.php';
			}
		}

		/**
		 * Set location actived.
		 */
		public static function set_location_actived() {
			if ( isset( $_GET['set_location'] ) && ! empty( $_GET['set_location'] ) ) {
				$_SESSION['set_location'] = trim( $_GET['set_location'] );
				wp_redirect( home_url( '/' ) );
				exit;
			}
			if ( isset( $_GET['clear_location'] ) && ! empty( $_GET['clear_location'] ) ) {
				$_SESSION['set_location'] = null;
				wp_redirect( home_url( '/' ) );
				exit;
			}
			if ( isset( $_SESSION['set_location'] ) && ! empty( $_SESSION['set_location'] ) ) {
				Opalestate_Query::$LOCATION = $_SESSION['set_location'];
			}

			if ( get_current_user_id() > 0 ) {
				do_action( "opalestate_user_init" );
			}
		}

		/**
		 * Load plugin textdomain.
		 */
		public function load_textdomain() {
			// Set filter for Opalestate's languages directory
			$lang_dir = dirname( plugin_basename( OPALESTATE_PLUGIN_FILE ) ) . '/languages/';
			$lang_dir = apply_filters( 'opalestate_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter
			$locale = apply_filters( 'plugin_locale', get_locale(), 'opalestate-pro' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'opalestate-pro', $locale );

			// Setup paths to current locale file
			$mofile_local  = $lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/opalestate/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/opalestate folder
				load_textdomain( 'opalestate-pro', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/opalestate/languages/ folder
				load_textdomain( 'opalestate-pro', $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'opalestate-pro', false, $lang_dir );
			}
		}

		public function widgets_init() {
			opalestate_includes( OPALESTATE_PLUGIN_DIR . 'inc/widgets/*.php' );
		}
	}
}

if ( ! function_exists( 'OpalEstate' ) ) {
	function OpalEstate() {
		return OpalEstate::get_instance();
	}

	// Constructor.
	OpalEstate();
}
