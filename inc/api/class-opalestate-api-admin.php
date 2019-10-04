<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Opaljob_API Class
 *
 * Renders API returns as a JSON/XML array
 *
 * @since  1.1
 */
class Opalestate_API_Admin {
	/**
	 * Latest API Version
	 */
	const VERSION = 1;
	/**
	 * Pretty Print?
	 *
	 * @var bool
	 * @access private
	 * @since  1.1
	 */
	private $pretty_print = false;
	/**
	 * Log API requests?
	 *
	 * @var bool
	 * @access public
	 * @since  1.1
	 */
	public $log_requests = true;
	/**
	 * Is this a valid request?
	 *
	 * @var bool
	 * @access private
	 * @since  1.1
	 */
	private $is_valid_request = false;
	/**
	 * User ID Perpropertying the API Request
	 *
	 * @var int
	 * @access public
	 * @since  1.1
	 */
	public $user_id = 0;
	/**
	 * Instance of OpalJob Stats class
	 *
	 * @var object
	 * @access private
	 * @since  1.1
	 */
	private $stats;
	/**
	 * Response data to return
	 *
	 * @var array
	 * @access private
	 * @since  1.1
	 */
	private $data = array();
	/**
	 *
	 * @var bool
	 * @access public
	 * @since  1.1
	 */
	public $override = true;

