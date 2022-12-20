<?php
/**
 * Ajax functions
 *
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
function opalestate_gallery_property() {
    $post_id = intval($_POST['property_id']);
    $gallery = get_post_meta($post_id, OPALESTATE_PROPERTY_PREFIX . 'gallery', 1);

    echo json_encode(['gallery' => $gallery]);
    die;
}

add_action('wp_ajax_opalestate_gallery_property', 'opalestate_gallery_property');
add_action('wp_ajax_nopriv_opalestate_gallery_property', 'opalestate_gallery_property');
/**
 * Searches for users via ajax and returns a list of results
 *
 * @return void
 * @since  1.0
 *
 */
function opalestate_ajax_search_property_users() {

    $search_query = trim($_GET['q']);

    $get_users_args = [
        'number' => 9999,
        'search' => $search_query . '*',
    ];

    $get_users_args = apply_filters('opalestate_search_users_args', $get_users_args);

    $found_users = apply_filters('opalestate_ajax_found_property_users', get_users($get_users_args), $search_query);

    $users = [];
    if (!empty($found_users)) {
        foreach ($found_users as $user) {
            $users[] = [
                'id'          => $user->ID,
                'name'        => $user->display_name,
                'avatar_url'  => OpalEstate_User::get_author_picture($user->ID),
                'full_name'   => $user->display_name,
                'description' => 'okokok',
            ];
        }
    }

    $output = [
        'total_count'        => count($users),
        'items'              => $users,
        'incomplete_results' => false,
    ];
    echo json_encode($output);

    die();
}

add_action('wp_ajax_opalestate_search_property_users', 'opalestate_ajax_search_property_users');

add_action('wp_ajax_opalestate_ajax_get_state_by_country', 'opalestate_ajax_get_state_by_country');
add_action('wp_ajax_nopriv_opalestate_ajax_get_state_by_country', 'opalestate_ajax_get_state_by_country');
function opalestate_ajax_get_state_by_country() {
    if (!isset($_POST['country'])) {
        die;
    }

    $country = sanitize_text_field($_POST['country']);

    $is_search = isset($_POST['is_search']) && $_POST['is_search'];

    $terms = get_terms([
        'taxonomy'   => 'opalestate_state',
        'orderby'    => 'name',
        'order'      => 'ASC',
        'hide_empty' => $is_search ? true : false,
        'meta_query' => [
            [
                'key'   => 'opalestate_state_location',
                'value' => $country,
            ],
        ],
    ]);

    $states   = [];
    $states[] = [
        'id'   => $is_search ? '-1' : '',
        'text' => esc_html__('Select State', 'opalestate-pro'),
    ];

    if ($terms) {
        foreach ($terms as $term) {
            $states[] = [
                'id'   => $term->slug,
                'text' => $term->name,
            ];
        }
    }

    echo json_encode($states);
    wp_die();
}

add_action('wp_ajax_opalestate_ajax_get_city_by_state', "opalestate_ajax_get_city_by_state");
function opalestate_ajax_get_city_by_state() {
    if (!isset($_POST['state'])) {
        die;
    }

    $state = sanitize_text_field($_POST['state']);

    $is_search = isset($_POST['is_search']) && $_POST['is_search'];

    $terms = get_terms([
        'taxonomy'   => 'opalestate_city',
        'orderby'    => 'name',
        'order'      => 'ASC',
        'hide_empty' => $is_search ? true : false,
        'meta_query' => [
            [
                'key'   => 'opalestate_city_state',
                'value' => $state,
            ],
        ],
    ]);

    $cities   = [];
    $cities[] = [
        'id'   => $is_search ? '-1' : '',
        'text' => esc_html__('Select City', 'opalestate-pro'),
    ];

    if ($terms) {
        foreach ($terms as $term) {
            $cities[] = [
                'id'   => $term->slug,
                'text' => $term->name,
            ];
        }
    }

    echo json_encode($cities);
    wp_die();
}

