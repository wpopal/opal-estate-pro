<?php

/**
 * Class Opalestate_Google_Login
 */
class Opalestate_Google_Login {
    /**
     * @var mixed
     */
    protected $google_client_id;

    /**
     * @var mixed
     */
    protected $google_client_secret;

    /**
     * @var mixed
     */
    protected $google_api_key;

    /**
     * Opalestate_Google_Login constructor.
     */
    public function __construct() {
        $this->google_client_id     = opalestate_get_option('google_client_id', '');
        $this->google_client_secret = opalestate_get_option('google_client_secret', '');
        $this->google_api_key       = opalestate_get_option('google_api_key', '');

        $this->includes();

        add_filter('init', [$this, 'add_query_var']);
        add_action('parse_request', [$this, 'google_login_request']);
        add_action('login_init', [$this, 'google_login']);
        add_action('wp_ajax_opalestate_ajax_redirect_google_login_link', [$this, 'ajax_redirect_google_login_link']);
        add_action('wp_ajax_nopriv_opalestate_ajax_redirect_google_login_link', [$this, 'ajax_redirect_google_login_link']);
    }

    /**
     * Includes.
     */
    public function includes() {
        if (!class_exists('apiClient') && !class_exists('apiOauth2Service')) {
            require_once 'Google/apiClient.php';
            require_once 'Google/contrib/apiOauth2Service.php';
        }
    }

    /**
     * Get login url.
     *
     * @return string
     */
    public static function get_login_url() {
        return home_url('wp-login.php') . '?opal_google_login=1';
    }

    /**
     * Redirect google login link via AJAX.
     */
    public function ajax_redirect_google_login_link() {
        if ('off' === opalestate_get_option('enable_google_login')) {
            wp_send_json_error('This feature is disabled.', 404);
        }

        $google_client_id     = $this->google_client_id;
        $google_client_secret = $this->google_client_secret;
        $google_api_key       = $this->google_api_key;
        $google_redirect_url  = static::get_login_url();

        if (!$google_client_id || !$google_client_secret || !$google_api_key) {
            wp_send_json_error('Missing keys!', 404);
        } else {
            wp_send_json_success($google_redirect_url, 200);
        }
        wp_die();
    }

    /**
     * Add query var.
     */
    public function add_query_var() {
        global $wp;
        $wp->add_query_var('opal_google_login');
    }

    /**
     * Login when parse request.
     */
    public function google_login_request() {
        global $wp;
        if ($wp->request == 'opal_google_login' || isset($wp->query_vars['opal_google_login'])) {
            $this->login();
        }
    }

    /**
     * Login in login page.
     */
    public function google_login() {
        if (isset($_REQUEST['opal_google_login']) && $_REQUEST['opal_google_login'] == '1') {
            $this->login();
        }
    }

