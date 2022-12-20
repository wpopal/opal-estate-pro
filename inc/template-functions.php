<?php
/**
 * Template functions.
 *
 * @package    opalestate
 * @author     Opal  Team <info@wpopal.com >
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Add body classes for Opalestate pages.
 *
 * @param array $classes Body Classes.
 * @return array
 */
function opalestate_body_class($classes) {
    $classes = (array)$classes;

    $classes[] = 'opalestate-active';

    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();

        if ($current_user) {
            $roles = $current_user->roles;
            if ($roles && is_array($roles)) {
                foreach ($roles as $role) {
                    $classes[] = 'opalestate-role-' . esc_attr($role);
                }
            }
        }
    }

    return array_unique($classes);
}

function opalestate_archive_search_block() {
    echo opalestate_load_template_path('parts/archive-search-block');
}

function opalestate_property_mortgage() {
    echo opalestate_load_template_path('parts/mortgage-calculator');
}

function opalestate_load_template_path($tpl, $args = [], $layout = '') {
    return Opalestate_Template_Loader::get_template_part($tpl, $args, $layout);
}

/**
 * Get image avatar placeholder src.
 *
 * @return string
 */
function opalestate_get_image_avatar_placehold() {
    return apply_filters('opalestate_get_image_avatar_placeholder', OPALESTATE_PLUGIN_URL . 'assets/images/avatar-placeholder.png');
}

function opalestate_get_admin_view($file) {
    return OPALESTATE_PLUGIN_DIR . 'inc/admin/views/' . $file;
}

function opalestate_user_fullname($user_id = null) {
    $user_info = $user_id ? new WP_User($user_id) : wp_get_current_user();
    if ($user_info->first_name) {
        if ($user_info->last_name) {
            return $user_info->first_name . ' ' . $user_info->last_name;
        }

        return $user_info->first_name;
    }

    return $user_info->display_name;
}

/**
 * Hooks Give actions, when present in the $_GET superglobal. Every opalestate_action
 * present in $_GET is called using WordPress's do_action function. These
 * functions are called on init.
 *
 * @return void
 *
 */
function opalestate_get_actions() {
    if (isset($_GET['opalestate_action'])) {
        do_action('opalestate_' . sanitize_text_field($_GET['opalestate_action']), $_GET);
    }
}

add_action('init', 'opalestate_get_actions');

/**
 * Hooks Give actions, when present in the $_POST superglobal. Every opalestate_action
 * present in $_POST is called using WordPress's do_action function. These
 * functions are called on init.
 *
 * @return void
 *
 */
function opalestate_post_actions() {
    if (isset($_POST['opalestate_action'])) {
        do_action('opalestate_' . sanitize_text_field($_POST['opalestate_action']), $_POST);
    }
}

add_action('init', 'opalestate_post_actions');

/**
 *
 */
function opalestate_template_init() {
    if (isset($_GET['display']) && ($_GET['display'] == 'list' || $_GET['display'] == 'grid' || $_GET['display'] == 'map')) {
        setcookie('opalestate_displaymode', trim($_GET['display']), time() + 3600 * 24 * 100, '/');
        $_COOKIE['opalestate_displaymode'] = trim($_GET['display']);
    }
}

add_action('init', 'opalestate_template_init');


function opalestate_get_read_message_uri($message_id) {
    $args['message_id'] = $message_id;

    return opalestate_get_current_url($args);
}

if (!function_exists('opalestate_terms_multi_check')) {
    function opalestate_terms_multi_check($terms) {
        $html = '<div class="opal-form-group">';

        foreach ($terms as $term) {
            $id   = time() . '-' . $term->slug;
            $html .= '<div class="group-item">';
            $html .= '<input  type="checkbox" class="form-control-checkbox" id="' . $id . '" name="types[]" id="' . $id . '" value="' . $term->slug . '">';
            $html .= ' <label for="' . $id . '">' . $term->name . '</label>';
            $html .= '</div>';
        }

        $html .= '</div>';

        return $html;
    }
}

if (!function_exists('opalestate_categories_multi_check')) {
    function opalestate_categories_multi_check($terms) {
        $html = '<div class="opal-form-group">';

        foreach ($terms as $term) {
            $id   = time() . '-' . $term->slug;
            $html .= '<div class="group-item">';
            $html .= '<input  type="checkbox" class="form-control-checkbox" id="' . $id . '" name="cat[' . $term->slug . ']" id="' . $id . '" value="' . $term->slug . '">';
            $html .= ' <label for="' . $id . '">' . $term->name . '</label>';
            $html .= '</div>';
        }

        $html .= '</div>';

        return $html;
    }
}

