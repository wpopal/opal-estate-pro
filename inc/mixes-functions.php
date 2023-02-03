<?php
/**
 * Mixes functions.
 *
 * @package    opalestate
 * @author     Opal  Team <info@wpopal.com >
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Output message json.
 *
 * @param bool $result
 * @param string $message
 * @param array $args
 * @param bool $return
 * @return false|mixed|string|void
 */
function opalestate_output_msg_json($result = false, $message = '', $args = [], $return = false) {
    $out          = new stdClass();
    $out->status  = $result;
    $out->message = $message;
    if ($args) {
        foreach ($args as $key => $arg) {
            $out->$key = $arg;
        }
    }
    if ($return) {
        return json_encode($out);
    } else {
        echo json_encode($out);
        die;
    }
}

/**
 * Process upload images for properties
 */
function opalesate_upload_image($submitted_file, $parent_id = 0) {
    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';

    // Handle PHP uploads in WordPress, sanitizing file names, checking extensions for mime type, and moving the
    // file to the appropriate directory within the uploads directory.
    $uploaded_image = wp_handle_upload($submitted_file, ['test_form' => false]);

    if (isset($uploaded_image['file'])) {
        $file_name = basename($submitted_file['name']);
        $file_type = wp_check_filetype($uploaded_image['file']);   //Retrieve the file type from the file name.

        // Prepare an array of post data for the attachment.
        $attachment_details = [
            'guid'           => $uploaded_image['url'],
            'post_mime_type' => $file_type['type'],
            'post_title'     => preg_replace('/\.[^.]+$/', '', basename($file_name)),
            'post_content'   => '',
            'post_status'    => 'inherit',
        ];

        // This function inserts an attachment into the media library.
        $attach_id = wp_insert_attachment($attachment_details, $uploaded_image['file'], $parent_id);

        // This function generates metadata for an image attachment.
        // It also creates a thumbnail and other intermediate sizes of the image attachment based on the sizes defined
        $attach_data = wp_generate_attachment_metadata($attach_id, $uploaded_image['file']);

        // Update metadata for an attachment.
        wp_update_attachment_metadata($attach_id, $attach_data);

        $thumbnail_url = opalestate_get_upload_image_url($attach_data);

        $ajax_response = [
            'success'       => true,
            'url'           => $thumbnail_url,
            'attachment_id' => $attach_id,
        ];

        update_post_meta($attach_id, '_pending_to_use_', 1);

        return $ajax_response;
    }

    return [];
}

/**
 * Upload an image with data base64 encoded.
 *
 * @param array $file File information (data, file_name, type)
 * @return bool|int|\WP_Error
 */
function opalestate_upload_base64_image($file, $parent_id = 0) {
    // Upload dir.
    $img       = str_replace(' ', '+', $file['data']);
    $decoded   = base64_decode($img);
    $filename  = $file['file_name'];
    $file_type = $file['type'];

    /*
     * A writable uploads dir will pass this test. Again, there's no point
     * overriding this one.
     */
    if (!(($uploads = wp_upload_dir()) && false === $uploads['error'])) {
        return false;
    }

    $filename = wp_unique_filename($uploads['path'], $filename);

    // Move the file to the uploads dir.
    $new_file = $uploads['path'] . "/$filename";

    // Save the image in the uploads directory.
    $upload_file = file_put_contents($new_file, $decoded);

    $attachment = [
        'post_mime_type' => $file_type,
        'post_title'     => preg_replace('/\.[^.]+$/', '', basename($file['file_name'])),
        'post_content'   => '',
        'post_status'    => 'inherit',
        'guid'           => $uploads['url'] . '/' . basename($filename),
    ];

    $attach_id = wp_insert_attachment($attachment, $new_file, $parent_id);

    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attach_data = wp_generate_attachment_metadata($attach_id, $new_file);
    wp_update_attachment_metadata($attach_id, $attach_data);

    return $attach_id;
}

function opalestate_get_request_viewing_time_list() {
    $range_time = opalestate_get_option('request_viewing_time_range', 15);

    return apply_filters('opalestate_request_viewing_time_list', opalestate_get_time_lapses($range_time));
}

/**
 * Gets time lapses.
 */
function opalestate_get_time_lapses($lapse = 15) {

    $output   = [];
    $start    = '12:00AM';
    $end      = '11:59PM';
    $interval = '+' . $lapse . ' minutes';

    $start_str = strtotime($start);
    $end_str   = strtotime($end);
    $now_str   = $start_str;

    $time_format_value = 'h:i a';
    $time_format_show  = 'h:i A';

    if ('24_hour' === opalestate_get_option('time_format')) {
        $time_format_value = 'H:i';
        $time_format_show  = 'H:i';
    }

    $time_format_value = apply_filters('opalestate_time_lapse_value', $time_format_value);
    $time_format_show  = apply_filters('opalestate_time_lapse_show', $time_format_show);

    while ($now_str <= $end_str) {
        $output[date($time_format_value, $now_str)] = date($time_format_show, $now_str);
        $now_str                                    = strtotime($interval, $now_str);
    }

    return $output;
}


function is_single_property() {
    global $post;

    $post_type = get_post_type();

    return $post_type == 'opalestate_property' && is_single();
}

function is_single_agent() {
    global $post;

    $post_type = get_post_type();

    return $post_type == 'opalestate_agent' && is_single();
}

function is_single_agency() {
    global $post;

    $post_type = get_post_type();

    return $post_type == 'opalestate_agency' && is_single();
}

/**
 * Gets search form styles.
 *
 * @return array
 */
function opalestate_search_properties_form_styles() {
    return apply_filters('opalestate_search_properties_form_styles', [
        'search-form-h'     => esc_html__('Advanced V1', 'opalestate-pro'),
        'advanced-v2'       => esc_html__('Advanced V2', 'opalestate-pro'),
        'advanced-v3'       => esc_html__('Advanced V3', 'opalestate-pro'),
        'advanced-v4'       => esc_html__('Advanced V4', 'opalestate-pro'),
        'advanced-v5'       => esc_html__('Advanced V5', 'opalestate-pro'),
        'advanced-v6'       => esc_html__('Advanced V6', 'opalestate-pro'),
        'search-form-v'     => esc_html__('Vertical Advanced', 'opalestate-pro'),
        'search-form-v2'    => esc_html__('Vertical Advanced V2', 'opalestate-pro'),
        'search-form-v3'    => esc_html__('Vertical Advanced V3', 'opalestate-pro'),
        'simple-city'       => esc_html__('Simple City', 'opalestate-pro'),
        'simple-keyword'    => esc_html__('Simple Keyword', 'opalestate-pro'),
        'collapse-city'     => esc_html__('Collapse City', 'opalestate-pro'),
        'collapse-keyword'  => esc_html__('Collapse Keyword', 'opalestate-pro'),
        'collapse-advanced' => esc_html__('Collapse Advanced', 'opalestate-pro'),
    ]);
}

