<?php
/**
 * $Desc$
 *
 * @version    $Id$
 * @package    opalestate
 * @author     Opal  Team <info@wpopal.com >
 * @copyright  Copyright (C) 2019 wpopal.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @website  http://www.wpopal.com
 * @support  http://www.wpopal.com/support/forum.html
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Give_Session Class
 *
 * @since 1.0
 */
class Opalestate_Session {

    /**
     * Holds our session data
     *
     * @var array
     * @access private
     * @since  1.0
     */
    private $session;

    /**
     * Whether to use PHP $_SESSION or WP_Session
     *
     * @var bool
     * @access private
     * @since  1.0
     */
    private $use_php_sessions = false;

    /**
     * Expiration Time
     *
     * @var int
     * @access private
     * @since  1.0
     */
    private $exp_option = false;

    /**
     * Session index prefix
     *
     * @var string
     * @access private
     * @since  1.0
     */
    private $prefix = '';

    /**
     * Get things started
     *
     * Defines our WP_Session constants, includes the necessary libraries and
     * retrieves the WP Session instance
     *
     * @since 1.0
     */
    public function __construct() {

        $this->use_php_sessions = $this->use_php_sessions();
        $this->exp_option       = 604800; //opalestate_get_option( 'session_lifetime' );

        if ($this->use_php_sessions) {

            if (is_multisite()) {
                $this->prefix = '_' . get_current_blog_id();
            }

            // Use PHP SESSION (must be enabled via the OPALMEMBERSHIP_USE_PHP_SESSIONS constant)
            add_action('init', array($this, 'maybe_start_session'), -2);

        } else {

            // Use WP_Session (default)
            if (!defined('WP_SESSION_COOKIE')) {
                define('WP_SESSION_COOKIE', 'opalestate_wp_session');
            }

            if (!class_exists('Recursive_ArrayAccess')) {
                //	require_once OPALESTATE_PLUGIN_DIR . 'inc/libraries/class-recursive-arrayaccess.php';
            }

            if (!class_exists('WP_Session')) {
                require_once OPALESTATE_PLUGIN_DIR . 'inc/libraries/wp_session/class-wp-session.php';
                require_once OPALESTATE_PLUGIN_DIR . 'inc/libraries/wp-session.php';
            }

            add_filter('wp_session_expiration_variant', array($this, 'set_expiration_variant_time'), 99999);
            add_filter('wp_session_expiration', array($this, 'set_expiration_time'), 99999);

        }

        if (empty($this->session) && !$this->use_php_sessions) {
            add_action('plugins_loaded', array($this, 'init'), -1);
        } else {
            add_action('init', array($this, 'init'), -1);
        }

    }

    /**
     * Setup the WP_Session instance
     *
     * @access public
     * @return array $this->session
     * @since  1.0
     */
    public function init() {

        if ($this->use_php_sessions) {
            $this->session = isset($_SESSION['opalestate-pro' . $this->prefix]) && is_array($_SESSION['opalestate-pro' . $this->prefix]) ? $_SESSION['opalestate-pro' . $this->prefix] : array();
        } else {
            $this->session = WP_Session::get_instance();
        }

        return $this->session;
    }

    /**
     * Retrieve session ID
     *
     * @access public
     * @return string Session ID
     * @since  1.0
     */
    public function get_id() {
        return $this->session->session_id;
    }

    /**
     * Retrieve a session variable
     *
     * @access public
     * @param string $key Session key
     *
     * @return string Session variable
     * @since  1.0
     *
     */
    public function get($key = '', $default = false) {
        $key = sanitize_key($key);

        return isset($this->session[$key]) ? maybe_unserialize($this->session[$key]) : $default;
    }

    /**
     * Set a session variable
     *
     * @param $key $_SESSION key
     * @param $value $_SESSION variable
     *
     * @return mixed Session variable
     * @since 1.0
     *
     */
    public function set($key, $value) {

        $key = sanitize_key($key);

        if (is_array($value)) {
            $this->session[$key] = serialize($value);
        } else {
            $this->session[$key] = $value;
        }

        if ($this->use_php_sessions) {
            $_SESSION['opalestate-pro' . $this->prefix] = $this->session;
        }

        return $this->session[$key];
    }

    /**
     * Set Cookie Variant Time
     *
     * @description Force the cookie expiration variant time to custom expiration option, less and hour; defaults to 23 hours (set_expiration_variant_time used in WP_Session)
     *
     * @access      public
     * @return int
     * @since       1.0
     *
     */
    public function set_expiration_variant_time() {
        return (!empty($this->exp_option) ? (intval($this->exp_option) - 3600) : 30 * 60 * 23);
    }

    /**
     * Set the Cookie Expiration
     *
     * @description Force the cookie expiration time if set, default to 24 hours
     *
     * @access      public
     * @return int
     * @since       1.0
     *
     */
    public function set_expiration_time() {
        return (!empty($this->exp_option) ? intval($this->exp_option) : 30 * 60 * 24);
    }

    /**
     * Starts a new session if one hasn't started yet.
     *
     * @return null
     * Checks to see if the server supports PHP sessions
     * or if the OPALMEMBERSHIP_USE_PHP_SESSIONS constant is defined
     *
     * @access public
     * @return bool $ret True if we are using PHP sessions, false otherwise
     * @since  1.0
     */
    public function use_php_sessions() {

        $ret = false;

        // If the database variable is already set, no need to run autodetection
        $opalestate_use_php_sessions = (bool)get_option('opalestate_use_php_sessions');

        if (!$opalestate_use_php_sessions) {

            // Attempt to detect if the server supports PHP sessions
            if (function_exists('session_start') && !ini_get('safe_mode')) {

                $this->set('opalestate_use_php_sessions', 1);

                if ($this->get('opalestate_use_php_sessions')) {

                    $ret = true;

                    // Set the database option
                    update_option('opalestate_use_php_sessions', true);

                }

            }

        } else {
            $ret = $opalestate_use_php_sessions;
        }

        // Enable or disable PHP Sessions based on the OPALMEMBERSHIP_USE_PHP_SESSIONS constant
        if (defined('OPALMEMBERSHIP_USE_PHP_SESSIONS') && OPALMEMBERSHIP_USE_PHP_SESSIONS) {
            $ret = true;
        } else if (defined('OPALMEMBERSHIP_USE_PHP_SESSIONS') && !OPALMEMBERSHIP_USE_PHP_SESSIONS) {
            $ret = false;
        }

        return (bool)apply_filters('opalestate_use_php_sessions', $ret);
    }

    /**
     * Maybe Start Session
     *
     * @description Starts a new session if one hasn't started yet.
     * @see         http://php.net/manual/en/function.session-set-cookie-params.php
     */
    public function maybe_start_session() {

//		session_destroy(); //Uncomment for testing ONLY

        if (!session_id() && !headers_sent()) {
            $lifetime = current_time('timestamp') + $this->set_expiration_time();
            session_start();
            setcookie(session_name(), session_id(), $lifetime); //
            setcookie(session_name() . '_expiration', $lifetime, $lifetime);
        }
    }

    /**
     * Get Session Expiration
     *
     * @description  Looks at the session cookies and returns the expiration date for this session if applicable
     *
     */
    public function get_session_expiration() {

        $expiration = false;

        if (session_id() && isset($_COOKIE[session_name() . '_expiration'])) {
            $expiration = date('D, d M Y h:i:s', intval($_COOKIE[session_name() . '_expiration']));
        }

        return $expiration;
    }

}