function opalestate_get_image_by_id($id) {
    if ($id) {
        $url = wp_get_attachment_url($id);

        return '<img src="' . $url . '">';
    }

    return '';
}

if (!function_exists('opalestate_get_loop_thumbnail')) {
    function opalestate_get_loop_thumbnail($size = 'property-thumbnail') { ?>
        <div class="property-box-image">
            <a href="<?php the_permalink(); ?>" class="property-box-image-inner">
                <?php if (has_post_thumbnail()) : ?>
                    <?php the_post_thumbnail(apply_filters('opalestate_loop_property_thumbnail', $size)); ?>
                <?php else: ?>
                    <?php echo opalestate_get_image_placeholder($size); ?>
                <?php endif; ?>
            </a>
        </div>
        <?php
    }
}

if (!function_exists('opalestate_get_loop_agent_thumbnail')) {
    function opalestate_get_loop_agent_thumbnail($size = 'agent-thumbnail') { ?>
        <div class="agent-box-image">
            <a href="<?php the_permalink(); ?>">
                <?php if (has_post_thumbnail()) : ?>
                    <?php the_post_thumbnail(apply_filters('opalestate_loop_agent_thumbnail', $size)); ?>
                <?php else: ?>
                    <?php echo opalestate_get_image_placeholder($size); ?>
                <?php endif; ?>
            </a>
        </div>
        <?php
    }
}

function opalestate_get_loop_short_meta() {
    echo opalestate_load_template_path('parts/property-loop-short-meta');
}

function opalestate_get_single_short_meta() {
    echo opalestate_load_template_path('single-property/short-meta');
}

/**
 *
 */
function opalestate_render_sortable_dropdown($selected = '') {

    $output = '';
    $modes  = [
        'featured_desc' => esc_html__('Featured Desending', 'opalestate-pro'),
        'price_asc'     => esc_html__('Price Ascending', 'opalestate-pro'),
        'price_desc'    => esc_html__('Price Desending', 'opalestate-pro'),
        'areasize_asc'  => esc_html__('Area Ascending', 'opalestate-pro'),
        'areasize_desc' => esc_html__('Area Desending', 'opalestate-pro'),
    ];
    $modes  = apply_filters('opalestate_sortable_modes', $modes);

    $modes  = array_merge(['' => esc_html__('Sort By', 'opalestate-pro')], $modes);
    $output = '<form id="opalestate-sortable-form" method="POST"><select name="opalsortable" class="form-control sortable-dropdown" >';
    if (empty($selected) && isset($_REQUEST['opalsortable'])) {
        $selected = sanitize_text_field($_REQUEST['opalsortable']);
    }
    foreach ($modes as $key => $mode) {

        $sselected = $key == $selected ? 'selected="selected"' : "";
        $output    .= '<option ' . $sselected . ' value="' . $key . '">' . $mode . '</option>';
    }

    $output .= '</select></form>';

    return $output;
}


/**
 * Display modes
 */
function opalestate_show_display_modes($default = '') {
    $op_display = opalestate_get_display_mode($default);

    $modes = apply_filters('opalestate_listing_display_modes', [
        'grid' => [
            'icon'  => 'fa fa-th',
            'title' => esc_html__('Grid', 'opalestate-pro'),
        ],
        'list' => [
            'icon'  => 'fa fa-th-list',
            'title' => esc_html__('List', 'opalestate-pro'),
        ],
    ]);

    echo '<div class="display-mode">';
    foreach ($modes as $key => $mode) {
        echo '<a href="' . opalestate_get_url_sort_mode($key) . '" aria-label="' . $mode['title'] . '" class="hint--top btn ' . ($op_display == $key ? 'active' : '') . '" data-mode="' . $key . '"><i class="' . $mode['icon'] . '"></i></a>';
    }

    echo '</div>';
}