/**
 * Gets loop property layouts.
 *
 * @return array
 */
function opalestate_get_loop_property_layouts() {
    return apply_filters('opalestate_get_loop_property_layouts', [
        'grid'       => esc_html__('Grid', 'opalestate-pro'),
        'grid-v2'    => esc_html__('Grid v2', 'opalestate-pro'),
        'grid-v3'    => esc_html__('Grid v3', 'opalestate-pro'),
        'list'       => esc_html__('List', 'opalestate-pro'),
        'list-v2'    => esc_html__('List v2', 'opalestate-pro'),
        'mark-hover' => esc_html__('Mark hover', 'opalestate-pro'),
    ]);
}

/**
 * Gets display modes.
 *
 * @return array
 */
function opalestate_display_modes() {
    return apply_filters('opalestate_display_modes', [
        'grid' => esc_html__('Grid', 'opalestate-pro'),
        'list' => esc_html__('List', 'opalestate-pro'),
    ]);
}

/**
 * Gets loop property grid layouts.
 *
 * @return array
 */
function opalestate_get_loop_property_grid_layouts() {
    return apply_filters('opalestate_get_loop_property_grid_layouts', [
        'grid'       => esc_html__('Grid', 'opalestate-pro'),
        'grid-v2'    => esc_html__('Grid v2', 'opalestate-pro'),
        'grid-v3'    => esc_html__('Grid v3', 'opalestate-pro'),
        'mark-hover' => esc_html__('Mark hover', 'opalestate-pro'),
    ]);
}

/**
 * Gets loop property list layouts.
 *
 * @return array
 */
function opalestate_get_loop_property_list_layouts() {
    return apply_filters('opalestate_get_loop_property_list_layouts', [
        'list'    => esc_html__('List', 'opalestate-pro'),
        'list-v2' => esc_html__('List v2', 'opalestate-pro'),
    ]);
}

/**
 * Gets loop agents grid layouts.
 *
 * @return array
 */
function opalestate_get_loop_agents_grid_layouts() {
    return apply_filters('opalestate_get_loop_agents_grid_layouts', [
        'grid' => esc_html__('Grid', 'opalestate-pro'),
    ]);
}

/**
 * Gets loop agents list layouts.
 *
 * @return array
 */
function opalestate_get_loop_agents_list_layouts() {
    return apply_filters('opalestate_get_loop_agents_list_layouts', [
        'list' => esc_html__('List', 'opalestate-pro'),
    ]);
}

/**
 * Gets loop Agencies grid layouts.
 *
 * @return array
 */
function opalestate_get_loop_agencies_grid_layouts() {
    return apply_filters('opalestate_get_loop_agencies_grid_layouts', [
        'grid' => esc_html__('Grid', 'opalestate-pro'),
    ]);
}

/**
 * Gets loop Agencies list layouts.
 *
 * @return array
 */
function opalestate_get_loop_agencies_list_layouts() {
    return apply_filters('opalestate_get_loop_agencies_list_layouts', [
        'list' => esc_html__('List', 'opalestate-pro'),
    ]);
}

function opalestate_get_map_api_uri() {

    $key = opalestate_options('google_map_api_keys') ?
        opalestate_options('google_map_api_keys') : 'AIzaSyAvJkbm23fhVAYcbdeVB0nkHjZmDeZ62bc';

    $api = 'https://maps.googleapis.com/maps/api/js?key=' . $key . '&libraries=geometry,places,drawing&ver=5.2.2&callback=Function.prototype';
    $api = apply_filters('opalestate_google_map_api_uri', $api);

    return $api;
}

function opalestate_get_map_search_api_uri($address) {

    $key = opalestate_options('google_map_api_keys') ?
        opalestate_options('google_map_api_keys') : 'AIzaSyAvJkbm23fhVAYcbdeVB0nkHjZmDeZ62bc';

    $api = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key={$key}";

    return $api;
}

function opalestate_is_setting_enabled($value, $compare_with = null) {
    if (!is_null($compare_with)) {

        if (is_array($compare_with)) {
            // Output.
            return in_array($value, $compare_with);
        }

        // Output.
        return ($value === $compare_with);
    }

    return (in_array($value, ['enabled', 'on', 'yes']) ? true : false);
}


function opalestate_get_dashdoard_page_uri() {
    return home_url();
}


if (!function_exists('opalestate_is_ajax_request')) {
    function opalestate_is_ajax_request() {
        return defined('DOING_AJAX') && DOING_AJAX;
    }
}

if (!function_exists('opalmembership_add_notice')) {
    function opalestate_add_notice($type = 'error', $message = '') {
        if (!$type || !$message) {
            return;
        }
        $notices = OpalEstate()->session->get('notices', []);
        if (!isset($notices[$type])) {
            $notices[$type] = [];
        }
        $notices[$type][] = $message;
        OpalEstate()->session->set('notices', $notices);
    }
}


if (!function_exists('opalestate_print_notices')) {

    /**
     * print all notices
     */
    function opalestate_print_notices() {

        $notices = OpalEstate()->session->get('notices', []);
        if (empty($notices)) {
            return;
        }
        ob_start();
        foreach ($notices as $type => $messages) {
            echo opalestate_load_template_path('notices/' . $type, ['messages' => $messages]);
        }
        OpalEstate()->session->set('notices', []);
        echo ob_get_clean();
    }
}


function opalestate_user_roles_by_user_id($user_id) {
    $user = get_userdata($user_id);

    return empty($user) ? [] : $user->roles;
}

/**
 * Featured Image Sizes
 */
function opalestate_get_featured_image_sizes() {
    global $_wp_additional_image_sizes;
    $sizes = ['full' => esc_html__('Orginal Size', 'opalestate-pro')];

    foreach (get_intermediate_image_sizes() as $_size) {

        if (in_array($_size, ['thumbnail', 'medium', 'medium_large', 'large'])) {
            $sizes[$_size] = $_size . ' - ' . get_option("{$_size}_size_w") . 'x' . get_option("{$_size}_size_h");
        } elseif (isset($_wp_additional_image_sizes[$_size])) {
            $sizes[$_size] = $_size . ' - ' . $_wp_additional_image_sizes[$_size]['width'] . 'x' . $_wp_additional_image_sizes[$_size]['height'];
        }

    }

    return apply_filters('opalestate_get_featured_image_sizes', $sizes);
}

/**
 * Gets register page uri.
 *
 * @return string
 */
