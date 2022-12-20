<?php

/**
 * Class Opalestate_Facebook_Login
 */
class Opalestate_Facebook_Login {
    /**
     * @var mixed
     */
    protected $facebook_app_id;

    /**
     * @var mixed
     */
    protected $facebook_secret;

    /**
     * Opalestate_Facebook_Login constructor.
     */
    public function __construct() {
        $this->facebook_app_id = opalestate_get_option('facebook_app_id', '');
        $this->facebook_secret = opalestate_get_option('facebook_secret', '');

        $this->includes();

        add_action('query_vars', [$this, 'add_query_vars']);
        add_action('parse_request', [$this, 'process']);
        add_action('wp_ajax_opalestate_ajax_redirect_facebook_login_link', [$this, 'ajax_redirect_facebook_login_link']);
        add_action('wp_ajax_nopriv_opalestate_ajax_redirect_facebook_login_link', [$this, 'ajax_redirect_facebook_login_link']);
    }

    /**
     * Includes.
     */
    public function includes() {
        if (!class_exists('Facebook/Facebook')) {
            require_once 'Facebook/autoload.php';
        }
    }

    /**
     * Add query vars.
     *
     * @param $vars
     * @return array
     */
    public function add_query_vars($vars) {
        $vars[] = 'opal_facebook_login';

        return $vars;
    }

    /**
     * Redirect facebook login link via AJAX.
     *
     * @throws \Facebook\Exceptions\FacebookSDKException
     */
    public function ajax_redirect_facebook_login_link() {
        if ('off' === opalestate_get_option('enable_facebook_login')) {
            wp_send_json_error('This feature is disabled.', 404);
        }

        $facebook_app_id = $this->facebook_app_id;
        $facebook_secret = $this->facebook_secret;

        if (!$facebook_app_id || !$facebook_secret) {
            wp_send_json_error('Missing keys!', 404);
        }

        $fb = new Facebook\Facebook([
            'app_id'                => $facebook_app_id,
            'app_secret'            => $facebook_secret,
            'default_graph_version' => 'v3.2',
        ]);

        $helper      = $fb->getRedirectLoginHelper();
        $permissions = ['email'];
        $link        = add_query_arg('opal_facebook_login', '1', home_url('/'));
        $login_url   = $helper->getLoginUrl($link, $permissions);

        if (!$facebook_app_id || !$facebook_secret) {
            wp_send_json_error('Missing keys!', 404);
        } else {
            wp_send_json_success($login_url, 200);
        }
        wp_die();
    }

    /**
     * Process.
     *
     * @param $wp
     */
    public function process($wp) {
        if (array_key_exists('opal_facebook_login', $wp->query_vars)) {
            if (isset($wp->query_vars['opal_facebook_login']) && $wp->query_vars['opal_facebook_login'] == '1') {
                if ((isset($_GET['code']) && isset($_GET['state']))) {
                    $vsessionid = session_id();
                    if (empty($vsessionid)) {
                        session_name('PHPSESSID');
                        session_start();
                    }
                    $this->login();
                }
            }
            wp_die();
        }
    }