if (!function_exists('opalestate_pagination')) {
    /**
     * Opalestate pagination.
     *
     * @param string $pages
     * @param int $range
     */
    function opalestate_pagination($pages = '', $range = 2) {
        global $wp_query;

        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $paged = isset($wp_query->query['paged']) ? $wp_query->query['paged'] : $paged;
        $paged = isset($_REQUEST['paged']) ? absint($_REQUEST['paged']) : $paged;

        // if(empty($paged))$paged = 1;

        $prev      = $paged - 1;
        $next      = $paged + 1;
        $showitems = ($range * 2) + 1;
        $range     = 2; // change it to show more links

        if ($pages == '') {
            global $wp_query;

            $pages = $wp_query->max_num_pages;
            if (!$pages) {
                $pages = 1;
            }
        }

        if (1 != $pages) {

            echo '<div class="opalestate-pagination pagination-main">';
            echo '<ul class="pagination">';
            echo ($paged > 2 && $paged > $range + 1 && $showitems < $pages) ? '<li><a aria-label="First" href="' . get_pagenum_link(1) . '"><span aria-hidden="true"><i class="fa fa-angle-double-left"></i></span></a></li>' : '';
            echo ($paged > 1) ? '<li><a aria-label="' . esc_html__('Previous',
                    'opalestate-pro') . '" href="' . get_pagenum_link($prev) . '"><span aria-hidden="true"><i class="fa fa-angle-left"></i></span></a></li>' : '<li class="disabled"><a aria-label="Previous"><span aria-hidden="true"><i class="fa fa-angle-left"></i></span></a></li>';
            for ($i = 1; $i <= $pages; $i++) {
                if (1 != $pages && (!($i >= $paged + $range + 1 || $i <= $paged - $range - 1) || $pages <= $showitems)) {
                    if ($paged == $i) {
                        echo '<li class="active" data-paged="' . $i . '"><a href="' . get_pagenum_link($i) . '">' . $i . ' <span class="sr-only"></span></a></li>';
                    } else {
                        echo '<li data-paged="' . $i . '"><a href="' . get_pagenum_link($i) . '">' . $i . '</a></li>';
                    }
                }
            }
            echo ($paged < $pages) ? '<li data-paged="' . $next . '"><a aria-label="' . esc_html__('Next',
                    'opalestate-pro') . '" href="' . get_pagenum_link($next) . '"><span aria-hidden="true"><i class="fa fa-angle-right"></i></span></a></li>' : '';
            echo ($paged < $pages - 1 && $paged + $range - 1 < $pages && $showitems < $pages) ? '<li data-paged="' . $prev . '"><a aria-label="Last" href="' . get_pagenum_link($pages) . '"><span aria-hidden="true"><i class="fa fa-angle-double-right"></i></span></a></li>' : '';
            echo '</ul>';
            echo '</div>';
        }
    }
}