function opalestate_get_register_page_uri() {
    global $opalmembership_options;
    $register_page = isset($opalmembership_options['register_page']) ? get_permalink(absint($opalmembership_options['register_page'])) : get_bloginfo('url');

    return apply_filters('opalestate_get_register_page_uri', $register_page);
}

/**
 * Gets search agency uri.
 *
 * @return false|string
 */
function opalestate_search_agency_uri() {
    return get_post_type_archive_link('opalestate_agency');
}

/**
 * Gets current uri.
 *
 * @return string
 */
function opalestate_get_current_uri() {
    global $wp;

    $current_url = home_url(add_query_arg([], $wp->request));

    return $current_url;
}

/**
 * Gets URL sort mode.
 *
 * @param string $mode
 * @return string
 */
function opalestate_get_url_sort_mode($mode = "") {
    global $wp;

    $get = [];

    if (isset($_GET)) {
        $get = $_GET;
    }
    $get['display'] = $mode;
    $current_url    = home_url(add_query_arg([$get], $wp->request));

    return $current_url;
}

/**
 *
 */
function opalestate_search_agent_uri() {
    global $opalestate_options;

    $search_agents = isset($opalestate_options['search_agents']) ? get_permalink(absint($opalestate_options['search_agents'])) : opalestate_get_current_uri();

    return apply_filters('opalestate_get_search_agents_page_uri', $search_agents);
}

/**
 *
 */
function opalestate_get_session_location_val() {
    return isset($_SESSION['set_location']) ? $_SESSION['set_location'] : 0;
}

function opalestate_get_location_active() {
    $location = opalestate_get_session_location_val();
    if (!is_numeric($location)) {
        $term = get_term_by('slug', $location, 'opalestate_location');
        $name = is_object($term) ? $term->name : esc_html__('Your Location', 'opalestate-pro');

        return $name;
    } else {
        return esc_html__('Your Location', 'opalestate-pro');
    }
}

function opalestate_get_upload_image_url($attach_data) {
    $upload_dir       = wp_upload_dir();
    $image_path_array = explode('/', $attach_data['file']);
    $image_path_array = array_slice($image_path_array, 0, count($image_path_array) - 1);
    $image_path       = implode('/', $image_path_array);
    $thumbnail_name   = null;
    if (isset($attach_data['sizes']['user-image'])) {
        $thumbnail_name = $attach_data['sizes']['user-image']['file'];
    } elseif (isset($attach_data['sizes']['thumbnail']['file'])) {
        $thumbnail_name = $attach_data['sizes']['thumbnail']['file'];
    } else {
        return $upload_dir['baseurl'] . '/' . $attach_data['file'];
    }

    return $upload_dir['baseurl'] . '/' . $image_path . '/' . $thumbnail_name;
}

function opalestate_update_attachement_used($attachment_id) {

}

/**
 * batch including all files in a path.
 *
 * @param String $path : PATH_DIR/*.php or PATH_DIR with $ifiles not empty
 */
function opalestate_includes($path, $ifiles = []) {
    if (!empty($ifiles)) {
        foreach ($ifiles as $key => $file) {
            $file = $path . '/' . $file;
            if (is_file($file)) {
                require($file);
            }
        }
    } else {
        $files = glob($path);
        foreach ($files as $key => $file) {
            if (is_file($file)) {
                require($file);
            }
        }
    }
}

/**
 * Gets property.
 *
 * @param $id
 * @return \Opalestate_Property
 */
function opalesetate_property($id) {
    global $property;

    $property = new Opalestate_Property($id);

    return $property;
}

/**
 * Gets agency
 *
 * @param $id
 * @return \Opalestate_Agency
 */
function opalesetate_agency($id) {
    global $agency;

    $agency = new Opalestate_Agency($id);

    return $agency;
}

/**
 * Gets agent.
 *
 * @param $id
 * @return \Opalestate_Agent
 */
function opalesetate_agent($id) {
    global $agent;

    $agent = new Opalestate_Agent($id);

    return $agent;
}

/**
 * Gets opalestate options.
 *
 * @param string $key
 * @param string $default
 * @return mixed|void
 */
function opalestate_options($key, $default = '') {
    global $opalestate_options;

    $value = isset($opalestate_options[$key]) ? $opalestate_options[$key] : $default;
    $value = apply_filters('opalestate_option_', $value, $key, $default);

    return apply_filters('opalestate_option_' . $key, $value, $key, $default);
}

/**
 * Gets image placeholder.
 *
 * @param string $size
 * @param bool $url
 * @return string
 */
function opalestate_get_image_placeholder($size = '', $url = false) {
    $src = opalestate_get_image_placeholder_src();

    $size = is_string($size) ? $size : '';

    if ($url) {
        return esc_url($src);
    }

    return '<img src="' . esc_url($src) . '" alt="' . sprintf(esc_html__('Placeholder %s', 'opalestate-pro'), $size) . '" />';
}

/**
 * Get image placeholder src.
 *
 * @return string
 */
function opalestate_get_image_placeholder_src() {
    return apply_filters('opalestate_get_image_placeholder_src', OPALESTATE_PLUGIN_URL . 'assets/images/placeholder.png');
}

/**
 * Get User IP
 *
 * Returns the IP address of the current visitor
 *
 * @return string $ip User's IP address
 */
function opalestate_get_ip() {
    $ip = '127.0.0.1';

    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        //check ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        //to check ip is pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    return apply_filters('opalestate_get_ip', $ip);
}

/**
 * Gets currencies.
 *
 * @return array
 */
