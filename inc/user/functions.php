<?php
/**
 * Checks if the current user has a role.
 *
 * @param string $role The role.
 * @return bool
 */
function opalestate_current_user_has_role($role) {
    return opalestate_user_has_role(wp_get_current_user(), $role);
}

/**
 * Checks if a user has a role.
 *
 * @param int|\WP_User $user The user.
 * @param string $role The role.
 * @return bool
 */
function opalestate_user_has_role($user, $role) {
    if (!is_object($user)) {
        $user = get_userdata($user);
    }

    if (!$user || !$user->exists()) {
        return false;
    }

    return in_array($role, $user->roles, true);
}

function opalestate_submssion_list_page($args = []) {
    return opalestate_get_user_management_page_uri(['tab' => 'submission_list']);
}

function opalestate_get_user_management_page_uri($args = []) {
    global $opalestate_options;

    $uri = isset($opalestate_options['user_management_page']) ? get_permalink(absint($opalestate_options['user_management_page'])) : get_bloginfo('url');

    if (!empty($args)) {
        // Check for backward compatibility
        if (is_string($args)) {
            $args = str_replace('?', '', $args);
        }
        $args = wp_parse_args($args);
        $uri  = add_query_arg($args, $uri);
    }

    return apply_filters('opalestate_user_management_page_uri', $uri);
}

function opalestate_is_user_management_page() {
    global $opalestate_options;

    $page_id = isset($opalestate_options['user_management_page']) ? (absint($opalestate_options['user_management_page'])) : 0;

    return $page_id && is_page($page_id);
}

function opalestate_get_current_url($args = []) {
    global $wp;
    if (isset($_GET['tab']) && $_GET['tab']) {
        $args['tab'] = $_GET['tab'];
    }
    $current_url = home_url(add_query_arg($args, $wp->request));

    return $current_url;
}


function opalestate_get_user_tab_uri($tab) {
    $args['tab'] = $tab;

    return opalestate_get_current_url($args);
}


function opalestate_management_show_content_page_tab() {
    $tab = isset($_GET['tab']) && $_GET['tab'] ? sanitize_text_field($_GET['tab']) : 'dashboard';

    if (!opalestate_current_user_can_access_dashboard_page($tab)) {
        echo opalestate_load_template_path('user/error');

        return;
    }

    $tab_hook = $tab;
    $tab_hook = apply_filters('opalestate_user_content_tab_hook', $tab_hook, $tab);
    $fnc      = 'opalestate_user_content_' . $tab_hook . '_page';

    $content = apply_filters($fnc, '');

    if ($content) {
        echo $content;
    } else {
        if (function_exists($fnc)) {
            $fnc();
        } else {
            opalestate_user_content_dashboard_page();
        }
    }
}

function opalestate_user_savedsearch_page($args = []) {

    $uri = get_permalink(opalestate_get_option('saved_link_page', '/'));

    if (!empty($args)) {
        // Check for backward compatibility
        if (is_string($args)) {
            $args = str_replace('?', '', $args);
        }
        $args = wp_parse_args($args);
        $uri  = add_query_arg($args, $uri);
    }

    return $uri;
}


function opalestate_my_account_page($id = false, $args = []) {

    $page = get_permalink(opalestate_get_option('user_myaccount_page', '/'));
    if ($id) {
        $edit_page_id = opalestate_get_option('user_myaccount_page');
        $page         = $edit_page_id ? get_permalink($edit_page_id) : $page;
        $page         = add_query_arg('id', $id, $page);
    }
    if ($args) {
        foreach ($args as $key => $value) {
            $page = add_query_arg($key, $value, $page);
        }
    }

    return $page;
}

function opalestate_submssion_page($id = false, $args = []) {
    $page = get_permalink(opalestate_get_option('submission_page', '/'));

    if ($id) {
        $edit_page_id = opalestate_get_option('submission_edit_page');
        $page         = $edit_page_id ? get_permalink($edit_page_id) : $page;
        $page         = add_query_arg('id', $id, $page);
    }
    if ($args) {
        foreach ($args as $key => $value) {
            $page = add_query_arg($key, $value, $page);
        }
    }

    return $page;
}

