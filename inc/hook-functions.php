<?php
/**
 * Hook functions.
 *
 * @package    opalestate
 * @author     Opal  Team <info@wpopal.com >
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function opalestate_widgets_init() {
    register_sidebar([
        'name'          => esc_html__('Single Property Sidebar', 'opalestate-pro'),
        'id'            => 'opalestate-single-property',
        'description'   => esc_html__('Add widgets here to appear in your single property sidebar area.', 'opalestate-pro'),
        'before_widget' => '<div id="%1$s" class="widget opalestate-single-property-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h5 class="widget-title">',
        'after_title'   => '</h5>',
    ]);
}

add_action('widgets_init', 'opalestate_widgets_init');

/**
 * Add hidden multilingual.
 */
function opalestate_add_hidden_multilingual() {
    if (!opalestate_running_on_multilanguage()) {
        return;
    }
    ?>
    <input type="hidden" name="lang" value="<?php echo opalestate_multilingual()->get_current_language(); ?>">
    <?php
}

add_action('opalestate_after_search_properties_form', 'opalestate_add_hidden_multilingual');

/**
 * Clean cron jobs when saving settings.
 *
 * @param $old_value
 * @param $value
 */
function opalestate_clean_cron_jobs($old_value, $value) {
    $update_schedule = isset($value['schedule']) ? $value['schedule'] : 0;
    $old_schedule    = isset($old_value['schedule']) ? $old_value['schedule'] : 0;

    if ($update_schedule !== $old_schedule) {
        wp_clear_scheduled_hook('opalestate_clean_update');
        if ($update_schedule) {
            add_filter('cron_schedules', [Opalestate_Install::class, 'cron_schedules']);

            if (!wp_next_scheduled('opalestate_clean_update')) {
                wp_schedule_event(time(), 'opalestate_corn', 'opalestate_clean_update');
            }
        }
    }
}

add_action('update_option_opalestate_settings', 'opalestate_clean_cron_jobs', 10, 2);

/**
 * Clean update.
 */
function opalestate_clean_update() {
    try {
        $query = new WP_Query(
            [
                'post_type'   => 'attachment',
                'post_status' => 'inherit',
                'date_query'  => [
                    'column' => 'post_date',
                    'before' => date('Y-m-d', strtotime('-1 days')),
                ],
                'meta_query'  => [
                    [
                        'key'     => '_pending_to_use_',
                        'value'   => 1,
                        'compare' => '>=',
                    ],
                ],
            ]
        );

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                wp_delete_attachment(get_the_ID());
            }
        }
        wp_reset_postdata();

        // Change status expired properties.
        $expired_query = new WP_Query([
            'post_type'   => 'opalestate_property',
            'post_status' => ['pending', 'publish'],
            'meta_query'  => [
                [
                    'key'     => OPALESTATE_PROPERTY_PREFIX . 'expired_time',
                    'value'   => time(),
                    'compare' => '<',
                    'type'    => 'NUMERIC',
                ],
            ],
        ]);

        opalestate_write_log($expired_query->found_posts);

        if ($expired_query->have_posts()) {
            while ($expired_query->have_posts()) {
                $expired_query->the_post();
                opalestate_write_log(get_the_ID());

                wp_update_post([
                    'ID'          => get_the_ID(),
                    'post_status' => 'expired',
                ]);
            }
        }

        wp_reset_postdata();
    } catch (Exception $e) {
        opalestate_write_log($e->getMessage());
    }
}

add_action('opalestate_clean_update', 'opalestate_clean_update');