function opalestate_get_currencies() {
    $currencies = [
        'AED' => esc_html__('United Arab Emirates dirham', 'opalestate-pro'),
        'AFN' => esc_html__('Afghan afghani', 'opalestate-pro'),
        'ALL' => esc_html__('Albanian lek', 'opalestate-pro'),
        'AMD' => esc_html__('Armenian dram', 'opalestate-pro'),
        'ANG' => esc_html__('Netherlands Antillean guilder', 'opalestate-pro'),
        'AOA' => esc_html__('Angolan kwanza', 'opalestate-pro'),
        'ARS' => esc_html__('Argentine peso', 'opalestate-pro'),
        'AUD' => esc_html__('Australian dollar', 'opalestate-pro'),
        'AWG' => esc_html__('Aruban florin', 'opalestate-pro'),
        'AZN' => esc_html__('Azerbaijani manat', 'opalestate-pro'),
        'BAM' => esc_html__('Bosnia and Herzegovina convertible mark', 'opalestate-pro'),
        'BBD' => esc_html__('Barbadian dollar', 'opalestate-pro'),
        'BDT' => esc_html__('Bangladeshi taka', 'opalestate-pro'),
        'BGN' => esc_html__('Bulgarian lev', 'opalestate-pro'),
        'BHD' => esc_html__('Bahraini dinar', 'opalestate-pro'),
        'BIF' => esc_html__('Burundian franc', 'opalestate-pro'),
        'BMD' => esc_html__('Bermudian dollar', 'opalestate-pro'),
        'BND' => esc_html__('Brunei dollar', 'opalestate-pro'),
        'BOB' => esc_html__('Bolivian boliviano', 'opalestate-pro'),
        'BRL' => esc_html__('Brazilian real', 'opalestate-pro'),
        'BSD' => esc_html__('Bahamian dollar', 'opalestate-pro'),
        'BTC' => esc_html__('Bitcoin', 'opalestate-pro'),
        'BTN' => esc_html__('Bhutanese ngultrum', 'opalestate-pro'),
        'BWP' => esc_html__('Botswana pula', 'opalestate-pro'),
        'BYR' => esc_html__('Belarusian ruble', 'opalestate-pro'),
        'BZD' => esc_html__('Belize dollar', 'opalestate-pro'),
        'CAD' => esc_html__('Canadian dollar', 'opalestate-pro'),
        'CDF' => esc_html__('Congolese franc', 'opalestate-pro'),
        'CHF' => esc_html__('Swiss franc', 'opalestate-pro'),
        'CLP' => esc_html__('Chilean peso', 'opalestate-pro'),
        'CNY' => esc_html__('Chinese yuan', 'opalestate-pro'),
        'UF'  => esc_html__('Chilean Foment Unity', 'opalestate-pro'),
        'COP' => esc_html__('Colombian peso', 'opalestate-pro'),
        'CRC' => esc_html__('Costa Rican col&oacute;n', 'opalestate-pro'),
        'CUC' => esc_html__('Cuban convertible peso', 'opalestate-pro'),
        'CUP' => esc_html__('Cuban peso', 'opalestate-pro'),
        'CVE' => esc_html__('Cape Verdean escudo', 'opalestate-pro'),
        'CZK' => esc_html__('Czech koruna', 'opalestate-pro'),
        'DJF' => esc_html__('Djiboutian franc', 'opalestate-pro'),
        'DKK' => esc_html__('Danish krone', 'opalestate-pro'),
        'DOP' => esc_html__('Dominican peso', 'opalestate-pro'),
        'DZD' => esc_html__('Algerian dinar', 'opalestate-pro'),
        'EGP' => esc_html__('Egyptian pound', 'opalestate-pro'),
        'ERN' => esc_html__('Eritrean nakfa', 'opalestate-pro'),
        'ETB' => esc_html__('Ethiopian birr', 'opalestate-pro'),
        'EUR' => esc_html__('Euro', 'opalestate-pro'),
        'FJD' => esc_html__('Fijian dollar', 'opalestate-pro'),
        'FKP' => esc_html__('Falkland Islands pound', 'opalestate-pro'),
        'GBP' => esc_html__('Pound sterling', 'opalestate-pro'),
        'GEL' => esc_html__('Georgian lari', 'opalestate-pro'),
        'GGP' => esc_html__('Guernsey pound', 'opalestate-pro'),
        'GHS' => esc_html__('Ghana cedi', 'opalestate-pro'),
        'GIP' => esc_html__('Gibraltar pound', 'opalestate-pro'),
        'GMD' => esc_html__('Gambian dalasi', 'opalestate-pro'),
        'GNF' => esc_html__('Guinean franc', 'opalestate-pro'),
        'GTQ' => esc_html__('Guatemalan quetzal', 'opalestate-pro'),
        'GYD' => esc_html__('Guyanese dollar', 'opalestate-pro'),
        'HKD' => esc_html__('Hong Kong dollar', 'opalestate-pro'),
        'HNL' => esc_html__('Honduran lempira', 'opalestate-pro'),
        'HRK' => esc_html__('Croatian kuna', 'opalestate-pro'),
        'HTG' => esc_html__('Haitian gourde', 'opalestate-pro'),
        'HUF' => esc_html__('Hungarian forint', 'opalestate-pro'),
        'IDR' => esc_html__('Indonesian rupiah', 'opalestate-pro'),
        'ILS' => esc_html__('Israeli new shekel', 'opalestate-pro'),
        'IMP' => esc_html__('Manx pound', 'opalestate-pro'),
        'INR' => esc_html__('Indian rupee', 'opalestate-pro'),
        'IQD' => esc_html__('Iraqi dinar', 'opalestate-pro'),
        'IRR' => esc_html__('Iranian rial', 'opalestate-pro'),
        'ISK' => esc_html__('Icelandic kr&oacute;na', 'opalestate-pro'),
        'JEP' => esc_html__('Jersey pound', 'opalestate-pro'),
        'JMD' => esc_html__('Jamaican dollar', 'opalestate-pro'),
        'JOD' => esc_html__('Jordanian dinar', 'opalestate-pro'),
        'JPY' => esc_html__('Japanese yen', 'opalestate-pro'),
        'KES' => esc_html__('Kenyan shilling', 'opalestate-pro'),
        'KGS' => esc_html__('Kyrgyzstani som', 'opalestate-pro'),
        'KHR' => esc_html__('Cambodian riel', 'opalestate-pro'),
        'KMF' => esc_html__('Comorian franc', 'opalestate-pro'),
        'KPW' => esc_html__('North Korean won', 'opalestate-pro'),
        'KRW' => esc_html__('South Korean won', 'opalestate-pro'),
        'KWD' => esc_html__('Kuwaiti dinar', 'opalestate-pro'),
        'KYD' => esc_html__('Cayman Islands dollar', 'opalestate-pro'),
        'KZT' => esc_html__('Kazakhstani tenge', 'opalestate-pro'),
        'LAK' => esc_html__('Lao kip', 'opalestate-pro'),
        'LBP' => esc_html__('Lebanese pound', 'opalestate-pro'),
        'LKR' => esc_html__('Sri Lankan rupee', 'opalestate-pro'),
        'LRD' => esc_html__('Liberian dollar', 'opalestate-pro'),
        'LSL' => esc_html__('Lesotho loti', 'opalestate-pro'),
        'LYD' => esc_html__('Libyan dinar', 'opalestate-pro'),
        'MAD' => esc_html__('Moroccan dirham', 'opalestate-pro'),
        'MDL' => esc_html__('Moldovan leu', 'opalestate-pro'),
        'MGA' => esc_html__('Malagasy ariary', 'opalestate-pro'),
        'MKD' => esc_html__('Macedonian denar', 'opalestate-pro'),
        'MMK' => esc_html__('Burmese kyat', 'opalestate-pro'),
        'MNT' => esc_html__('Mongolian t&ouml;gr&ouml;g', 'opalestate-pro'),
        'MOP' => esc_html__('Macanese pataca', 'opalestate-pro'),
        'MRO' => esc_html__('Mauritanian ouguiya', 'opalestate-pro'),
        'MUR' => esc_html__('Mauritian rupee', 'opalestate-pro'),
        'MVR' => esc_html__('Maldivian rufiyaa', 'opalestate-pro'),
        'MWK' => esc_html__('Malawian kwacha', 'opalestate-pro'),
        'MXN' => esc_html__('Mexican peso', 'opalestate-pro'),
        'MYR' => esc_html__('Malaysian ringgit', 'opalestate-pro'),
        'MZN' => esc_html__('Mozambican metical', 'opalestate-pro'),
        'NAD' => esc_html__('Namibian dollar', 'opalestate-pro'),
        'NGN' => esc_html__('Nigerian naira', 'opalestate-pro'),
        'NIO' => esc_html__('Nicaraguan c&oacute;rdoba', 'opalestate-pro'),
        'NOK' => esc_html__('Norwegian krone', 'opalestate-pro'),
        'NPR' => esc_html__('Nepalese rupee', 'opalestate-pro'),
        'NZD' => esc_html__('New Zealand dollar', 'opalestate-pro'),
        'OMR' => esc_html__('Omani rial', 'opalestate-pro'),
        'PAB' => esc_html__('Panamanian balboa', 'opalestate-pro'),
        'PEN' => esc_html__('Peruvian nuevo sol', 'opalestate-pro'),
        'PGK' => esc_html__('Papua New Guinean kina', 'opalestate-pro'),
        'PHP' => esc_html__('Philippine peso', 'opalestate-pro'),
        'PKR' => esc_html__('Pakistani rupee', 'opalestate-pro'),
        'PLN' => esc_html__('Polish z&#x142;oty', 'opalestate-pro'),
        'PRB' => esc_html__('Transnistrian ruble', 'opalestate-pro'),
        'PYG' => esc_html__('Paraguayan guaran&iacute;', 'opalestate-pro'),
        'QAR' => esc_html__('Qatari riyal', 'opalestate-pro'),
        'RON' => esc_html__('Romanian leu', 'opalestate-pro'),
        'RSD' => esc_html__('Serbian dinar', 'opalestate-pro'),
        'RUB' => esc_html__('Russian ruble', 'opalestate-pro'),
        'RWF' => esc_html__('Rwandan franc', 'opalestate-pro'),
        'SAR' => esc_html__('Saudi riyal', 'opalestate-pro'),
        'SBD' => esc_html__('Solomon Islands dollar', 'opalestate-pro'),
        'SCR' => esc_html__('Seychellois rupee', 'opalestate-pro'),
        'SDG' => esc_html__('Sudanese pound', 'opalestate-pro'),
        'SEK' => esc_html__('Swedish krona', 'opalestate-pro'),
        'SGD' => esc_html__('Singapore dollar', 'opalestate-pro'),
        'SHP' => esc_html__('Saint Helena pound', 'opalestate-pro'),
        'SLL' => esc_html__('Sierra Leonean leone', 'opalestate-pro'),
        'SOS' => esc_html__('Somali shilling', 'opalestate-pro'),
        'SRD' => esc_html__('Surinamese dollar', 'opalestate-pro'),
        'SSP' => esc_html__('South Sudanese pound', 'opalestate-pro'),
        'STD' => esc_html__('S&atilde;o Tom&eacute; and Pr&iacute;ncipe dobra', 'opalestate-pro'),
        'SYP' => esc_html__('Syrian pound', 'opalestate-pro'),
        'SZL' => esc_html__('Swazi lilangeni', 'opalestate-pro'),
        'THB' => esc_html__('Thai baht', 'opalestate-pro'),
        'TJS' => esc_html__('Tajikistani somoni', 'opalestate-pro'),
        'TMT' => esc_html__('Turkmenistan manat', 'opalestate-pro'),
        'TND' => esc_html__('Tunisian dinar', 'opalestate-pro'),
        'TOP' => esc_html__('Tongan pa&#x2bb;anga', 'opalestate-pro'),
        'TRY' => esc_html__('Turkish lira', 'opalestate-pro'),
        'TTD' => esc_html__('Trinidad and Tobago dollar', 'opalestate-pro'),
        'TWD' => esc_html__('New Taiwan dollar', 'opalestate-pro'),
        'TZS' => esc_html__('Tanzanian shilling', 'opalestate-pro'),
        'UAH' => esc_html__('Ukrainian hryvnia', 'opalestate-pro'),
        'UGX' => esc_html__('Ugandan shilling', 'opalestate-pro'),
        'USD' => esc_html__('United States dollar', 'opalestate-pro'),
        'UYU' => esc_html__('Uruguayan peso', 'opalestate-pro'),
        'UZS' => esc_html__('Uzbekistani som', 'opalestate-pro'),
        'VEF' => esc_html__('Venezuelan bol&iacute;var', 'opalestate-pro'),
        'VND' => esc_html__('Vietnamese &#x111;&#x1ed3;ng', 'opalestate-pro'),
        'VUV' => esc_html__('Vanuatu vatu', 'opalestate-pro'),
        'WST' => esc_html__('Samoan t&#x101;l&#x101;', 'opalestate-pro'),
        'XAF' => esc_html__('Central African CFA franc', 'opalestate-pro'),
        'XCD' => esc_html__('East Caribbean dollar', 'opalestate-pro'),
        'XOF' => esc_html__('West African CFA franc', 'opalestate-pro'),
        'XPF' => esc_html__('CFP franc', 'opalestate-pro'),
        'YER' => esc_html__('Yemeni rial', 'opalestate-pro'),
        'ZAR' => esc_html__('South African rand', 'opalestate-pro'),
        'ZMW' => esc_html__('Zambian kwacha', 'opalestate-pro'),
    ];

    return apply_filters('opalestate_currencies', $currencies);
}

