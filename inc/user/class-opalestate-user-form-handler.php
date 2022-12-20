<?php
if (!defined('ABSPATH')) {
    exit();
}

/**
 * Login processer
 */
class Opalestate_User_Form_Handler {

    /**
     * Login processer
     */
    public function __construct() {

        add_action('init', [$this, 'process_login']);
        add_action('init', [$this, 'process_register']);

        add_action('wp_ajax_opalestate_login_form', [$this, 'process_login']);
        add_action('wp_ajax_opalestate_register_form', [$this, 'process_register']);
        add_filter('opalestate_signon_redirect_url', [$this, 'login_redirect_url']);
        add_filter('opalestate_register_redirect_url', [$this, 'register_redirect_url']);
    }

    /**
     * Login processer
     */
    public static function process_login() {

        $nonce_value = isset($_POST['opalestate-login-popup-nonce']) ? sanitize_text_field($_POST['opalestate-login-popup-nonce']) : '';
        $nonce_value = isset($_POST['opalestate-login-nonce']) ? sanitize_text_field($_POST['opalestate-login-nonce']) : $nonce_value;


        /* verify wp nonce */
        if (!wp_verify_nonce($nonce_value, 'opalestate-login')) {
            return;
        }

        try {

            do_action('opalestate_user_proccessing_login_before');

            $credentials = [];
            $username    = isset($_POST['username']) ? sanitize_user($_POST['username']) : '';
            $password    = isset($_POST['password']) ? sanitize_text_field($_POST['password']) : '';

            /* sanitize, allow hook process like block somebody =)))) */
            $validation = apply_filters('opalestate_validation_process_login_error', new WP_Error(), $username, $password);
            if ($validation->get_error_code()) {
                throw new Exception('<strong>' . esc_html__('ERROR', 'opalestate-pro') . ':</strong> ' . $validation->get_error_message());
            }

            /* validate username */
            if (!$username) {
                throw new Exception('<strong>' . esc_html__('ERROR', 'opalestate-pro') . ':</strong> ' . esc_html__('Username is required.', 'opalestate-pro'));
            } else {

                if (is_email($username)) {
                    /* user object */
                    $user = get_user_by('email', $username);
                    if ($user->user_login) {
                        $credentials['user_login'] = $user->user_login;
                    } else {
                        throw new Exception('<strong>' . esc_html__('ERROR', 'opalestate-pro') . ':</strong> ' . esc_html__('A user could not be found with this email address.',
                                'opalestate-pro'));
                    }
                } else {
                    $credentials['user_login'] = $username;
                }

            }

            /* validate password if it empty */
            if (!$password) {
                throw new Exception('<strong>' . esc_html__('ERROR', 'opalestate-pro') . ':</strong> ' . esc_html__('Password is required.', 'opalestate-pro'));
            }
            $credentials['user_password'] = $password;
            /* is rembemer me checkbox */
            $credentials['remember'] = isset($_POST['remember']);

            /* signon user */
            $user = wp_signon($credentials, is_ssl());
            if (is_wp_error($user)) {
                throw new Exception($user->get_error_message());
            } else {

                /* after signon successfully */
                do_action('opalestate_after_signon_successfully', $user);
                $redirect = opalestate_get_dashdoard_page_uri();

                if (!empty($_POST['redirect'])) {
                    $redirect = sanitize_text_field($_POST['redirect']);
                } elseif (wp_get_referer()) {
                    $redirect = wp_get_referer();
                }

                $redirect = apply_filters('opalestate_signon_redirect_url', $redirect);

                if (opalestate_is_ajax_request()) {

                    opalestate_add_notice('success', esc_html__('Logged successfully, welcome back!', 'opalestate-pro'));
                    ob_start();
                    opalestate_print_notices();
                    $message = ob_get_clean();


                    wp_send_json([
                        'status'   => true,
                        'message'  => $message,
                        'redirect' => $redirect,
                    ]);

                } else {
                    wp_safe_redirect($redirect);
                    exit();
                }
            }

            do_action('opalestate_user_proccessing_login_after');

        } catch (Exception $e) {
            opalestate_add_notice('error', $e->getMessage());
        }

        if (opalestate_is_ajax_request()) {
            ob_start();
            opalestate_print_notices();
            $message = ob_get_clean();
            wp_send_json([
                'status'  => false,
                'message' => $message,
            ]);
        }
    }

