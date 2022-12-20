<?php
/**
 * Body classes.
 */
add_filter('body_class', 'opalestate_body_class');

/**
 * Archive Page
 */
add_action("opalestate_archive_property_page_before", "opalestate_archive_search_block", 4);
/**
 * Layout Single Default
 */
function opalestate_single_property_layout_default() {
    add_action('opalestate_single_property_summary', 'opalestate_get_single_short_meta', 10);
    add_action('opalestate_single_property_summary', 'opalestate_property_content', 12);
    add_action('opalestate_single_property_summary', 'opalestate_property_information', 15);
    add_action('opalestate_single_property_summary', 'opalestate_property_amenities', 16);
    add_action('opalestate_single_property_summary', 'opalestate_property_facilities', 17);
    add_action('opalestate_single_property_summary', 'opalestate_property_attachments', 18);

    add_action('opalestate_after_single_property_summary', 'opalestate_property_video', 20);
    add_action('opalestate_after_single_property_summary', 'opalestate_property_virtual_tour', 25);
    add_action('opalestate_after_single_property_summary', 'opalestate_property_map', 30);

    add_action('opalestate_after_single_property_summary', 'opalestate_property_nearby', 35);
    add_action('opalestate_after_single_property_summary', 'opalestate_property_walkscore', 40);

    add_action('opalestate_after_single_property_summary', 'opalestate_property_apartments', 45);
    add_action('opalestate_after_single_property_summary', 'opalestate_property_floor_plans', 45);
    add_action('opalestate_after_single_property_summary', 'opalestate_property_views_statistics', 50);
    add_action('opalestate_after_single_property_summary', 'opalestate_property_tags', 60);

    if (opalestate_property_reviews_enabled()) {
        add_action('opalestate_after_single_property_summary', 'comments_template', 65);
    }

    add_action('opalestate_after_single_property_summary_v2', 'opalestate_property_map_v2', 5);

    add_action('opalestate_single_property_sidebar', 'opalestate_property_author_v2', 5);
    add_action('opalestate_single_property_sidebar', 'opalestate_property_equiry_form', 6);

    $single_mortgage = get_post_meta(get_the_ID(), OPALESTATE_PROPERTY_PREFIX . 'enable_single_mortgage', true);

    if ($single_mortgage === 'on') {
        add_action('opalestate_single_property_sidebar', 'opalestate_property_mortgage', 9);
    } elseif ($single_mortgage === '') {
        if (opalestate_get_option('enable_single_mortgage', 'on') == 'on') {
            add_action('opalestate_single_property_sidebar', 'opalestate_property_mortgage', 9);
        }
    }

    add_filter('opalestate_thumbnail_nav_column', function () {
        return 6;
    });
}


/**
 * Layout Single Default
 */
function opalestate_single_property_layout_v2() {
    add_action('opalestate_single_property_summary', 'opalestate_get_single_short_meta', 10);
    add_action('opalestate_single_property_summary', 'opalestate_property_content', 12);

    add_action('opalestate_after_single_property_summary', 'opalestate_property_video', 20);
    add_action('opalestate_after_single_property_summary', 'opalestate_property_virtual_tour', 25);
    add_action('opalestate_after_single_property_summary', 'opalestate_property_map', 30);
    add_action('opalestate_after_single_property_summary', 'opalestate_property_nearby', 35);
    add_action('opalestate_after_single_property_summary', 'opalestate_property_walkscore', 40);
    add_action('opalestate_after_single_property_summary', 'opalestate_property_apartments', 45);
    add_action('opalestate_after_single_property_summary', 'opalestate_property_views_statistics', 50);

    add_action('opalestate_after_single_property_summary', 'opalestate_property_tags', 60);

    if (opalestate_property_reviews_enabled()) {
        add_action('opalestate_after_single_property_summary', 'comments_template', 65);
    }

    add_action('opalestate_after_single_property_summary_v2', 'opalestate_property_map_v2', 5);

    $single_mortgage = get_post_meta(get_the_ID(), OPALESTATE_PROPERTY_PREFIX . 'enable_single_mortgage', true);

    if ($single_mortgage === 'on') {
        add_action('opalestate_single_property_sidebar', 'opalestate_property_mortgage', 9);
    } elseif ($single_mortgage === '') {
        if (opalestate_get_option('enable_single_mortgage', 'on') == 'on') {
            add_action('opalestate_single_property_sidebar', 'opalestate_property_mortgage', 9);
        }
    }

    add_action('opalestate_single_property_sidebar', 'opalestate_property_author_v3', 10);
}