/**
 * Get the price format depending on the currency position
 *
 * @return string
 */
function opalestate_price_format_position() {
    global $opalestate_options;
    $currency_pos = opalestate_options('currency_position', 'before');

    $format = '%1$s%2$s';
    switch ($currency_pos) {
        case 'before' :
            $format = '%1$s%2$s';
            break;
        case 'after' :
            $format = '%2$s%1$s';
            break;
        case 'left_space' :
            $format = '%1$s&nbsp;%2$s';
            break;
        case 'right_space' :
            $format = '%2$s&nbsp;%1$s';
            break;
    }

    return apply_filters('opalestate_price_format_position', $format, $currency_pos);
}

/**
 * Price format.
 *
 * @param string $price
 * @param array $args
 * @return mixed|void
 */
function opalestate_price_format($price, $args = []) {

    $price = opalestate_price($price, $args);
    $price = sprintf(opalestate_price_format_position(), opalestate_currency_symbol(), $price);

    return apply_filters('opalestate_price_format', $price);
}

function opalestate_get_currency() {
    return opalestate_options('currency', 'USD');
}

/**
 * Gets currency symbol
 *
 * @param string $currency
 * @return string
 */
function opalestate_currency_symbol($currency = '') {
    if (!$currency) {
        $currency = opalestate_get_currency();
    }

    $symbols = apply_filters('opalestate_currency_symbols', [
        'AED' => '&#x62f;.&#x625;',
        'AFN' => '&#x60b;',
        'ALL' => 'L',
        'AMD' => 'AMD',
        'ANG' => '&fnof;',
        'AOA' => 'Kz',
        'ARS' => '&#36;',
        'AUD' => '&#36;',
        'AWG' => '&fnof;',
        'AZN' => 'AZN',
        'BAM' => 'KM',
        'BBD' => '&#36;',
        'BDT' => '&#2547;&nbsp;',
        'BGN' => '&#1083;&#1074;.',
        'BHD' => '.&#x62f;.&#x628;',
        'BIF' => 'Fr',
        'BMD' => '&#36;',
        'BND' => '&#36;',
        'BOB' => 'Bs.',
        'BRL' => '&#82;&#36;',
        'BSD' => '&#36;',
        'BTC' => '&#3647;',
        'BTN' => 'Nu.',
        'BWP' => 'P',
        'BYR' => 'Br',
        'BZD' => '&#36;',
        'CAD' => '&#36;',
        'CDF' => 'Fr',
        'CHF' => '&#67;&#72;&#70;',
        'CLP' => '&#36;',
        'CNY' => '&yen;',
        'UF'  => 'UF',
        'COP' => '&#36;',
        'CRC' => '&#x20a1;',
        'CUC' => '&#36;',
        'CUP' => '&#36;',
        'CVE' => '&#36;',
        'CZK' => '&#75;&#269;',
        'DJF' => 'Fr',
        'DKK' => 'DKK',
        'DOP' => 'RD&#36;',
        'DZD' => '&#x62f;.&#x62c;',
        'EGP' => 'EGP',
        'ERN' => 'Nfk',
        'ETB' => 'Br',
        'EUR' => '&euro;',
        'FJD' => '&#36;',
        'FKP' => '&pound;',
        'GBP' => '&pound;',
        'GEL' => '&#x10da;',
        'GGP' => '&pound;',
        'GHS' => '&#x20b5;',
        'GIP' => '&pound;',
        'GMD' => 'D',
        'GNF' => 'Fr',
        'GTQ' => 'Q',
        'GYD' => '&#36;',
        'HKD' => '&#36;',
        'HNL' => 'L',
        'HRK' => 'Kn',
        'HTG' => 'G',
        'HUF' => '&#70;&#116;',
        'IDR' => 'Rp',
        'ILS' => '&#8362;',
        'IMP' => '&pound;',
        'INR' => '&#8377;',
        'IQD' => '&#x639;.&#x62f;',
        'IRR' => '&#xfdfc;',
        'ISK' => 'Kr.',
        'JEP' => '&pound;',
        'JMD' => '&#36;',
        'JOD' => '&#x62f;.&#x627;',
        'JPY' => '&yen;',
        'KES' => 'KSh',
        'KGS' => '&#x43b;&#x432;',
        'KHR' => '&#x17db;',
        'KMF' => 'Fr',
        'KPW' => '&#x20a9;',
        'KRW' => '&#8361;',
        'KWD' => '&#x62f;.&#x643;',
        'KYD' => '&#36;',
        'KZT' => 'KZT',
        'LAK' => '&#8365;',
        'LBP' => '&#x644;.&#x644;',
        'LKR' => '&#xdbb;&#xdd4;',
        'LRD' => '&#36;',
        'LSL' => 'L',
        'LYD' => '&#x644;.&#x62f;',
        'MAD' => '&#x62f;.&#x645;.',
        'MDL' => 'L',
        'MGA' => 'Ar',
        'MKD' => '&#x434;&#x435;&#x43d;',
        'MMK' => 'Ks',
        'MNT' => '&#x20ae;',
        'MOP' => 'P',
        'MRO' => 'UM',
        'MUR' => '&#x20a8;',
        'MVR' => '.&#x783;',
        'MWK' => 'MK',
        'MXN' => '&#36;',
        'MYR' => '&#82;&#77;',
        'MZN' => 'MT',
        'NAD' => '&#36;',
        'NGN' => '&#8358;',
        'NIO' => 'C&#36;',
        'NOK' => '&#107;&#114;',
        'NPR' => '&#8360;',
        'NZD' => '&#36;',
        'OMR' => '&#x631;.&#x639;.',
        'PAB' => 'B/.',
        'PEN' => 'S/.',
        'PGK' => 'K',
        'PHP' => '&#8369;',
        'PKR' => '&#8360;',
        'PLN' => '&#122;&#322;',
        'PRB' => '&#x440;.',
        'PYG' => '&#8370;',
        'QAR' => '&#x631;.&#x642;',
        'RMB' => '&yen;',
        'RON' => 'lei',
        'RSD' => '&#x434;&#x438;&#x43d;.',
        'RUB' => '&#8381;',
        'RWF' => 'Fr',
        'SAR' => '&#x631;.&#x633;',
        'SBD' => '&#36;',
        'SCR' => '&#x20a8;',
        'SDG' => '&#x62c;.&#x633;.',
        'SEK' => '&#107;&#114;',
        'SGD' => '&#36;',
        'SHP' => '&pound;',
        'SLL' => 'Le',
        'SOS' => 'Sh',
        'SRD' => '&#36;',
        'SSP' => '&pound;',
        'STD' => 'Db',
        'SYP' => '&#x644;.&#x633;',
        'SZL' => 'L',
        'THB' => '&#3647;',
        'TJS' => '&#x405;&#x41c;',
        'TMT' => 'm',
        'TND' => '&#x62f;.&#x62a;',
        'TOP' => 'T&#36;',
        'TRY' => '&#8378;',
        'TTD' => '&#36;',
        'TWD' => '&#78;&#84;&#36;',
        'TZS' => 'Sh',
        'UAH' => '&#8372;',
        'UGX' => 'UGX',
        'USD' => '&#36;',
        'UYU' => '&#36;',
        'UZS' => 'UZS',
        'VEF' => 'Bs F',
        'VND' => '&#8363;',
        'VUV' => 'Vt',
        'WST' => 'T',
        'XAF' => 'Fr',
        'XCD' => '&#36;',
        'XOF' => 'Fr',
        'XPF' => 'Fr',
        'YER' => '&#xfdfc;',
        'ZAR' => '&#82;',
        'ZMW' => 'ZK',
    ]);

    $currency_symbol = isset($symbols[$currency]) ? $symbols[$currency] : '';

    return apply_filters('opalestate_currency_symbol', $currency_symbol, $currency);
}