    /**
     * Register processer
     */
    public function process_register() {
        if (!isset($_POST['opalestate-register-nonce'])) {
            return;
        }

        $nonce_value = isset($_POST['_wpnonce']) ? sanitize_text_field($_POST['_wpnonce']) : '';
        $nonce_value = isset($_POST['opalestate-register-nonce']) ? sanitize_text_field($_POST['opalestate-register-nonce']) : $nonce_value;

        /* verify wp nonce */
        if (!isset($_POST['confirmed_register']) || !wp_verify_nonce($nonce_value, 'opalestate-register')) {
            return;
        }

        try {

            do_action('opalestate_user_proccessing_register_before');

            $credentials = [];
            $username    = isset($_POST['username']) ? sanitize_user($_POST['username']) : '';
            $email       = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
            $password    = isset($_POST['password']) ? sanitize_text_field($_POST['password']) : '';
            $password1   = isset($_POST['password1']) ? sanitize_text_field($_POST['password1']) : '';

            /* sanitize, allow hook process like block somebody =)))) */
            $validation = apply_filters('opalestate_validation_process_register_error', new WP_Error(), $username, $email);

            /* sanitize */
            if ($validation->get_error_code()) {
                throw new Exception('<strong>' . esc_html__('ERROR', 'opalestate-pro') . ':</strong> ' . $validation->get_error_message());
            }

            /* validate username */
            if (!$username) {
                throw new Exception('<strong>' . esc_html__('ERROR', 'opalestate-pro') . ':</strong> ' . esc_html__('Username is required.', 'opalestate-pro'));
            } else {
                $credentials['user_login'] = $username;
            }

            /* validate email */
            if (!$email) {
                throw new Exception('<strong>' . esc_html__('ERROR', 'opalestate-pro') . ':</strong> ' . esc_html__('Email is required.', 'opalestate-pro'));
            } else {
                $credentials['user_email'] = $email;
            }

            /* validate password */
            if (!$password) {
                throw new Exception('<strong>' . esc_html__('ERROR', 'opalestate-pro') . ':</strong> ' . esc_html__('Password is required.', 'opalestate-pro'));
            }
            if ($password !== $password1) {
                throw new Exception('<strong>' . esc_html__('ERROR', 'opalestate-pro') . ':</strong> ' . esc_html__('Re-Password is not match.', 'opalestate-pro'));
            }

            $credentials['user_pass'] = $password;

            /* create new user */
            $user_id = opalestate_create_user($credentials);

            if (is_wp_error($user_id)) {
                throw new Exception('<strong>' . esc_html__('ERROR', 'opalestate-pro') . ':</strong> ' . $user_id->get_error_message());
            } else {
                /* After register successfully */
                do_action('opalestate_after_register_successfully', $user_id);
                wp_new_user_notification($user_id, false, 'user');

                $redirect = home_url();
                if (opalestate_get_option('login_user')) {
                    wp_set_auth_cookie($user_id);
                    $redirect = opalestate_get_dashdoard_page_uri();
                } elseif (!empty($_POST['redirect'])) {
                    $redirect = sanitize_text_field($_POST['redirect']);
                } elseif (wp_get_referer()) {
                    $redirect = wp_get_referer();
                }

                do_action('opalestate_user_proccessing_register_after');

                $redirect = apply_filters('opalestate_register_redirect_url', $redirect);

                /* Is ajax request */
                if (opalestate_is_ajax_request()) {
                    wp_send_json(['status' => true, 'redirect' => $redirect]);
                } else {
                    wp_safe_redirect($redirect);
                    exit();
                }
            }

        } catch (Exception $e) {
            opalestate_add_notice('error', $e->getMessage());
        }

        /* is ajax request */
        if (opalestate_is_ajax_request()) {
            ob_start();
            opalestate_print_notices();
            $message = ob_get_clean();
            wp_send_json([
                'status'  => false,
                'message' => $message,
            ]);
        }
    }

    /**
     * process user doForgotPassword with username/password
     *
     * return Json Data with messsage and login status
     */
    public function process_forgot_password() {

        // First check the nonce, if it fails the function will break
        check_ajax_referer('ajax-pbr-lostpassword-nonce', 'security');

        global $wpdb;

        $account = sanitize_text_field($_POST['user_login']);

        if (empty($account)) {
            $error = esc_html__('Enter an username or e-mail address.', 'opalestate-pro');
        } else {
            if (is_email($account)) {
                if (email_exists($account)) {
                    $get_by = 'email';
                } else {
                    $error = esc_html__('There is no user registered with that email address.', 'opalestate-pro');
                }
            } elseif (validate_username($account)) {
                if (username_exists($account)) {
                    $get_by = 'login';
                } else {
                    $error = esc_html__('There is no user registered with that username.', 'opalestate-pro');
                }
            } else {
                $error = esc_html__('Invalid username or e-mail address.', 'opalestate-pro');
            }
        }

        if (empty ($error)) {
            $random_password = wp_generate_password();

            $user = get_user_by($get_by, $account);

            $update_user = wp_update_user(['ID' => $user->ID, 'user_pass' => $random_password]);

            if ($update_user) {

                $from = get_option('admin_email'); // Set whatever you want like mail@yourdomain.com

                if (!(isset($from) && is_email($from))) {
                    $sitename = strtolower($_SERVER['SERVER_NAME']);
                    if (substr($sitename, 0, 4) == 'www.') {
                        $sitename = substr($sitename, 4);
                    }
                    $from = 'do-not-reply@' . $sitename;
                }

                $to      = $user->user_email;
                $subject = esc_html__('Your new password', 'opalestate-pro');
                $sender  = 'From: ' . get_option('name') . ' <' . $from . '>' . "\r\n";

                $message = esc_html__('Your new password is: ', 'opalestate-pro') . $random_password;

                $headers[] = 'MIME-Version: 1.0' . "\r\n";
                $headers[] = 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $headers[] = "X-Mailer: PHP \r\n";
                $headers[] = $sender;

                $mail = wp_mail($to, $subject, $message, $headers);
                if ($mail) {
                    $success = esc_html__('Check your email address for you new password.', 'opalestate-pro');
                } else {
                    $error = esc_html__('System is unable to send you mail containg your new password.', 'opalestate-pro');
                }
            } else {
                $error = esc_html__('Oops! Something went wrong while updating your account.', 'opalestate-pro');
            }
        }

        if (!empty($error)) {
            echo wp_send_json(['status' => false, 'message' => ($error)]);
        }

        if (!empty($success)) {
            echo wp_send_json(['status' => false, 'message' => $success]);
        }
        die();
    }

    public function login_redirect_url($redirect) {
        if ('on' === opalestate_get_option('enable_login_redirect_to_dashboard', 'off')) {
            $redirect = opalestate_get_user_management_page_uri();
        }

        return $redirect;
    }

    public function register_redirect_url($redirect) {
        if ('on' === opalestate_get_option('enable_register_redirect_to_dashboard', 'off')) {
            $redirect = opalestate_get_user_management_page_uri();
        }

        return $redirect;
    }
}

new Opalestate_User_Form_Handler();