/**
 * Layout Single Version 3
 */
function opalestate_single_property_layout_v3() {
    add_action('opalestate_single_property_summary', 'opalestate_get_single_short_meta', 10);
    add_action('opalestate_single_property_summary', 'opalestate_property_content', 12);
    add_action('opalestate_single_property_summary', 'opalestate_property_information', 15);
    add_action('opalestate_single_property_summary', 'opalestate_property_amenities', 16);
    add_action('opalestate_single_property_summary', 'opalestate_property_facilities', 17);
    add_action('opalestate_single_property_summary', 'opalestate_property_attachments', 18);

    add_action('opalestate_after_single_property_summary', 'opalestate_property_video', 20);
    add_action('opalestate_after_single_property_summary', 'opalestate_property_virtual_tour', 25);
    add_action('opalestate_after_single_property_summary', 'opalestate_property_map', 30);

    add_action('opalestate_after_single_property_summary', 'opalestate_property_walkscore', 40);
    add_action('opalestate_after_single_property_summary', 'opalestate_property_apartments', 45);
    add_action('opalestate_after_single_property_summary', 'opalestate_property_views_statistics', 50);

    add_action('opalestate_after_single_property_summary', 'opalestate_property_tags', 60);

    if (opalestate_property_reviews_enabled()) {
        add_action('opalestate_after_single_property_summary', 'comments_template', 65);
    }

    add_action('opalestate_after_single_property_summary_v2', 'opalestate_property_map_v2', 5);

    add_action('opalestate_single_property_sidebar', 'opalestate_property_author_v2', 5);
    add_action('opalestate_single_property_sidebar', 'opalestate_property_equiry_form', 6);

    $single_mortgage = get_post_meta(get_the_ID(), OPALESTATE_PROPERTY_PREFIX . 'enable_single_mortgage', true);

    if ($single_mortgage === 'on') {
        add_action('opalestate_single_property_sidebar', 'opalestate_property_mortgage', 9);
    } elseif ($single_mortgage === '') {
        if (opalestate_get_option('enable_single_mortgage', 'on') == 'on') {
            add_action('opalestate_single_property_sidebar', 'opalestate_property_mortgage', 9);
        }
    }
}

/**
 * Layout Single Version 4
 */
function opalestate_single_property_layout_v4() {
    add_action('opalestate_single_property_summary', 'opalestate_get_single_short_meta', 10);
    add_action('opalestate_single_property_summary', 'opalestate_property_content', 12);
    add_action('opalestate_single_property_summary', 'opalestate_property_information', 15);
    add_action('opalestate_single_property_summary', 'opalestate_property_amenities', 16);
    add_action('opalestate_single_property_summary', 'opalestate_property_facilities', 17);
    add_action('opalestate_single_property_summary', 'opalestate_property_attachments', 18);

    add_action('opalestate_after_single_property_summary', 'opalestate_property_video', 20);
    add_action('opalestate_after_single_property_summary', 'opalestate_property_virtual_tour', 25);
    add_action('opalestate_after_single_property_summary', 'opalestate_property_map', 30);

    add_action('opalestate_after_single_property_summary', 'opalestate_property_nearby', 35);
    add_action('opalestate_after_single_property_summary', 'opalestate_property_walkscore', 40);

    add_action('opalestate_after_single_property_summary', 'opalestate_property_apartments', 45);
    add_action('opalestate_after_single_property_summary', 'opalestate_property_views_statistics', 50);
    add_action('opalestate_after_single_property_summary', 'opalestate_property_tags', 60);

    if (opalestate_property_reviews_enabled()) {
        add_action('opalestate_after_single_property_summary', 'comments_template', 65);
    }

    add_action('opalestate_after_single_property_summary_v2', 'opalestate_property_map_v2', 5);

    add_action('opalestate_single_property_sidebar', 'opalestate_property_author_v2', 5);
    add_action('opalestate_single_property_sidebar', 'opalestate_property_equiry_form', 6);

    $single_mortgage = get_post_meta(get_the_ID(), OPALESTATE_PROPERTY_PREFIX . 'enable_single_mortgage', true);

    if ($single_mortgage === 'on') {
        add_action('opalestate_single_property_sidebar', 'opalestate_property_mortgage', 9);
    } elseif ($single_mortgage === '') {
        if (opalestate_get_option('enable_single_mortgage', 'on') == 'on') {
            add_action('opalestate_single_property_sidebar', 'opalestate_property_mortgage', 9);
        }
    }
}