/**
 * Return the thousand separator for prices
 *
 * @return string
 */
function opalestate_get_price_thousand_separator() {
    $separator = stripslashes(opalestate_options('thousands_separator'));

    return $separator;
}

/**
 * Return the decimal separator for prices
 *
 * @return string
 */
function opalestate_get_price_decimal_separator() {
    $separator = stripslashes(opalestate_options('decimal_separator', '.'));

    return $separator ? $separator : '.';
}

/**
 * Return the number of decimals after the decimal point.
 *
 * @return int
 */
function opalestate_get_price_decimals() {
    return absint(opalestate_options('number_decimals', 2));
}


/**
 * Returns Price.
 *
 * @param int $price
 * @param array $args
 * @return bool|mixed|string|void
 */
function opalestate_price($price, $args = []) {

    $negative = $price < 0;

    if ($negative) {
        $price = substr($price, 1);
    }


    extract(apply_filters('opalestate_price_args', wp_parse_args($args, [
        'ex_tax_label'       => false,
        'decimal_separator'  => opalestate_get_price_decimal_separator(),
        'thousand_separator' => opalestate_get_price_thousand_separator(),
        'decimals'           => opalestate_get_price_decimals(),

    ])));

    $negative = $price < 0;
    $price    = apply_filters('opalestate_raw_price', floatval($negative ? $price * -1 : $price));
    $price    = apply_filters('opalestate_formatted_price', number_format($price, $decimals, $decimal_separator, $thousand_separator), $price, $decimals, $decimal_separator, $thousand_separator);

    return $price;
}