function opalestate_management_user_menu() {
}


function opalestate_management_user_menu_tabs() {
    $menu = opalestate_get_user_dashboard_menus();

    $output = '<ul class="account-links nav-pills nav-stacked">';

    global $post;

    $uri = opalestate_get_user_management_page_uri();

    $current_tab = isset($_GET['tab']) && $_GET['tab'] ? sanitize_text_field($_GET['tab']) : 'dashboard';

    foreach ($menu as $key => $item) {
        if (preg_match("#http#", $item['link'])) {
            $link      = $item['link'];
            $is_active = is_page($item['id']) ? ' active' : '';
        } else {
            $link      = $uri . '?tab=' . $item['link'];
            $is_active = isset($_GET['tab']) && $current_tab == $item['link'] ? ' active' : '';
        }

        $output .= '<li class="account-links-item ' . $key . $is_active . '"><a href="' . $link . '"><i class="' . $item['icon'] . '"></i> ' . $item['title'] . '</a></li>';
    }

    $output .= '<li><a href="' . wp_logout_url(home_url('/')) . '"> <i class="fa fa-unlock"></i> ' . esc_html__('Log out', 'opalestate-pro') . '</a></li>';

    $output .= '</ul>';

    echo $output;
}

function opalestate_get_user_dashboard_menus() {
    global $opalestate_options;
    $menu = [];

    $menu['dashboard'] = [
        'icon'  => 'fas fa-chart-line',
        'link'  => opalestate_current_user_can_access_dashboard_page('dashboard') ? 'dashboard' : get_dashboard_url(),
        'title' => esc_html__('Dashboard', 'opalestate-pro'),
        'id'    => isset($opalestate_options['profile_page']) ? $opalestate_options['profile_page'] : 0,
    ];

    if (opalestate_current_user_can_access_dashboard_page('profile') && 'on' === opalestate_get_option('enable_dashboard_profile', 'on')) {
        $menu['profile'] = [
            'icon'  => 'far fa-user',
            'link'  => 'profile',
            'title' => esc_html__('Personal Information', 'opalestate-pro'),
            'id'    => isset($opalestate_options['profile_page']) ? $opalestate_options['profile_page'] : 0,
        ];
    }

    if (opalestate_current_user_can_access_dashboard_page('favorite') && 'on' === opalestate_get_option('enable_dashboard_favorite', 'on')) {
        $menu['favorite'] = [
            'icon'  => 'far fa-heart',
            'link'  => 'favorite',
            'title' => esc_html__('Favorite', 'opalestate-pro'),
            'id'    => isset($opalestate_options['favorite_page']) ? $opalestate_options['favorite_page'] : 0,
        ];
    }

    if (opalestate_current_user_can_access_dashboard_page('reviews') && 'on' === opalestate_get_option('enable_dashboard_reviews', 'on')) {
        $menu['reviews'] = [
            'icon'  => 'far fa-star',
            'link'  => 'reviews',
            'title' => esc_html__('Reviews', 'opalestate-pro'),
            'id'    => isset($opalestate_options['profile_page']) ? $opalestate_options['profile_page'] : 0,
        ];
    }

    if (opalestate_current_user_can_access_dashboard_page('messages') && 'on' === opalestate_get_option('message_log', 'on')) {
        $menu['messages'] = [
            'icon'  => 'fa fa-envelope',
            'link'  => 'messages',
            'title' => esc_html__('Messages', 'opalestate-pro'),
            'id'    => isset($opalestate_options['profile_page']) ? $opalestate_options['profile_page'] : 0,
        ];
    }

    if (opalestate_current_user_can_access_dashboard_page('submission') && 'on' === opalestate_get_option('enable_dashboard_submission', 'on')) {
        $menu['submission'] = [
            'icon'  => 'fa fa-upload',
            'link'  => 'submission',
            'title' => esc_html__('Submit Property', 'opalestate-pro'),
            'id'    => isset($opalestate_options['submission_page']) ? $opalestate_options['submission_page'] : 0,
        ];
    }

    if (opalestate_current_user_can_access_dashboard_page('myproperties') && 'on' === opalestate_get_option('enable_dashboard_properties', 'on')) {
        $statistics = new OpalEstate_User_Statistics();

        $menu['myproperties'] = [
            'icon'  => 'fas fa-building',
            'link'  => 'submission_list',
            'title' => esc_html__('My Properties', 'opalestate-pro') . '<span class="count">' . $statistics->get_count_properties() . '</span>',
            'id'    => isset($opalestate_options['submission_list_page']) ? $opalestate_options['submission_list_page'] : 0,
        ];
    }

    return apply_filters('opalestate_management_user_menu', $menu);
}