/**
 * Layout Single Version 5
 */
function opalestate_single_property_layout_v5() {
    add_action('opalestate_single_property_summary', 'opalestate_get_single_short_meta', 10);
    add_action('opalestate_single_property_summary', 'opalestate_property_content', 12);
    add_action('opalestate_single_property_summary', 'opalestate_property_information', 15);
    add_action('opalestate_single_property_summary', 'opalestate_property_amenities', 16);
    add_action('opalestate_single_property_summary', 'opalestate_property_facilities', 17);
    add_action('opalestate_single_property_summary', 'opalestate_property_attachments', 18);

    add_action('opalestate_after_single_property_summary', 'opalestate_property_video', 20);
    add_action('opalestate_after_single_property_summary', 'opalestate_property_virtual_tour', 25);
    add_action('opalestate_after_single_property_summary', 'opalestate_property_map', 30);
    add_action('opalestate_after_single_property_summary', 'opalestate_property_nearby', 35);
    add_action('opalestate_after_single_property_summary', 'opalestate_property_walkscore', 40);
    add_action('opalestate_after_single_property_summary', 'opalestate_property_apartments', 45);

    add_action('opalestate_after_single_property_summary', 'opalestate_property_floor_plans', 45);

    add_action('opalestate_after_single_property_summary', 'opalestate_property_views_statistics', 50);

    add_action('opalestate_after_single_property_summary', 'opalestate_property_tags', 60);

    if (opalestate_property_reviews_enabled()) {
        add_action('opalestate_after_single_property_summary', 'comments_template', 65);
    }

    add_action('opalestate_after_single_property_summary_v2', 'opalestate_property_map_v2', 5);

    add_filter('opalestate_thumbnail_nav_column', function () {
        return 10;
    });

    add_action('opalestate_single_property_sidebar', 'opalestate_property_author_v2', 5);
    add_action('opalestate_single_property_sidebar', 'opalestate_property_equiry_form', 6);

    $single_mortgage = get_post_meta(get_the_ID(), OPALESTATE_PROPERTY_PREFIX . 'enable_single_mortgage', true);

    if ($single_mortgage === 'on') {
        add_action('opalestate_single_property_sidebar', 'opalestate_property_mortgage', 9);
    } elseif ($single_mortgage === '') {
        if (opalestate_get_option('enable_single_mortgage', 'on') == 'on') {
            add_action('opalestate_single_property_sidebar', 'opalestate_property_mortgage', 9);
        }
    }
}

add_action('opalestate_single_property_after_render', 'opalestate_properties_related', 5);
add_action('opalestate_single_property_after_render', 'opalestate_properties_nearby', 6);
add_action('opalestate_single_property_preview', 'opalestate_property_preview', 15);

function opalestate_property_request_viewing_button($islink = false) {
    if ('on' != opalestate_get_option('enable_single_request_viewing', 'on')) {
        return;
    }

    $class = $islink ? 'btn-link' : 'btn btn-primary';
    if (!is_user_logged_in()) {
        $class .= ' opalestate-need-login';
    } else {
        $class .= ' opalestate-popup-button';
    }

    echo '<a href="#opalestate-user-form-popup" class="' . $class . ' btn-request-viewing" data-target="#property-request-view-popup" >
    <i class="fa fa-calendar-check-o"></i>
    <span class="btn-request-viewing__text">' . esc_html__('Request Viewing', 'opalestate-pro') . '</span>
    </a>';
}