function opalestate_show_display_status() {
    global $wp;
    $current_url = add_query_arg($wp->query_string, '', preg_replace('#page\/\d+#', '', home_url($wp->request)));
    $current_url = remove_query_arg(['paged', 'page'], $current_url);
    $gstatus     = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';

    // echo $current_url;die;
    ?>
    <div id="property-filter-status" class="clearfix">

        <?php
        $statuses = Opalestate_Taxonomy_Status::get_list();
        if ($statuses):
            echo '<form action="' . $current_url . '" id="display-by-status" method="get">';

            echo '<input type="hidden" name="status" value="">';
            echo '</form>';
            ?>
            <ul class="list-inline clearfix list-property-status pull-left">
                <li class="status-item <?php if ($gstatus == ""): ?>active<?php endif; ?>" data-id="all">
                    <span><?php esc_html_e('All', 'opalestate-pro'); ?></span>
                </li>
                <?php foreach ($statuses as $status): ?>

                    <li class="status-item <?php if ($status->slug == $gstatus): ?> active <?php endif; ?>" data-id="<?php echo $status->slug; ?>">
                        <span><?php echo $status->name; ?> </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Get display mode.
 *
 * @param string $default
 * @return string
 */
function opalestate_get_display_mode($default = '') {
    $op_display = $default ? $default : opalestate_options('displaymode', 'grid');

    if (isset($_GET['display'])) {
        $op_display = sanitize_text_field($_GET['display']);
    }

    return $op_display;
}

/**
 *
 */
function opalestate_get_search_link() {
    global $opalestate_options;
    $pages = get_pages([
        'meta_key'   => '_wp_page_template',
        'meta_value' => 'page-templates/page-property-search-results.php',
    ]);

    if ($pages) {
        $pages         = reset($pages);
        $search_submit = get_permalink($pages->ID);
    } else {
        $search_submit = isset($opalestate_options['search_map_properties_page']) ? get_permalink(absint($opalestate_options['search_map_properties_page'])) : get_bloginfo('url');
    }

    return apply_filters('opalestate_get_search_link', $search_submit);
}

function opalestate_get_user_properties_uri($args = []) {

    global $opalestate_options;

    $uri = isset($opalestate_options['submission_list_page']) ? get_permalink(absint($opalestate_options['submission_list_page'])) : get_bloginfo('url');

    if (!empty($args)) {
        // Check for backward compatibility
        if (is_string($args)) {
            $args = str_replace('?', '', $args);
        }
        $args = wp_parse_args($args);
        $uri  = add_query_arg($args, $uri);
    }

    $scheme = defined('FORCE_SSL_ADMIN') && FORCE_SSL_ADMIN ? 'https' : 'admin';

    $ajax_url = admin_url('admin-ajax.php', $scheme);

    if ((!preg_match('/^https/', $uri) && preg_match('/^https/', $ajax_url))) {
        $uri = preg_replace('/^http:/', 'https:', $uri);
    }

    return apply_filters('opalestate_get_user_properties_uri', $uri);
}

function opalestate_get_favorite_page_uri() {

    global $opalestate_options;

    $favorite_page = isset($opalestate_options['favorite_page']) ? get_permalink(absint($opalestate_options['favorite_page'])) : get_bloginfo('url');

    return apply_filters('opalestate_get_favorite_page_uri', $favorite_page);
}

/**
 * Single layout templates
 *
 * @param $layout
 * @return array
 */
function opalestate_single_layout_templates($layout) {
    $layout['v2'] = esc_html__('Vesion 2', 'opalestate-pro');
    $layout['v3'] = esc_html__('Vesion 3', 'opalestate-pro');
    $layout['v4'] = esc_html__('Vesion 4', 'opalestate-pro');
    $layout['v5'] = esc_html__('Vesion 5', 'opalestate-pro');

    return $layout;
}

add_filter('opalestate_single_layout_templates', 'opalestate_single_layout_templates');

function opalestate_single_the_property_layout() {

    global $opalestate_options;

    $layout = get_post_meta(get_the_ID(), OPALESTATE_PROPERTY_PREFIX . 'layout', true);
    if (!$layout) {
        $layout = isset($opalestate_options['layout']) ? $opalestate_options['layout'] : '';
    }

    return $layout;
}

/**
 * Single layout preview.
 *
 * @param bool $show_none_option
 * @return array
 */
function opalestate_single_layout_preview($show_none_option = true) {
    $layouts = [
        'gallery-thumbnail' => esc_html__('Gallery Thumb Nav', 'opalestate-pro'),
        'gallery-slider'    => esc_html__('Gallery Slider', 'opalestate-pro'),
        'map'               => esc_html__('Maps', 'opalestate-pro'),
        'tabs-gallery'      => esc_html__('Tabs - Gallery Active', 'opalestate-pro'),
        'tabs-map'          => esc_html__('Tabs - Map Active', 'opalestate-pro'),
        'tabs-street'       => esc_html__('Tabs - Street Map Active', 'opalestate-pro'),
        'tour360'           => esc_html__('Tour 360', 'opalestate-pro'),
        'gallery-metro'     => esc_html__('Gallery Metro', 'opalestate-pro'),
        'mark-picture'      => esc_html__('Mark Picture', 'opalestate-pro'),
    ];

    if ($show_none_option) {
        $layouts = array_merge(['' => esc_html__('Inherit', 'opalestate-pro')], $layouts);
    }

    return apply_filters('opalestate_single_layout_preview', $layouts);
}


function opalestate_property_loop_price() {
    echo opalestate_load_template_path('parts/property-loop-price');
}

function opalestate_property_featured_label() {
    echo opalestate_load_template_path('parts/featured-label');
}

function opalestate_property_label() {
    echo opalestate_load_template_path('parts/property-label');
}

function opalestate_property_status() {
    echo opalestate_load_template_path('parts/property-status');
}

/**
 * Single property logic functions
 */
function opalestate_property_meta() {
    echo opalestate_load_template_path('single-property/meta');
}

function opalestate_single_show_map() {
    return false;
}

/**
 * Single property logic functions
 */
function opalestate_property_preview() {
    global $property;
    $preview = $property->get_preview_template();

    if (!$preview) {
        $preview = opalestate_get_option('single_preview', '');
    }

    if (isset($_GET['preview']) && $_GET['preview']) {
        $preview = sanitize_text_field($_GET['preview']);
    }

    switch ($preview) {
        case 'tour360':
            echo opalestate_load_template_path('single-property/preview/virtualtour');
            break;
        case 'gallery-slider':
            echo opalestate_load_template_path('single-property/preview/gallery-slider');
            break;
        case 'tabs-gallery':
            echo opalestate_load_template_path('single-property/preview/tabs', ['tab_active' => 'gallery-slider']);
            remove_action('opalestate_after_single_property_summary', 'opalestate_property_map', 30);
            add_filter('opalestate_single_show_map', 'opalestate_single_show_map');
            break;
        case 'tabs-map':
            echo opalestate_load_template_path('single-property/preview/tabs', ['tab_active' => 'google-map']);
            remove_action('opalestate_after_single_property_summary', 'opalestate_property_map', 30);
            add_filter('opalestate_single_show_map', 'opalestate_single_show_map');
            break;
        case 'tabs-street':
            echo opalestate_load_template_path('single-property/preview/tabs', ['tab_active' => 'street-view-map']);
            remove_action('opalestate_after_single_property_summary', 'opalestate_property_map', 30);
            add_filter('opalestate_single_show_map', 'opalestate_single_show_map');
            break;
        case 'map':
            echo opalestate_load_template_path('single-property/preview/map', ['tab_active' => 'street-view-map']);
            remove_action('opalestate_after_single_property_summary', 'opalestate_property_map', 30);
            break;
        case 'mark-picture':
            echo opalestate_load_template_path('single-property/preview/mark-picture');
            break;
        case 'gallery-metro':
            echo opalestate_load_template_path('single-property/preview/gallery-metro');
            break;
        default:
            echo opalestate_load_template_path('single-property/preview');
            break;
    }
}

/**
 *
 */
function opalestate_property_content() {
    echo opalestate_load_template_path('single-property/content');
}

/**
 *
 */
function opalestate_property_information() {
    echo opalestate_load_template_path('single-property/information');
}

/**
 *
 */
function opalestate_property_amenities() {
    echo opalestate_load_template_path('single-property/amenities');
}

function opalestate_property_facilities() {
    echo opalestate_load_template_path('single-property/facilities');
}


function opalestate_property_attachments() {
    echo opalestate_load_template_path('single-property/attachments');
}

function opalestate_property_tags() {
    return the_tags('<footer class="entry-meta"><span class="tag-links">', '', '</span></footer>');
}

function opalestate_property_map() {
    echo opalestate_load_template_path('single-property/map');
}

function opalestate_property_map_v2() {
    echo opalestate_load_template_path('single-property/map-v2');
}

function opalestate_property_nearby() {
    echo opalestate_load_template_path('single-property/nearby');
}

function opalestate_property_walkscore() {
    echo opalestate_load_template_path('single-property/walkscore');
}

function opalestate_property_apartments() {
    echo opalestate_load_template_path('single-property/apartments');
}

function opalestate_property_floor_plans() {
    echo opalestate_load_template_path('single-property/floor-plans');
}

function opalestate_property_views_statistics() {
    echo opalestate_load_template_path('single-property/views-statistics');
}

function opalestate_property_author() {
    echo opalestate_load_template_path('single-property/author');
}


function opalestate_property_author_v2() {
    echo opalestate_load_template_path('single-property/author-v2');
}

function opalestate_property_author_v3() {
    echo opalestate_load_template_path('single-property/author-v3');
}


function opalestate_property_video() {
    echo opalestate_load_template_path('single-property/video');
}

function opalestate_property_virtual_tour() {
    echo opalestate_load_template_path('single-property/virtualtour');
}

function opalestate_properties_same_agent() {
    echo opalestate_load_template_path('single-property/sameagent');
}

function opalestate_property_location() {
    echo opalestate_load_template_path('single-property/location');
}

/**
 *
 */
add_action('opalestate_single_property_sameagent', 'opalestate_properties_same_agent', 5);

function opalestate_agent_summary() {
    echo opalestate_load_template_path('single-agent/summary');
}

function opalestate_agent_properties() {
    echo opalestate_load_template_path('single-agent/properties');
}

function opalestate_agent_featured_properties() {
    echo opalestate_load_template_path('single-agent/featured-properties');
}

function opalestate_agent_contactform() {
    global $post;
    $args = ['post_id' => $post->ID];
    echo opalestate_load_template_path('single-agent/form', $args);
}

add_action('opalestate_single_agent_summary', 'opalestate_agent_summary', 5);
add_action('opalestate_single_content_agent_after', 'opalestate_agent_properties', 15);
add_action('opalestate_single_content_agent_sidebar', 'opalestate_agent_featured_properties', 16);

/**
 *
 */
function opalestate_agent_navbar() {

}

add_action('opalestate_single_agent_summary', 'opalestate_agent_navbar', 5);

/**
 * Search page:
 */

function opalestate_show_contact_share_search_link() {
    $args = [];
    echo opalestate_load_template_path('user/share-search-form', $args);
}

add_action('opalestate_before_render_search_properties_result', 'opalestate_show_contact_share_search_link');

function opalestate_calculate_distance_geo($lat, $long, $start_lat, $start_long, $dist_measure) {
    $angle    = $start_long - $long;
    $distance = sin(deg2rad($start_lat)) * sin(deg2rad($lat)) + cos(deg2rad($start_lat)) * cos(deg2rad($lat)) * cos(deg2rad($angle));
    $distance = acos($distance);
    $distance = rad2deg($distance);

    if ($dist_measure == 'miles') {
        $distance_miles = $distance * 60 * 1.1515;

        return '(' . round($distance_miles, 2) . ' ' . esc_html__('miles', 'opalestate-pro') . ')';
    } else {
        $distance_miles = $distance * 60 * 1.1515 * 1.6;

        return '(' . round($distance_miles, 2) . ' ' . esc_html__('km', 'opalestate-pro') . ')';
    }
}


function opalestate_get_walkscore_results($latitude, $longitude, $address) {
    $walkscore_api = esc_html(opalestate_get_option('walkscore_api_key', ''));
    if (!$walkscore_api) {
        return null;
    }

    if (!$latitude || !$longitude) {
        return null;
    }

    $w = new Opalestate_WalkScore($walkscore_api);

    $walkscore = $w->WalkScore([
        'lat'     => $latitude,
        'lon'     => $longitude,
        'address' => $address,
        'transit' => 1,
        'bike'    => 1,
    ]);

    if (!isset($walkscore->walkscore) || !$walkscore->walkscore) {
        return null;
    }

    return $walkscore;
}

function opalestate_get_property_walkscore_results($property) {
    if (!$property instanceof Opalestate_Property) {
        return false;
    }

    $map = $property->get_map();

    if (!$map || !is_array($map) || !isset($map['latitude']) || !isset($map['longitude'])) {
        return false;
    }

    $latitude  = $map['latitude'];
    $longitude = $map['longitude'];

    $key = md5($latitude . $longitude);

    if (!$latitude || !$longitude) {
        return false;
    }

    $address = $property->get_address() ? $property->get_address() : '';

    if (false === ($results = get_transient($key))) {
        $results = opalestate_get_walkscore_results($latitude, $longitude, $address);
        set_transient($key, $results, 24 * 7 * HOUR_IN_SECONDS);
    }

    return $results;
}

function opalestate_get_property_walkscore_score($property) {
    $walkscore = opalestate_get_property_walkscore_results($property);

    return $walkscore->walkscore;
}

/**
 * Gets related properties template.
 */
function opalestate_properties_related() {
    if ('on' !== opalestate_get_option('enable_single_related_properties', 'on')) {
        return;
    }

    $num  = opalestate_get_option('single_related_number', 6);
    $args = [
        'post_type'      => 'opalestate_property',
        'posts_per_page' => $num,
        'post__not_in'   => [get_the_ID()],
    ];

    $terms = wp_get_post_terms(get_the_ID(), 'opalestate_types');

    $tax_query = [];
    if ($terms) {
        $tax_query[] = [
            [
                'taxonomy' => 'opalestate_types',
                'field'    => 'slug',
                'terms'    => $terms[0]->slug,
            ],
        ];
    }

    $status = wp_get_post_terms(get_the_ID(), 'opalestate_status');
    if (!is_wp_error($status) && $status) {
        $tax_query[]
            = [
            'taxonomy' => 'opalestate_status',
            'field'    => 'slug',
            'terms'    => $status[0]->slug,

        ];
    }

    if ($tax_query) {
        $args['tax_query'] = ['relation' => 'AND'];
        $args['tax_query'] = array_merge($args['tax_query'], $tax_query);
    }
    $query = Opalestate_Query::get_property_query($args);

    $args = [
        'query'   => $query,
        'column'  => 3,
        'style'   => 'content-property-' . opalestate_get_option('single_related_properties_layout', 'grid'),
        'heading' => esc_html__('Similar Properties You May Like', 'opalestate-pro'),
    ];

    echo opalestate_load_template_path('parts/modules/carousel', $args);
}

/**
 * Gets nearby properties template.
 */
function opalestate_properties_nearby() {
    if ('on' !== opalestate_get_option('enable_single_nearby_properties', 'on')) {
        return;
    }

    global $property;
    $maps = $property->get_map();

    $num     = opalestate_get_option('single_nearby_number', 6);
    $post_id = get_the_ID();

    $args = [
        'post_type'      => 'opalestate_property',
        'posts_per_page' => $num,
    ];

    $geo_lat  = $maps['latitude'];
    $geo_long = $maps['longitude'];

    if (empty($geo_lat) || empty($geo_long)) {
        return;
    }

    $radius       = opalestate_get_option('single_nearby_radius', 5);
    $measure_unit = opalestate_get_option('single_nearby_measure_unit', 'km');
    $post_ids     = Opalestate_Query::filter_by_location($geo_lat, $geo_long, $radius, $measure_unit);

    if (empty($post_ids)) {
        return;
    }

    $args['post__in'] = $post_ids;

    $query = Opalestate_Query::get_property_query($args);

    $args = [
        'query'   => $query,
        'column'  => 3,
        'style'   => 'content-property-' . opalestate_get_option('single_nearby_properties_layout', 'grid'),
        'heading' => esc_html__('New Listings Nearby', 'opalestate-pro'),
    ];

    echo opalestate_load_template_path('parts/modules/carousel', $args);
}

/**
 * Opalestate Date Format - Allows to change date format for everything Opalestate.
 *
 * @return string
 */
function opalestate_date_format() {
    return apply_filters('opalestate_date_format', get_option('date_format'));
}

/**
 * Opalestate Time Format - Allows to change time format for everything Opalestate.
 *
 * @return string
 */
function opalestate_time_format() {
    return apply_filters('opalestate_time_format', get_option('time_format'));
}

/**
 * Get HTML for ratings.
 *
 * @param float $rating Rating being shown.
 * @param int $count Total number of ratings.
 * @return string
 */
function opalestate_get_rating_html($rating, $count = 0) {
    $html = '';

    if (0 < $rating) {
        /* translators: %s: rating */
        $label = sprintf(esc_html__('Rated %s out of 5', 'opalestate-pro'), $rating);
        $html  = '<div class="opalestate-rating" role="img" aria-label="' . esc_attr($label) . '"><div class="opalestate-rating__stars">' . opalestate_get_star_rating_html($rating,
                $count) . '</div></div>';
    }

    return apply_filters('opalestate_property_get_rating_html', $html, $rating, $count);
}

/**
 * Get HTML for star rating.
 *
 * @param float $rating Rating being shown.
 * @param int $count Total number of ratings.
 * @return string
 */
function opalestate_get_star_rating_html($rating, $count = 0) {
    $html = '<span style="width:' . (($rating / 5) * 100) . '%">';

    if (0 < $count) {
        /* translators: 1: rating 2: rating count */
        $html .= sprintf(_n('Rated %1$s out of 5 based on %2$s customer rating', 'Rated %1$s out of 5 based on %2$s customer ratings', $count, 'opalestate-pro'),
            '<strong class="rating">' . esc_html(
                $rating
            ) . '</strong>', '<span class="rating">' . esc_html($count) . '</span>');
    } else {
        /* translators: %s: rating */
        $html .= sprintf(esc_html__('Rated %s out of 5', 'opalestate-pro'), '<strong class="rating">' . esc_html($rating) . '</strong>');
    }

    $html .= '</span>';

    return apply_filters('opalestate_get_star_rating_html', $html, $rating, $count);
}

function opalestate_load_yelp_places() {
    if (!isset($_POST['property_id'])) {
        return;
    }

    $property_id = absint($_POST['property_id']);
    $property    = opalesetate_property($property_id);

    if (!Opalestate_Yelp::get_client_id() || !Opalestate_Yelp::get_app_key()) {
        return;
    }

    $categories = Opalestate_Yelp::get_categories();
    if (!$categories) {
        return;
    }

    $map = $property->get_map();

    $latitude  = $map['latitude'];
    $longitude = $map['longitude'];
    if (!$latitude || !$longitude) {
        return;
    }

    $all_categories    = Opalestate_Yelp::get_all_categories();
    $yelp_dist_measure = opalestate_get_option('yelp_measurement_unit');

    ob_start();
    ?>

    <?php foreach ($categories as $category) : ?>
        <?php
        $yelp    = new Opalestate_Yelp();
        $results = $yelp->get_results($category, $latitude, $longitude);

        if (!$results || !$results->businesses) {
            continue;
        }

        $category_name = $all_categories[$category]['category'];
        $category_icon = $all_categories[$category]['category_sign'];
        ?>
        <div class="opalestate-yelp-bussines_wrapper">
            <div class="opalestate-yelp-title">
                <span class="opalestate-yelp-icon"><i class="<?php echo esc_attr($category_icon); ?>"></i></span>
                <h5 class="opalestate-yelp-category"><?php echo esc_html($category_name); ?></h5>
            </div>

            <?php foreach ($results->businesses as $result) : ?>
                <?php
                $location          = $result->location->display_address;
                $business_address0 = isset($location[0]) ? $location[0] : '';
                $business_address1 = isset($location[1]) ? $location[1] : '';
                $business_address2 = isset($location[2]) ? $location[2] : '';
                $business_address  = $business_address0 . ' ' . $business_address1 . ' ' . $business_address2;

                $business_rating = isset($result->rating) ? $result->rating : 0;
                ?>
                <div class="opalestate-yelp-unit">
                    <?php if (isset($result->image_url) && $result->image_url) : ?>
                        <div class="opalestate-yelp-unit__avatar">
                            <a href="<?php echo esc_url($result->url); ?>" title="<?php echo esc_attr($result->name); ?>" target="_blank">
                                <img src="<?php echo esc_url($result->image_url); ?>" alt="<?php echo esc_attr($result->name); ?>">
                            </a>
                        </div>
                    <?php endif; ?>

                    <div class="opalestate-yelp-unit__info">
                        <h6 class="opalestate-yelp-unit__name">
                            <a href="<?php echo esc_url($result->url); ?>" title="<?php echo esc_attr($result->name); ?>" target="_blank"><?php echo esc_html($result->name); ?></a>
                        </h6>
                        <?php if (isset($result->coordinates->latitude) && isset($result->coordinates->longitude)) : ?>
                            <div class="opalestate-yelp-unit-distance">
                                <?php echo opalestate_calculate_distance_geo(
                                    $result->coordinates->latitude,
                                    $result->coordinates->longitude,
                                    $latitude,
                                    $longitude,
                                    $yelp_dist_measure
                                ); ?>
                            </div>
                        <?php endif; ?>
                        <div class="opalestate-yelp-unit__address"><?php echo esc_html($business_address); ?></div>
                    </div>

                    <div class="opalestate-yelp-unit__ratings">
                        <div class="opalestate-yelp-unit__ratings-wrapper">
                            <?php
                            if ($business_rating) {
                                echo opalestate_get_rating_html($business_rating); // WPCS: XSS ok.
                            }
                            ?>

                            <span class="opalestate-yelp-unit__ratings-count">
                                <?php
                                printf(
                                /* translators: %s number of reviews */
                                    _nx(
                                        '%s review',
                                        '%s reviews',
                                        absint($result->review_count),
                                        'review numbers',
                                        'opalestate-pro'
                                    ),
                                    number_format_i18n(absint($result->review_count))
                                );
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach;
    $response['result'] = ob_get_contents();
    ob_end_clean();

    echo json_encode($response);
    wp_die();
}

add_action('wp_ajax_opalestate_load_yelp_places', 'opalestate_load_yelp_places');
add_action('wp_ajax_nopriv_opalestate_load_yelp_places', 'opalestate_load_yelp_places');

function opalestate_ajax_create_property_print() {
    if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
        exit();
    }

    ob_start();
    get_header();

    echo opalestate_load_template_path('content-single-property-print', ['property_id' => absint($_POST['id'])]);
    get_footer();

    $output = ob_get_contents();
    ob_end_clean();
    echo $output;
    exit();
}

add_action('wp_ajax_opalestate_ajax_create_property_print', 'opalestate_ajax_create_property_print');
add_action('wp_ajax_nopriv_opalestate_ajax_create_property_print', 'opalestate_ajax_create_property_print');

if (!function_exists('opalestate_property_print_button')) {
    function opalestate_property_print_button($id) {
        ?>
        <a href="#" class="js-print-property property-print-button hint--top" aria-label="<?php esc_attr_e('Print', 'opalestate-pro'); ?>" data-id="<?php echo absint($id); ?>">
            <i class="fa fa-print" aria-hidden="true"></i>
            <span class="property-print-button__text"> <?php esc_html_e('Print', 'opalestate-pro'); ?></span>
        </a>
        <?php
    }
}

if (!function_exists('opalestate_property_types_list')) {
    function opalestate_property_types_list() {
        echo opalestate_load_template_path('parts/property-types');
    }
}

if (!function_exists('opalestate_property_categories_list')) {
    function opalestate_property_categories_list() {
        echo opalestate_load_template_path('parts/property-categories');
    }
}