/**
 *
 *  Applyer function to show unit for property
 */

function opalestate_areasize_unit_format($value = '') {
    $measurement_units = opalestate_get_measurement_units();
    $unit              = opalestate_options('measurement_unit', 'sqft');
    if (isset($measurement_units[$unit])) {
        $unit = $measurement_units[$unit];
    }

    return sprintf(_x('%1$s <span>%2$s</span>', 'areasize info', 'opalestate-pro'), $value, $unit);
}

add_filter('opalestate_areasize_unit_format', 'opalestate_areasize_unit_format');

/**
 *
 *  Applyer function to show unit for property
 */
if (!function_exists('opalestate_fnc_excerpt')) {
    //Custom Excerpt Function
    function opalestate_fnc_excerpt($limit, $afterlimit = '[...]') {
        $excerpt = get_the_excerpt();

        return opalestate_fnc_get_words($excerpt, $limit, $afterlimit);
    }
}

function opalestate_fnc_get_words($excerpt, $limit, $afterlimit = '...') {
    if ($excerpt != '') {
        $excerpt = explode(' ', strip_tags($excerpt), $limit);
    } else {
        $excerpt = explode(' ', strip_tags(get_the_content()), $limit);
    }
    if (count($excerpt) >= $limit) {
        array_pop($excerpt);
        $excerpt = implode(" ", $excerpt) . ' ' . $afterlimit;
    } else {
        $excerpt = implode(" ", $excerpt);
    }
    $excerpt = preg_replace('`[[^]]*]`', '', $excerpt);

    return strip_shortcodes($excerpt);
}

/**
 *
 */
function opalestate_is_own_property($post_id, $user_id) {
    $post = get_post($post_id);
    wp_reset_postdata();
    if (!is_object($post) || !$post->ID) {
        return false;
    }

    return $user_id == $post->post_author;
}


if (!function_exists('opalesate_insert_user_agent')) {

    function opalesate_insert_user_agent($args = []) {
        $userdata = wp_parse_args($args, [
            'first_name' => '',
            'last_name'  => '',
            'avatar'     => '',
            'job'        => '',
            'email'      => '',
            'phone'      => '',
            'mobile'     => '',
            'fax'        => '',
            'web'        => '',
            'address'    => '',
            'twitter'    => '',
            'facebook'   => '',
            'google'     => '',
            'linkedin'   => '',
            'instagram'  => '',
        ]);

        $agent_id = wp_insert_post([
            'post_title'   => $args['first_name'] && $args['last_name'] ? $args['first_name'] . ' ' . $args['last_name'] : $userdata['email'],
            'post_content' => 'empty description',
            'post_excerpt' => 'empty excerpt',
            'post_type'    => 'opalestate_agent',
            'post_status'  => 'publish',
            'post_author'  => 1,
        ], true);

        foreach ($userdata as $key => $value) {
            if (in_array($key, ['first_name', 'last_name'])) {
                continue;
            }
            update_post_meta($agent_id, OPALESTATE_AGENT_PREFIX . $key, $value);
        }
        do_action('opalesate_insert_user_agent', $agent_id);

        return $agent_id;
    }
}

/**
 * Returns the multilingual instance.
 *
 * @return Opalestate_Multilingual
 */
function opalestate_multilingual() {
    $multilingual = new Opalestate_Multilingual();

    return $multilingual;
}

/**
 * Is current WordPress is running on multi-languages.
 *
 * @return bool
 */
function opalestate_running_on_multilanguage() {
    return apply_filters('opalestate_is_running_multilanguage', Opalestate_Multilingual::is_polylang() || Opalestate_Multilingual::is_wpml());
}

/**
 * Gets measurement units.
 *
 * @return array
 */
function opalestate_get_measurement_units() {
    return apply_filters('opalestate_measurement_units', [
        'sqft' => esc_html__('sq ft', 'opalestate-pro'),
        'sqm'  => esc_html__('sq m', 'opalestate-pro'),
        'mq'   => esc_html__('mq', 'opalestate-pro'),
        'm2'   => esc_html__('m2', 'opalestate-pro'),
    ]);
}

/**
 * Gets time formats.
 *
 * @return array
 */
function opalestate_get_time_formats() {
    return apply_filters('opalestate_time_formats', [
        '12_hour' => esc_html__('12-hour', 'opalestate-pro'),
        '24_hour' => esc_html__('24-hour', 'opalestate-pro'),
    ]);
}

/**
 * Returns property statuses.
 *
 * @return array
 */