/* set feature property */
add_action('wp_ajax_opalestate_set_feature_property', 'opalestate_set_feature_property');
// add_action( 'wp_ajax_nopriv_opalestate_set_feature_property', 'opalestate_set_feature_property' );
if (!function_exists('opalestate_set_feature_property')) {
    function opalestate_set_feature_property() {

        if (!isset($_REQUEST['nonce']) && !wp_verify_nonce($_REQUEST['nonce'], 'nonce')) {
            return;
        }
        if (!isset($_REQUEST['property_id'])) {
            return;
        }
        update_post_meta(absint($_REQUEST['property_id']), OPALESTATE_PROPERTY_PREFIX . 'featured', 1);

        wp_redirect(admin_url('edit.php?post_type=opalestate_property'));
        exit();
    }
}
/* remove feature property */
add_action('wp_ajax_opalestate_remove_feature_property', 'opalestate_remove_feature_property');
// add_action( 'wp_ajax_nopriv_opalestate_remove_feature_property', 'opalestate_remove_feature_property' );
if (!function_exists('opalestate_remove_feature_property')) {
    function opalestate_remove_feature_property() {
        if (!isset($_REQUEST['nonce']) && !wp_verify_nonce($_REQUEST['nonce'], 'nonce')) {
            return;
        }

        if (!isset($_REQUEST['property_id'])) {
            return;
        }

        update_post_meta(absint($_REQUEST['property_id']), OPALESTATE_PROPERTY_PREFIX . 'featured', '');
        wp_redirect(admin_url('edit.php?post_type=opalestate_property'));
        exit();
    }
}

/**
 * Set Featured Item Following user
 */
add_action('wp_ajax_opalestate_toggle_featured_property', 'opalestate_toggle_featured_property');
add_action('wp_ajax_nopriv_opalestate_toggle_featured_property', 'opalestate_toggle_featured_property');

function opalestate_toggle_featured_property() {

    global $current_user;
    wp_get_current_user();
    $user_id = $current_user->ID;

    $property_id = intval($_POST['property_id']);
    $post        = get_post($property_id);

    if ($post->post_author == $user_id) {

        $check = apply_filters('opalestate_set_feature_property_checked', false);
        if ($check) {
            do_action('opalestate_toggle_featured_property_before', $user_id, $property_id);
            update_post_meta($property_id, OPALESTATE_PROPERTY_PREFIX . 'featured', 'on');
            echo json_encode(['status' => true, 'msg' => esc_html__('Could not set this as featured', 'opalestate-pro')]);
            wp_die();
        }
    }

    echo json_encode(['status' => false, 'msg' => esc_html__('Could not set this as featured', 'opalestate-pro')]);
    wp_reset_query();
    wp_die();

}

/**
 * load more properties by agency
 */
add_action('wp_ajax_get_agent_property', 'opalestate_get_agent_property');
add_action('wp_ajax_nopriv_get_agent_property', 'opalestate_get_agent_property');

function opalestate_get_agent_property() {
    global $paged;
    $post = [

        'paged' => 1,
        'id'    => '',
        'limit' => apply_filters('opalesate_agent_properties_limit', 6),
    ];

    $post = array_merge($post, $_POST);
    extract($post);

    set_query_var('paged', $post['paged']);
    $query = Opalestate_Query::get_agent_property(null, absint($post['id']), absint($limit));

    $paged = absint($post['paged']);
    if ($query->have_posts()) : ?>
        <div class="opalestate-rows">
            <div class="<?php echo apply_filters('opalestate_row_container_class', 'opal-row'); ?>">
                <?php while ($query->have_posts()) : $query->the_post(); ?>
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <?php echo opalestate_load_template_path('content-property-grid'); ?>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
        <?php if ($query->max_num_pages > 1): ?>
            <div class="w-pagination"><?php opalestate_pagination($query->max_num_pages); ?></div>
        <?php endif; ?>
    <?php
    endif;
    wp_reset_postdata();
    exit;
}

/**
 * load more properties by agency
 */
add_action('wp_ajax_get_agency_property', 'opalestate_get_agency_property');
add_action('wp_ajax_nopriv_get_agency_property', 'opalestate_get_agency_property');

function opalestate_get_agency_property() {
    global $paged;

    $post = [
        'id'      => 0,
        'paged'   => 1,
        'user_id' => '',
        'related' => '',
        'limit'   => apply_filters('opalesate_agency_properties_limit', 5),
    ];

    $post = array_merge($post, $_POST);
    extract($post);

    $user_id = get_post_meta(absint($id), OPALESTATE_AGENCY_PREFIX . 'user_id', true);
    $user_id = $user_id ? $user_id : null;
    $query   = Opalestate_Query::get_agency_property(absint($id), absint($user_id), absint($limit), absint($paged));

    $paged = absint($post['paged']);
    if ($query->have_posts()) : ?>
        <div class="opalestate-rows">
            <div class="<?php echo apply_filters('opalestate_row_container_class', 'row opal-row'); ?>">
                <?php while ($query->have_posts()) : $query->the_post(); ?>
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <?php echo opalestate_load_template_path('content-property-list-v2'); ?>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
        <?php if ($query->max_num_pages > 1): ?>
            <div class="w-pagination"><?php opalestate_pagination($query->max_num_pages); ?></div>
        <?php endif; ?>
    <?php
    endif;
    wp_reset_postdata();
    exit;
}