function opalestate_user_content_dashboard_page() {
    echo opalestate_load_template_path('user/dashboard');
}

if (!function_exists('opalestate_create_user')) {
    /**
     * create new wp user
     */
    function opalestate_create_user($credentials = []) {
        $cred = wp_parse_args($credentials, [
            'user_login' => '',
            'user_email' => '',
            'user_pass'  => '',
            'first_name' => '',
            'last_name'  => '',
        ]);

        /* sanitize user email */
        $user_email = sanitize_email($cred['user_email']);
        if (email_exists($user_email)) {
            return new WP_Error('email-exists', esc_html__('An account is already registered with your email address. Please login.', 'opalestate-pro'));
        }

        $username = sanitize_user($cred['user_login']);
        if (!$username || !validate_username($username)) {
            return new WP_Error('username-invalid', esc_html__('Please enter a valid account username.', 'opalestate-pro'));
        }
        /* if username exists */
        if (username_exists($username)) {
            return new WP_Error('username-exists', esc_html__('Username is already exists.', 'opalestate-pro'));
        }

        /* password empty */
        if (!$cred['user_pass']) {
            return new WP_Error('password-empty', esc_html__('Password is requried.', 'opalestate-pro'));
        } else {
            $password = $cred['user_pass'];
        }

        $user_data = apply_filters('opalestate_create_user_data', [
            'user_login' => $username,
            'user_pass'  => $password,
            'user_email' => $user_email,
        ]);

        /* insert new wp user */
        $user_id = wp_insert_user($user_data);
        if (is_wp_error($user_id)) {
            return new WP_Error('user-create-failed', $user_id->get_error_message());
        }

        /* allow hook like insert user meta. create new post type agent in opalmembership */
        do_action('opalmembership_create_new_user_successfully', $user_id, $user_data, $cred);

        return $user_id;
    }
}

/**
 * Get user meta.
 *
 * @param $user_id
 * @param $key
 */
function opalestate_get_user_meta($user_id, $key, $single = true) {
    return get_user_meta($user_id, OPALESTATE_USER_PROFILE_PREFIX . $key, $single);
}

/**
 * Current user can access dashboard page?
 *
 * @param $page
 * @return bool
 */
function opalestate_current_user_can_access_dashboard_page($page = '') {
    if (!is_user_logged_in()) {
        return false;
    }

    $current_user = wp_get_current_user();
    $roles        = $current_user->roles;
    $allowd_roles = opalestate_get_allowed_roles();

    foreach ($roles as $role) {
        if (in_array($role, $allowd_roles)) {
            return apply_filters('opalestate_opalestate_user_can_access', true, $role, $page);
        }
    }

    return false;
}

/**
 * User has estate roles?
 *
 * @param $user_id
 * @return bool
 */
function opalestate_user_has_estate_roles($user_id) {
    $user_meta    = get_userdata($user_id);
    $roles        = $user_meta->roles;
    $allowd_roles = opalestate_get_allowed_roles();

    foreach ($roles as $role) {
        if (in_array($role, $allowd_roles)) {
            return true;
        }
    }

    return false;
}

/**
 * Get allowed roles for dashboard page.
 */
function opalestate_get_allowed_roles() {
    return apply_filters('opalestate_get_allowed_roles', [
        'opalestate_agent',
        'opalestate_agency',
        'opalestate_manager',
        'administrator',
    ]);
}