function opalestate_get_property_statuses() {
    return apply_filters('opalestate_get_property_statuses', [
        'all'       => esc_html__('All', 'opalestate-pro'),
        'publish'   => esc_html__('Published', 'opalestate-pro'),
        'published' => esc_html__('Published', 'opalestate-pro'),
        'pending'   => esc_html__('Pending', 'opalestate-pro'),
        'expired'   => esc_html__('Expired', 'opalestate-pro'),
    ]);
}

/**
 * Returns property meta icon classes.
 *
 * @param $key
 */
function opalestate_get_property_meta_icon($key) {
    $classes = '';
    $classes .= 'icon-property-' . esc_attr($key);

    switch ($key) {
        case 'builtyear':
            $icon = 'fas fa-calendar';
            break;
        case 'parking':
            $icon = 'fas fa-car';
            break;
        case 'bedrooms':
            $icon = 'fas fa-bed';
            break;
        case 'bathrooms':
            $icon = 'fas fa-bath';
            break;
        case 'plotsize':
            $icon = 'fas fa-map';
            break;
        case 'areasize':
            $icon = 'fas fa-arrows-alt';
            break;
        case 'orientation':
            $icon = 'fas fa-compass';
            break;
        case 'livingrooms':
            $icon = 'fas fa-tv';
            break;
        case 'kitchens':
            $icon = 'fas fa-utensils';
            break;
        case 'amountrooms':
            $icon = 'fas fa-building';
            break;
        default:
            $icon = $key;
            break;
    }

    $classes .= ' ';

    $classes .= apply_filters('opalestate_property_meta_icon', $icon, $key);

    return $classes;
}

/**
 * Is enable price field in the search forms?
 *
 * @return bool
 */
function opalestate_is_enable_price_field() {
    return 'on' == opalestate_get_option('opalestate_ppt_price_opt', 'on');
}

/**
 * Is enable areasize field in the search forms?
 *
 * @return bool
 */
function opalestate_is_enable_areasize_field() {
    return 'on' == opalestate_get_option('opalestate_ppt_areasize_opt', 'on');
}

function opalestate_is_require_login_to_show_author_box() {
    $require = opalestate_get_option('enable_single_login_to_show_author_box', 'off');

    return !($require == 'on') || ($require == 'on' && is_user_logged_in());
}

function opalestate_is_require_login_to_show_price() {
    $require = opalestate_get_option('enable_single_login_to_show_price', 'off');

    return !($require == 'on') || ($require == 'on' && is_user_logged_in());
}

function opalestate_is_require_login_to_show_enquire_form() {
    $require = opalestate_get_option('enable_single_login_to_enquire_form', 'off');

    return !($require == 'on') || ($require == 'on' && is_user_logged_in());
}

/**
 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
 * Non-scalar values are ignored.
 *
 * @param string|array $var Data to sanitize.
 * @return string|array
 */
function opalestate_clean($var) {
    if (is_array($var)) {
        return array_map('opalestate_clean', $var);
    }

    return is_scalar($var) ? sanitize_text_field($var) : $var;
}

/**
 * Get unique ID.
 *
 * This is a PHP implementation of Underscore's uniqueId method. A static variable
 * contains an integer that is incremented with each call. This number is returned
 * with the optional prefix. As such the returned value is not universally unique,
 * but it is unique across the life of the PHP process.
 *
 * @param string $prefix Prefix for the returned ID.
 * @return string Unique ID.
 * @see       wp_unique_id() Themes requiring WordPress 5.0.3 and greater should use this instead.
 *
 * @staticvar int $id_counter
 *
 */
function opalestate_unique_id($prefix = '') {
    static $id_counter = 0;
    if (function_exists('wp_unique_id')) {
        return wp_unique_id($prefix);
    }

    return $prefix . (string)++$id_counter;
}

/**
 * Get email date format.
 *
 * @return string
 */
function opalestate_email_date_format() {
    return apply_filters('opalestate_email_date_format', 'F j, Y, g:i a');
}

/**
 * Get autocomplete restrictions.
 *
 * @return string
 */
function opalestate_get_autocomplete_restrictions() {
    $restrictions_option = trim(opalestate_options('autocomplete_restrictions', ''));

    if (!$restrictions_option) {
        return '';
    }

    $array   = explode(',', $restrictions_option);
    $results = [];

    foreach ($array as $res) {
        $results[] = strtolower(trim($res));
    }

    if (!$results) {
        return '';
    }

    return json_encode($results);
}

/**
 * Query property data for a term and return IDs.
 *
 * Use for 'post__in' in WP_Query.
 *
 * @param string $term The term to search.
 * @return array
 */
function opalestate_search_property_by_term($term) {
    global $wpdb;

    // Filters the search fields.
    $search_fields = array_map('opalestate_clean', apply_filters('opalestate_search_property_fields', [
        'opalestate_ppt_sku',
        'opalestate_ppt_address',
    ]));

    // Prepare search bookings.
    $property_ids = [];

    if (is_numeric($term)) {
        $property_ids[] = absint($term);
    }

    if (!empty($search_fields)) {
        $search = $wpdb->get_col($wpdb->prepare(
            "SELECT DISTINCT `p1`.`post_id` FROM {$wpdb->postmeta} AS `p1` WHERE `p1`.`meta_value` LIKE %s AND `p1`.`meta_key` IN ('" . implode("','",
                array_map('esc_sql', $search_fields)) . "')", // @codingStandardsIgnoreLine
            '%' . $wpdb->esc_like(opalestate_clean($term)) . '%'
        ));

        $property_ids = array_unique(array_merge($property_ids, $search));
    }

    return apply_filters('opalestate_search_property_results', $property_ids, $term, $search_fields);
}

/**
 * Get Schedule Intervals
 *
 * @return array
 */
function opalestate_get_schedule_interval_options() {
    return apply_filters('opalestate_schedule_interval_options', [
            '0'                  => esc_html__('Never', 'opalestate-pro'),
            WEEK_IN_SECONDS      => esc_html__('1 Week', 'opalestate-pro'),
            DAY_IN_SECONDS       => esc_html__('24 Hours', 'opalestate-pro'),
            12 * HOUR_IN_SECONDS => esc_html__('12 Hours', 'opalestate-pro'),
            6 * HOUR_IN_SECONDS  => esc_html__('6 Hours', 'opalestate-pro'),
            HOUR_IN_SECONDS      => esc_html__('1 Hours', 'opalestate-pro'),
        ]
    );
}

if (!function_exists('opalestate_write_log')) {

    /**
     * Write log.
     *
     * @param $log
     */
    function opalestate_write_log($log) {
        if (true === WP_DEBUG) {
            if (is_array($log) || is_object($log)) {
                error_log(print_r($log, true));
            } else {
                error_log($log);
            }
        }
    }
}