function opalestate_update_api_key() {
    ob_start();

    global $wpdb;

    check_ajax_referer('update-api-key', 'security');

    if (!current_user_can('manage_opalestate_settings')) {
        wp_die(-1);
    }

    $response = [];

    try {
        if (empty($_POST['description'])) {
            throw new Exception(__('Description is missing.', 'opalestate-pro'));
        }
        if (empty($_POST['user'])) {
            throw new Exception(__('User is missing.', 'opalestate-pro'));
        }
        if (empty($_POST['permissions'])) {
            throw new Exception(__('Permissions is missing.', 'opalestate-pro'));
        }

        $key_id      = isset($_POST['key_id']) ? absint($_POST['key_id']) : 0;
        $description = sanitize_text_field(wp_unslash($_POST['description']));
        $permissions = (in_array(wp_unslash($_POST['permissions']), ['read', 'write', 'read_write'], true)) ? sanitize_text_field(wp_unslash($_POST['permissions'])) : 'read';
        $user_id     = absint($_POST['user']);

        // Check if current user can edit other users.
        if ($user_id && !current_user_can('edit_user', $user_id)) {
            if (get_current_user_id() !== $user_id) {
                throw new Exception(__('You do not have permission to assign API Keys to the selected user.', 'opalestate-pro'));
            }
        }

        if (0 < $key_id) {
            $data = [
                'user_id'     => $user_id,
                'description' => $description,
                'permissions' => $permissions,
            ];

            $wpdb->update(
                $wpdb->prefix . 'opalestate_api_keys',
                $data,
                ['key_id' => $key_id],
                [
                    '%d',
                    '%s',
                    '%s',
                ],
                ['%d']
            );

            $response                    = $data;
            $response['consumer_key']    = '';
            $response['consumer_secret'] = '';
            $response['message']         = __('API Key updated successfully.', 'opalestate-pro');
        } else {
            $consumer_key    = 'ck_' . opalestate_rand_hash();
            $consumer_secret = 'cs_' . opalestate_rand_hash();

            $data = [
                'user_id'         => $user_id,
                'description'     => $description,
                'permissions'     => $permissions,
                'consumer_key'    => opalestate_api_hash($consumer_key),
                'consumer_secret' => $consumer_secret,
                'truncated_key'   => substr($consumer_key, -7),
            ];

            $wpdb->insert(
                $wpdb->prefix . 'opalestate_api_keys',
                $data,
                [
                    '%d',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                ]
            );

            $key_id                      = $wpdb->insert_id;
            $response                    = $data;
            $response['consumer_key']    = $consumer_key;
            $response['consumer_secret'] = $consumer_secret;
            $response['message']         = __('API Key generated successfully. Make sure to copy your new keys now as the secret key will be hidden once you leave this page.', 'opalestate-pro');
            $response['revoke_url']      = '<a style="color: #a00; text-decoration: none;" href="' . esc_url(wp_nonce_url(add_query_arg(['revoke-key' => $key_id],
                    admin_url('edit.php?post_type=opalestate_property&page=opalestate-settings&tab=api_keys')), 'revoke')) . '">' . __('Revoke key', 'opalestate-pro') . '</a>';
        }
    } catch (Exception $e) {
        wp_send_json_error(['message' => $e->getMessage()]);
    }

    // wp_send_json_success must be outside the try block not to break phpunit tests.
    wp_send_json_success($response);
}

add_action('wp_ajax_opalestate_update_api_key', 'opalestate_update_api_key');

function opalestate_ajax_setting_custom_fields() {
    $metas = Opalestate_Property_MetaBox::metaboxes_info_fields();

    $metabox_key = [];

    if ($metas) {
        foreach ($metas as $meta_item) {
            $metabox_key[] = $meta_item['id'];
        }
    }

    echo json_encode(['data' => $metabox_key]);
    exit;
}

add_action('wp_ajax_opalestate_setting_custom_fields', 'opalestate_ajax_setting_custom_fields');
add_action('wp_ajax_nopriv_opalestate_setting_custom_fields', 'opalestate_ajax_setting_custom_fields');