    /**
     * Handle login.
     *
     * @throws \Facebook\Exceptions\FacebookSDKException
     */
    public function login() {
        $facebook_app_id = $this->facebook_app_id;
        $facebook_secret = $this->facebook_secret;

        $fb = new Facebook\Facebook([
            'app_id'                => $facebook_app_id,
            'app_secret'            => $facebook_secret,
            'default_graph_version' => 'v3.2',
        ]);

        $helper = $fb->getRedirectLoginHelper();

        try {
            $accessToken = $helper->getAccessToken();
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        if (!isset($accessToken)) {
            if ($helper->getError()) {
                header('HTTP/1.0 401 Unauthorized');
                echo "Error: " . $helper->getError() . "\n";
                echo "Error Code: " . $helper->getErrorCode() . "\n";
                echo "Error Reason: " . $helper->getErrorReason() . "\n";
                echo "Error Description: " . $helper->getErrorDescription() . "\n";
            } else {
                header('HTTP/1.0 400 Bad Request');
                echo 'Bad request';
            }
            exit;
        }

        // Logged in
        // echo '<h3>Access Token</h3>';
        // var_dump( $accessToken->getValue() );

        // The OAuth 2.0 client handler helps us manage access tokens
        $oAuth2Client = $fb->getOAuth2Client();

        // Get the access token metadata from /debug_token
        $tokenMetadata = $oAuth2Client->debugToken($accessToken);
        // echo '<h3>Metadata</h3>';
        // var_dump( $tokenMetadata );

        // Validation (these will throw FacebookSDKException's when they fail)
        $tokenMetadata->validateAppId($facebook_app_id); // Replace {app-id} with your app id
        // If you know the user ID this access token belongs to, you can validate it here
        //$tokenMetadata->validateUserId('123');
        $tokenMetadata->validateExpiration();

        if (!$accessToken->isLongLived()) {
            // Exchanges a short-lived access token for a long-lived one
            try {
                $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                echo "<p>Error getting long-lived access token: " . $e->getMessage() . "</p>\n\n";
                exit;
            }

            // echo '<h3>Long-lived</h3>';
            // var_dump( $accessToken->getValue() );
        }

        $_SESSION['fb_access_token'] = (string)$accessToken;

        try {
            // Returns a `Facebook\FacebookResponse` object
            $response = $fb->get('/me?fields=id,email,name,first_name,last_name', $accessToken);
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            print 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            print 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        $u = $response->getGraphUser();

        $email = filter_var($u['email'], FILTER_SANITIZE_EMAIL);

        if (!is_user_logged_in()) {
            $ID              = email_exists($email);
            $random_password = wp_generate_password(12, false);

            if ($ID) {
                // Login.
                $user_info = get_userdata($ID);
                wp_set_password($random_password, $ID);

                // Update user meta.
                update_user_meta($ID, 'opal_user_last_activity_date', strtotime(date('d-m-Y H:i:s')));

                $credentials                  = [];
                $credentials['user_login']    = $user_info->user_login;
                $credentials['user_password'] = $random_password;
                $credentials['remember']      = true;

                $this->signon($credentials);
            } else {
                // Register.
                $sanitized_user_login = sanitize_user('Facebook - ' . $u['name']);
                if (!validate_username($sanitized_user_login)) {
                    $sanitized_user_login = sanitize_user('facebook' . $u['id']);
                }
                $defaul_user_name = $sanitized_user_login;
                $i                = 1;
                while (username_exists($sanitized_user_login)) {
                    $sanitized_user_login = $defaul_user_name . $i;
                    $i++;
                }

                $credentials                  = [];
                $credentials['user_login']    = $sanitized_user_login;
                $credentials['user_password'] = $random_password;
                $credentials['remember']      = true;

                $user_id = wp_create_user($sanitized_user_login, $random_password, $email);

                // Update user meta.
                update_user_meta($user_id, 'opal_user_registered', 'facebook');

                /**
                 * After create Google user.
                 */
                do_action('opalestate_after_create_facebook_user_new_email', $user_id);

                wp_update_user([
                    'ID'           => $user_id,
                    'display_name' => $u['name'],
                    'first_name'   => isset($u['first_name']) && $u['first_name'] ? $u['first_name'] : '',
                    'last_name'    => isset($u['last_name']) && $u['last_name'] ? $u['last_name'] : '',
                ]);

                $this->signon($credentials);
            }
            exit();
        } else {
            $user_info = wp_get_current_user();
            set_site_transient($user_info->ID . '_facebook_admin_notice', 'Facebook logged', 3600);
        }
        exit();
    }

    /**
     * Set facebook unique id.
     *
     * @return mixed|string
     */
    public function get_uniqid() {
        if (isset($_COOKIE['opal_facebook_uniqid'])) {
            if (get_site_transient('n_' . $_COOKIE['opal_facebook_uniqid']) !== false) {
                return $_COOKIE['opal_facebook_uniqid'];
            }
        }

        $_COOKIE['opal_facebook_uniqid'] = uniqid('nextend', true);
        setcookie('opal_facebook_uniqid', $_COOKIE['opal_facebook_uniqid'], time() + 3600, '/');
        set_site_transient('n_' . $_COOKIE['opal_facebook_uniqid'], 1, 3600);

        return $_COOKIE['opal_facebook_uniqid'];
    }

    /**
     * Redirect.
     */
    public function redirect() {
        $redirect = Opalestate_Social_Login::get_redirect_url();

        header('LOCATION: ' . $redirect);
        delete_site_transient($this->get_uniqid() . '_facebook_redirect');
        exit();
    }

    public function signon($credentials) {
        $user_signon = wp_signon($credentials, true);

        if (is_wp_error($user_signon)) {
            wp_redirect(esc_url(home_url()));
        } else {
            /**
             * After signon successfully.
             */
            do_action('opalestate_after_signon_successfully', $credentials);

            $redirect = opalestate_get_user_management_page_uri();

            if (!empty($_REQUEST['redirect'])) {
                $redirect = sanitize_text_field($_REQUEST['redirect']);
            }

            $redirect = apply_filters('opalestate_signon_redirect_url', $redirect);
            wp_redirect($redirect);
        }
    }
}