/**
 * Get single layout.
 *
 * @param string $layout Layout.
 */
function opalestate_single_property_layout($layout) {
    switch ($layout) {
        case 'v2':
            opalestate_single_property_layout_v2();
            break;
        case 'v3':
            opalestate_single_property_layout_v3();
            break;
        case 'v4':
            opalestate_single_property_layout_v4();
            break;
        case 'v5':
            opalestate_single_property_layout_v5();
            break;
        default:
            opalestate_single_property_layout_default();
            break;
    }
}

add_action('opalestate_single_property_layout', 'opalestate_single_property_layout');

/**
 * Forms
 */
function opalestate_property_request_view_form() {
    if ('on' != opalestate_get_option('enable_single_request_viewing', 'on')) {
        return;
    }

    if (!is_user_logged_in()) {
        return;
    }

    if (!is_single_property()) {
        return;
    }

    $object = OpalEstate_User_Message::get_instance();
    $fields = $object->get_request_review_form_fields();

    $form = OpalEstate()->html->render_form($fields);

    $description = esc_html__('Physical Arrange viewings is always been attractive to property clients. Fill out the form to arrange visualizations around our properties.', 'opalestate-pro');

    $atts = [
        'heading'     => esc_html__('Request Viewing', 'opalestate-pro'),
        'description' => $description,
        'id'          => 'property-request-view',
        'form'        => $form,
    ];

    echo opalestate_load_template_path('messages/request-reviewing-form', $atts);
}

add_action('wp_footer', 'opalestate_property_request_view_form', 9);

function opalestate_property_equiry_form() {
    echo opalestate_load_template_path('messages/enquiry-form');
}

if (!function_exists("opalestate_login_register_form_popup")) {
    function opalestate_login_register_form_popup() {
        echo opalestate_load_template_path('user/my-account-popup');
    }
}
add_action('wp_footer', 'opalestate_login_register_form_popup', 9);

/**
 * Add "Custom" template to page attirbute template section.
 */
function opalestate_add_template_to_select($post_templates, $wp_theme, $post, $post_type) {
    // Add custom template named template-custom.php to select dropdown
    $post_templates['user-management.php'] = esc_html__('User Management', 'opalestate-pro');
    $post_templates['fullwidth-page.php']  = esc_html__('Opalestate Fullwidth', 'opalestate-pro');

    return $post_templates;
}

add_filter('theme_page_templates', 'opalestate_add_template_to_select', 10, 4);

function opalestate_load_plugin_template($template) {
    if (get_page_template_slug() === 'user-management.php') {

        if ($theme_file = locate_template(['page-templates/user-management.php', 'user-management.php'])) {
            $template = $theme_file;
        } else {
            $template = OPALESTATE_PLUGIN_DIR . '/templates/user-management.php';
        }
    } elseif (get_page_template_slug() === 'fullwidth-page.php') {

        if ($theme_file = locate_template(['page-templates/fullwidth-page.php', 'fullwidth-page.php'])) {
            $template = $theme_file;
        } else {
            $template = OPALESTATE_PLUGIN_DIR . '/templates/fullwidth-page.php';
        }
    }

    if ($template == '') {
        throw new Exception('No template found');
    }

    return $template;
}

add_filter('template_include', 'opalestate_load_plugin_template');
add_action('opalestate_before_property_loop_item', 'opalestate_property_featured_label');
add_action('opalestate_before_property_loop_item', 'opalestate_property_label');

/**
 * Add custom sidebar widgets to single property sidebar.
 */
function opalestate_single_property_sidebar_widgets() {
    if (is_active_sidebar('opalestate-single-property')) : ?>
        <div class="opalestate-single-property-widgets">
            <?php dynamic_sidebar('opalestate-single-property'); ?>
        </div>
    <?php endif;
}

add_action('opalestate_single_property_sidebar', 'opalestate_single_property_sidebar_widgets', 99);

function opalestate_hide_unset_amenities($show) {
    if ('off' === opalestate_get_option('hide_unset_amenities', 'off')) {
        return false;
    }

    return true;
}

add_filter('opalestate_hide_unset_amenity', 'opalestate_hide_unset_amenities');