	/**
	 * Render Sidebar
	 *
	 *	Display Sidebar on left side and next is main content
	 *
	 * @since 1.0
	 *
	 * @return string
	 */
	public static function get_instance(){

		static $_instance;
		if( !$_instance ){
			$_instance = new Opalestate_API_Admin();
		}
		return $_instance;
	}
	/**
	 * Setup the OpalJob API
	 *
	 * @since 1.1
	 * @access public
	 */
	public function __construct() {
	}
	/**
	 * Registers query vars for API access
	 *
	 * @access public
	 * @since  1.1
	 *
	 * @param array $vars Query vars
	 *
	 * @return string[] $vars New query vars
	 */
	public function register_actions () {
		add_action( 'admin_init', array( $this,'process_action') );
		add_action( 'show_user_profile', array( $this, 'user_key_field' ) );
		add_action( 'edit_user_profile', array( $this, 'user_key_field' ) );
		add_action( 'personal_options_update', array( $this, 'update_key' ) );
		add_action( 'edit_user_profile_update', array( $this, 'update_key' ) );
		add_action( 'opaljob_action', array( $this, 'process_api_key' ) );
		// Setup a backwards compatibility check for user API Keys
		add_filter( 'get_user_metadata', array( $this, 'api_key_backwards_compat' ), 10, 4 );
		// Determine if JSON_PRETTY_PRINT is available
		$this->pretty_print = defined( 'JSON_PRETTY_PRINT' ) ? JSON_PRETTY_PRINT : null;
		// Allow API request logging to be turned off
		$this->log_requests = apply_filters( 'opaljob_api_log_requests', $this->log_requests );
	}
	/**
	 * Registers query vars for API access
	 *
	 * @access public
	 * @since  1.1
	 *
	 * @param array $vars Query vars
	 *
	 * @return string[] $vars New query vars
	 */
	public function process_action() {
		if( isset($_REQUEST['opaljob_action']) ){
			$args = array(
				'user_id' => isset( $_REQUEST['user_id'] ) ? sanitize_text_field( $_REQUEST['user_id'] ) : 0,
				'key_permissions' => isset( $_REQUEST['key_permissions'] ) ? sanitize_text_field( $_REQUEST['key_permissions'] ) : 'read',
				'description' => isset( $_REQUEST['description'] ) ? sanitize_text_field( $_REQUEST['description'] ) : '',
				'opaljob_api_process' => isset( $_REQUEST['opaljob_api_process'] ) ? sanitize_text_field( $_REQUEST['opaljob_api_process'] ) : ''
			);

			do_action( 'opaljob_action', $args  );
		}
	}
	/**
	 * Retrieve the user ID based on the public key provided
	 *
	 * @access public
	 * @since  1.1
	 * @global WPDB $wpdb Used to query the database using the WordPress
	 *                      Database API
	 *
	 * @param string $key Public Key
	 *
	 * @return bool if user ID is found, false otherwise
	 */
	public function get_user( $key = '' ) {
		global $wpdb, $wp_query;
		if ( empty( $key ) ) {
			$key = urldecode( $wp_query->query_vars['key'] );
		}
		if ( empty( $key ) ) {
			return false;
		}
		$user = get_transient( md5( 'opaljob_api_user_' . $key ) );
		if ( false === $user ) {
			$user = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = %s LIMIT 1", $key ) );
			set_transient( md5( 'opaljob_api_user_' . $key ), $user, DAY_IN_SECONDS );
		}
		if ( $user != null ) {
			$this->user_id = $user;
			return $user;
		}
		return false;
	}
	public function get_user_public_key( $user_id = 0 ) {
		global $wpdb;
		if ( empty( $user_id ) ) {
			return '';
		}
		$cache_key       = md5( 'opaljob_api_user_public_key' . $user_id );
		$user_public_key = get_transient( $cache_key );
		if ( empty( $user_public_key ) ) {
			$user_public_key = $wpdb->get_var( $wpdb->prepare( "SELECT meta_key FROM $wpdb->usermeta WHERE meta_value = 'opaljob_user_public_key' AND user_id = %d", $user_id ) );
			set_transient( $cache_key, $user_public_key, HOUR_IN_SECONDS );
		}
		return $user_public_key;
	}
	public function get_user_secret_key( $user_id = 0 ) {
		global $wpdb;
		if ( empty( $user_id ) ) {
			return '';
		}
		$cache_key       = md5( 'opaljob_api_user_secret_key' . $user_id );
		$user_secret_key = get_transient( $cache_key );
		if ( empty( $user_secret_key ) ) {
			$user_secret_key = $wpdb->get_var( $wpdb->prepare( "SELECT meta_key FROM $wpdb->usermeta WHERE meta_value = 'opaljob_user_secret_key' AND user_id = %d", $user_id ) );
			set_transient( $cache_key, $user_secret_key, HOUR_IN_SECONDS );
		}
		return $user_secret_key;
	}
	/**
	 * Modify User Profile
	 *
	 * Modifies the output of profile.php to add key generation/revocation
	 *
	 * @access public
	 * @since  1.1
	 *
	 * @param object $user Current user info
	 *
	 * @return void
	 */
	function user_key_field( $user ) {
		if ( ( opaljob_options( 'api_allow_user_keys', false ) || current_user_can( 'manage_opaljob_settings' ) ) && current_user_can( 'edit_user', $user->ID ) ) {
			$user = get_userdata( $user->ID );
			?>
			<hr class="clearfix clear">
			<table class="property-table">
				<tbody>
				<tr>
					<th>
						<?php esc_html_e( 'OpalJob API Keys', 'opaljob' ); ?>
					</th>
					<td>
						<?php
						$public_key = $this->get_user_public_key( $user->ID );
						$secret_key = $this->get_user_secret_key( $user->ID );
						?>
						<?php if ( empty( $user->opaljob_user_public_key ) ) { ?>
							<input name="opaljob_set_api_key" type="checkbox" id="opaljob_set_api_key" value="0"/>
							<span class="description"><?php esc_html_e( 'Generate API Key', 'opaljob' ); ?></span>
						<?php } else { ?>
							<strong style="display:inline-block; width: 125px;"><?php esc_html_e( 'Public key:', 'opaljob' ); ?>&nbsp;</strong>
							<input type="text" disabled="disabled" class="regular-text" id="publickey" value="<?php echo esc_attr( $public_key ); ?>"/>
							<br/>
							<strong style="display:inline-block; width: 125px;"><?php esc_html_e( 'Secret key:', 'opaljob' ); ?>&nbsp;</strong>
							<input type="text" disabled="disabled" class="regular-text" id="privatekey" value="<?php echo esc_attr( $secret_key ); ?>"/>
							<br/>
							<strong style="display:inline-block; width: 125px;"><?php esc_html_e( 'Token:', 'opaljob' ); ?>&nbsp;</strong>
							<input type="text" disabled="disabled" class="regular-text" id="token" value="<?php echo esc_attr( $this->get_token( $user->ID ) ); ?>"/>
							<br/>
							<input name="opaljob_set_api_key" type="checkbox" id="opaljob_set_api_key" value="0"/>
							<span class="description"><label for="opaljob_set_api_key"><?php esc_html_e( 'Revoke API Keys', 'opaljob' ); ?></label></span>
						<?php } ?>
					</td>
				</tr>
				</tbody>
			</table>
		<?php }
	}
	/**
	 * Process an API key generation/revocation
	 *
	 * @access public
	 * @since  1.1
	 *
	 * @param array $args
	 *
	 * @return void
	 */
	public function process_api_key( $args ) {

		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'opaljob-api-nonce' ) ) {
			wp_die( esc_html__( 'Nonce verification failed.', 'opaljob' ), esc_html__( 'Error', 'opaljob' ), array( 'response' => 403 ) );
		}
		if ( empty( $args['user_id'] ) ) {
			wp_die( esc_html__( 'User ID Required.', 'opaljob' ), esc_html__( 'Error', 'opaljob' ), array( 'response' => 401 ) );
		}
		if ( is_numeric( $args['user_id'] ) ) {
			$user_id = isset( $args['user_id'] ) ? absint( $args['user_id'] ) : get_current_user_id();
		} else {
			$userdata = get_user_by( 'login', $args['user_id'] );
			$user_id  = $userdata->ID;
		}
		$process = isset( $args['opaljob_api_process'] ) ? strtolower( $args['opaljob_api_process'] ) : false;

		if ( $user_id == get_current_user_id() && ! opaljob_options( 'allow_user_api_keys' ) && ! current_user_can( 'manage_opaljob_settings' ) ) {
			wp_die(
				sprintf(
				/* translators: %s: process */
					esc_html__( 'You do not have permission to %s API keys for this user.', 'opaljob' ),
					$process
				),
				esc_html__( 'Error', 'opaljob' ),
				array( 'response' => 403 )
			);
		} elseif ( ! current_user_can( 'manage_opaljob_settings' ) ) {
			wp_die(
				sprintf(
				/* translators: %s: process */
					esc_html__( 'You do not have permission to %s API keys for this user.', 'opaljob' ),
					$process
				),
				esc_html__( 'Error', 'opaljob' ),
				array( 'response' => 403 )
			);
		}
		switch ( $process ) {
			case 'generate':
				if ( $this->generate_api_key( $user_id ) ) {
					delete_transient( 'opaljob_total_api_keys' );
					wp_redirect( add_query_arg( 'opaljob-message', 'api-key-generated', 'edit.php?post_type=opaljob_job&page=opal-job-settings&tab=api_keys' ) );
					exit();
				} else {
					wp_redirect( add_query_arg( 'opaljob-message', 'api-key-failed', 'edit.php?post_type=opaljob_job&page=opal-job-settings&tab=api_keys' ) );
					exit();
				}
				break;
			case 'regenerate':
				$this->generate_api_key( $user_id, true );
				delete_transient( 'opaljob_total_api_keys' );
				wp_redirect( add_query_arg( 'opaljob-message', 'api-key-regenerated', 'edit.php?post_type=opaljob_job&page=opal-job-settings&tab=api_keys' ) );
				exit();
				break;
			case 'revoke':
				$this->revoke_api_key( $user_id );
				delete_transient( 'opaljob_total_api_keys' );
				wp_redirect( add_query_arg( 'opaljob-message', 'api-key-revoked', 'edit.php?post_type=opaljob_job&page=opal-job-settings&tab=api_keys' ) );
				exit();
				break;
			default;
				break;
		}
	}
	/**
	 * Generate new API keys for a user
	 *
	 * @access public
	 * @since  1.1
	 *
	 * @param int $user_id User ID the key is being generated for
	 * @param boolean $regenerate Regenerate the key for the user
	 *
	 * @return boolean True if (re)generated succesfully, false otherwise.
	 */
	public function generate_api_key( $user_id = 0, $regenerate = false ) {
		if ( empty( $user_id ) ) {
			return false;
		}
		$user = get_userdata( $user_id );
		if ( ! $user ) {
			return false;
		}
		$public_key = $this->get_user_public_key( $user_id );
		$secret_key = $this->get_user_secret_key( $user_id );
		if ( empty( $public_key ) || $regenerate == true ) {
			$new_public_key = $this->generate_public_key( $user->user_email );
			$new_secret_key = $this->generate_private_key( $user->ID );
		} else {
			return false;
		}
		if ( $regenerate == true ) {
			$this->revoke_api_key( $user->ID );
		}
		update_user_meta( $user_id, $new_public_key, 'opaljob_user_public_key' );
		update_user_meta( $user_id, $new_secret_key, 'opaljob_user_secret_key' );
		return true;
	}
	/**
	 * Revoke a users API keys
	 *
	 * @access public
	 * @since  1.1
	 *
	 * @param int $user_id User ID of user to revoke key for
	 *
	 * @return string
	 */
	public function revoke_api_key( $user_id = 0 ) {
		if ( empty( $user_id ) ) {
			return false;
		}
		$user = get_userdata( $user_id );
		if ( ! $user ) {
			return false;
		}
		$public_key = $this->get_user_public_key( $user_id );
		$secret_key = $this->get_user_secret_key( $user_id );
		if ( ! empty( $public_key ) ) {
			delete_transient( md5( 'opaljob_api_user_' . $public_key ) );
			delete_transient( md5( 'opaljob_api_user_public_key' . $user_id ) );
			delete_transient( md5( 'opaljob_api_user_secret_key' . $user_id ) );
			delete_user_meta( $user_id, $public_key );
			delete_user_meta( $user_id, $secret_key );
		} else {
			return false;
		}
		return true;
	}
	public function get_version() {
		return self::VERSION;
	}
	/**
	 * Generate and Save API key
	 *
	 * Generates the key requested by user_key_field and stores it in the database
	 *
	 * @access public
	 * @since  1.1
	 *
	 * @param int $user_id
	 *
	 * @return void
	 */
	public function update_key( $user_id ) {
		if ( current_user_can( 'edit_user', $user_id ) && isset( $_POST['opaljob_set_api_key'] ) ) {
			$user = get_userdata( $user_id );
			$public_key = $this->get_user_public_key( $user_id );
			$secret_key = $this->get_user_secret_key( $user_id );
			if ( empty( $public_key ) ) {
				$new_public_key = $this->generate_public_key( $user->user_email );
				$new_secret_key = $this->generate_private_key( $user->ID );
				update_user_meta( $user_id, $new_public_key, 'opaljob_user_public_key' );
				update_user_meta( $user_id, $new_secret_key, 'opaljob_user_secret_key' );
			} else {
				$this->revoke_api_key( $user_id );
			}
		}
	}
	/**
	 * Generate the public key for a user
	 *
	 * @access private
	 * @since  1.1
	 *
	 * @param string $user_email
	 *
	 * @return string
	 */
	private function generate_public_key( $user_email = '' ) {
		$auth_key = defined( 'AUTH_KEY' ) ? AUTH_KEY : '';
		$public   = hash( 'md5', $user_email . $auth_key . date( 'U' ) );
		return $public;
	}
	/**
	 * Generate the secret key for a user
	 *
	 * @access private
	 * @since  1.1
	 *
	 * @param int $user_id
	 *
	 * @return string
	 */
	private function generate_private_key( $user_id = 0 ) {
		$auth_key = defined( 'AUTH_KEY' ) ? AUTH_KEY : '';
		$secret   = hash( 'md5', $user_id . $auth_key . date( 'U' ) );
		return $secret;
	}
	/**
	 * Retrieve the user's token
	 *
	 * @access private
	 * @since  1.1
	 *
	 * @param int $user_id
	 *
	 * @return string
	 */
	public function get_token( $user_id = 0 ) {
		return hash( 'md5', $this->get_user_secret_key( $user_id ) . $this->get_user_public_key( $user_id ) );
	}
	/**
	 * API Key Backwards Compatibility
	 *
	 * A Backwards Compatibility call for the change of meta_key/value for users API Keys
	 *
	 * @since  1.3.6
	 *
	 * @param  string $check     Whether to check the cache or not
	 * @param  int    $object_id The User ID being passed
	 * @param  string $meta_key  The user meta key
	 * @param  bool   $single    If it should return a single value or array
	 *
	 * @return string            The API key/secret for the user supplied
	 */
	public function api_key_backwards_compat( $check, $object_id, $meta_key, $single ) {
		if ( $meta_key !== 'opaljob_user_public_key' && $meta_key !== 'opaljob_user_secret_key' ) {
			return $check;
		}
		$return = $check;
		switch ( $meta_key ) {
			case 'opaljob_user_public_key':
				$return = $this->get_user_public_key( $object_id );
				break;
			case 'opaljob_user_secret_key':
				$return =$this->get_user_secret_key( $object_id );
				break;
		}
		if ( ! $single ) {
			$return = array( $return );
		}
		return $return;
	}
}