    /**
     * Handle login.
     *
     * @throws \apiAuthException
     */
    public function login() {
        $google_client_id     = $this->google_client_id;
        $google_client_secret = $this->google_client_secret;
        $google_api_key       = $this->google_api_key;
        $google_redirect_url  = static::get_login_url();

        if (!$google_client_id || !$google_client_secret || !$google_api_key) {
            wp_redirect(esc_url(home_url()));
            exit();
        }

        $client = new apiClient();
        $client->setClientId($google_client_id);
        $client->setClientSecret($google_client_secret);
        $client->setDeveloperKey($google_api_key);
        $client->setRedirectUri($google_redirect_url);
        $client->setApprovalPrompt('auto');
        $oauth2 = new apiOauth2Service($client);

        // If isset code, redirect to google redirect url.
        if (isset($_GET['code'])) {
            $_GET['redirect'] = $google_redirect_url;

            set_site_transient($this->get_uniqid() . '_google_redirect', sanitize_text_field($_GET['redirect']), 3600);
            $client->authenticate();
            $access_token = $client->getAccessToken();
            set_site_transient($this->get_uniqid() . '_google_atoken', $access_token, 3600);
            header('Location: ' . filter_var($google_redirect_url, FILTER_SANITIZE_URL));
            exit();
        }

        $access_token = get_site_transient($this->get_uniqid() . '_google_atoken');
        if ($access_token !== false) {
            $client->setAccessToken($access_token);
        }

        // Delete transient if logout.
        if (isset($_REQUEST['logout'])) {
            delete_site_transient($this->get_uniqid() . '_google_atoken');
            $client->revokeToken();
        }

        // Process user data if has data, else redirect to createAuthUrl.
        if ($client->getAccessToken()) {
            try {
                $u = $oauth2->userinfo->get();

                // The access token may have been updated lazily.
                set_site_transient($this->get_uniqid() . '_google_atoken', $client->getAccessToken(), 3600);

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
                        $sanitized_user_login = sanitize_user('Google - ' . $u['name']);
                        if (!validate_username($sanitized_user_login)) {
                            $sanitized_user_login = sanitize_user('google' . $u['id']);
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
                        update_user_meta($user_id, 'opal_user_registered', 'google');

                        if (isset($u['picture']) && $u['picture']) {
                            update_user_meta($user_id, OPALESTATE_USER_PROFILE_PREFIX . 'avatar', esc_url($u['picture']));
                        }

                        /**
                         * After create Google user.
                         */
                        do_action('opalestate_after_create_google_user_new_email', $user_id);

                        wp_update_user([
                            'ID'           => $user_id,
                            'display_name' => $u['name'],
                            'first_name'   => isset($u['given_name']) && $u['given_name'] ? $u['given_name'] : '',
                            'last_name'    => isset($u['family_name']) && $u['family_name'] ? $u['family_name'] : '',
                        ]);

                        $this->signon($credentials);
                    }
                    exit();
                } else {
                    $user_info = wp_get_current_user();
                    set_site_transient($user_info->ID . '_google_admin_notice', 'Google logged', 3600);
                }
            } catch (Google_ServiceException $e) {
                echo sprintf('<p>' . 'Service error' . ' <code>%s</code></p>', htmlspecialchars($e->getMessage()));
                exit();
            } catch (Google_Exception $e) {
                echo sprintf('<p>' . 'Client error' . ' <code>%s</code></p>', htmlspecialchars($e->getMessage()));
                exit();
            } catch (apiServiceException $e) {
                // Handle exception. You can also catch Exception here.
                // You can also get the error code from $e->getCode();
                echo ('google_error_code') . ': ' . $e->getCode() . '<br/>';
                echo('google_authencitcation_failed');
                exit();
            }
            // End If
        } else {
            if (isset($_GET['redirect'])) {
                set_site_transient($this->get_uniqid() . '_google_redirect', $_GET['redirect'], 3600);
            }

            $redirect = get_site_transient($this->get_uniqid() . '_google_redirect');

            if ($redirect || $redirect == $google_redirect_url) {
                $redirect = esc_url(home_url('/'));
                set_site_transient($this->get_uniqid() . '_google_redirect', $redirect, 3600);
            }
            header('LOCATION: ' . $client->createAuthUrl());
            exit();
        }
        $this->redirect();
    }

    /**
     * Set google unique id.
     *
     * @return mixed|string
     */
    public function get_uniqid() {
        if (isset($_COOKIE['opal_google_uniqid'])) {
            if (get_site_transient('n_' . $_COOKIE['opal_google_uniqid']) !== false) {
                return $_COOKIE['opal_google_uniqid'];
            }
        }

        $_COOKIE['opal_google_uniqid'] = uniqid('nextend', true);
        setcookie('opal_google_uniqid', $_COOKIE['opal_google_uniqid'], time() + 3600, '/');
        set_site_transient('n_' . $_COOKIE['opal_google_uniqid'], 1, 3600);

        return $_COOKIE['opal_google_uniqid'];
    }

    /**
     * Redirect.
     */
    public function redirect() {
        $redirect = Opalestate_Social_Login::get_redirect_url();

        header('LOCATION: ' . $redirect);
        delete_site_transient($this->get_uniqid() . '_google_redirect');
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
