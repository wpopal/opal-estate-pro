<?php
/**
 * Search field templates.
 *
 * @package    opalestate
 * @author     Opal  Team <info@wpopal.com >
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Render field template.
 *
 * @param string $field Field.
 * @param string $label Label.
 * @param string $type Type.
 */
function opalestate_property_render_field_template($field, $label, $type = 'select') {
    $qvalue   = isset($_GET['info'][$field]) ? sanitize_text_field($_GET['info'][$field]) : '';
    $template = apply_filters('opalestate_property_render_search_field_template', $field, $label);
    $template = apply_filters('opalestate_property_' . $field . '_field_template', $template);

    if ($template == $field) {
        $template = '';
    }

    $input_default_value = apply_filters('opalestate_search_form_input_type_default_value', 0);

    if (empty($template)) {
        switch ($type) {
            case 'input': ?>
                <label class="opalestate-label opalestate-label--<?php echo sanitize_html_class($field); ?>"><?php echo esc_html($label); ?></label>
                <div class="input-group-number">
                    <i class="<?php echo opalestate_get_property_meta_icon($field); ?>"></i>
                    <input class="form-control" value="<?php echo esc_attr($qvalue ? $qvalue : $input_default_value); ?>" type="text" name="info[<?php echo $field; ?>]"
                           placeholder="<?php echo esc_attr(
                               $label); ?>"/>
                    <div class="btn-actions">
                        <span class="btn-minus"><i class="fa fa-minus"></i></span>
                        <span class="btn-plus"><i class="fa fa-plus"></i></span>
                    </div>
                </div>
                <?php
                break;

            default:
                $setting_search_type          = 'opalestate_ppt_' . $field . '_search_type';
                $setting_search_type_options  = 'opalestate_ppt_' . $field . '_options_value';
                $setting_search_min_range     = 'opalestate_ppt_' . $field . '_min_range';
                $setting_search_max_range     = 'opalestate_ppt_' . $field . '_max_range';
                $setting_search_unit_thousand = 'opalestate_ppt_' . $field . '_unit_thousand';
                $setting_search_default_text  = 'opalestate_ppt_' . $field . '_default_text';

                $display_type_search = opalestate_options($setting_search_type, 'select');

                if ($display_type_search == 'select') {
                    $option_values = (array)explode(',', opalestate_options($setting_search_type_options, '1,2,3,4,5,6,7,8,9,10'));
                    $option_values = array_map('trim', $option_values);
                    $option_values = array_combine($option_values, $option_values);
                    $option_values = apply_filters('opalestate_search_select_type_options', $option_values, $setting_search_type_options, $field);
                    $template      = '<label class="opalestate-label opalestate-label--' . sanitize_html_class($label) . '">' . esc_html($label) . '</label>';
                    $template      .= '<select class="form-control" name="info[%s]"><option value="">%s</option>';

                    foreach ($option_values as $option_key => $value) {
                        $selected = $value == $qvalue ? 'selected="selected"' : '';
                        $template .= '<option ' . $selected . ' value="' . esc_attr($option_key) . '">' . esc_html($value) . '</option>';
                    }
                    $template .= '</select>';
                    $template = sprintf($template, $field, $label);

                } elseif ($display_type_search == 'text') {
                    $option_values = opalestate_options($setting_search_default_text, '');
                    $qvalue        = $qvalue ? $qvalue : $option_values;
                    $template      = '<label class="opalestate-label opalestate-label--' . sanitize_html_class($label) . '">' . esc_html($label) . '</label>';
                    $template      .= '<input class="form-control" type="text" name="info[%s]" value="%s"/>';

                    $template = sprintf($template, $field, $qvalue);
                } elseif ($display_type_search == 'range') {
                    $min_name = 'min_' . $field;
                    $max_name = 'max_' . $field;

                    $search_min = (int)isset($_GET[$min_name]) ? $_GET[$min_name] : opalestate_options($setting_search_min_range, 0);
                    $search_max = (int)isset($_GET[$max_name]) ? $_GET[$max_name] : opalestate_options($setting_search_max_range, 1000);

                    $data = [
                        'id'            => $field,
                        'unit'          => '',
                        'ranger_min'    => opalestate_options($setting_search_min_range, 0),
                        'ranger_max'    => opalestate_options($setting_search_max_range, 1000),
                        'input_min'     => $search_min,
                        'input_max'     => $search_max,
                        'unit_thousand' => apply_filters('opalestate_search_range_unit_thousand', opalestate_options($setting_search_unit_thousand), $field),
                    ];

                    ob_start();

                    opalesate_property_slide_ranger_template(__($label . ": ", 'opalestate-pro'), $data);
                    $template = ob_get_contents();

                    ob_end_clean();
                } else {
                    $template = '<label class="opalestate-label opalestate-label--' . sanitize_html_class($label) . '">' . esc_html($label) . '</label>';

                    $template .= '<select class="form-control" name="info[%s]"><option value="">%s</option>';
                    for ($i = 1; $i <= 10; $i++) {
                        $selected = $i == $qvalue ? 'selected="selected"' : '';

                        $template .= '<option ' . $selected . ' value="' . $i . '">' . $i . '</option>';
                    }

                    $template .= '</select>';
                    $template = sprintf($template, $field, $label);
                }

                break;
        }
    }

    echo $template; // WPCS: XSS OK.
}

/**
 * Render area size field.
 */
function opalestate_property_areasize_field_template($template = '') {
    $search_min        = isset($_GET['min_area']) ? sanitize_text_field($_GET['min_area']) : opalestate_options('search_min_area', 0);
    $search_max        = isset($_GET['max_area']) ? sanitize_text_field($_GET['max_area']) : opalestate_options('search_max_area', 1000);
    $measurement_units = opalestate_get_measurement_units();
    $unit              = opalestate_options('measurement_unit', 'sqft');
    if (isset($measurement_units[$unit])) {
        $unit = $measurement_units[$unit];
    }

    $data = [
        'id'            => 'area',
        'unit'          => $unit . ' ',
        'ranger_min'    => opalestate_options('search_min_area', 0),
        'ranger_max'    => opalestate_options('search_max_area', 1000),
        'input_min'     => $search_min,
        'input_max'     => $search_max,
        'unit_thousand' => apply_filters('opalestate_areasize_unit_thousand', ','),
    ];

    opalesate_property_slide_ranger_template(esc_html__('Area', 'opalestate-pro'), $data);

    return;
}

add_filter('opalestate_property_areasize_field_template', 'opalestate_property_areasize_field_template');

/**
 * Render slider ranger template.
 *
 * @param $label
 * @param $data
 */
function opalesate_property_slide_ranger_template($label, $data) {
    $default = [
        'id'            => 'price',
        'unit'          => '',
        'decimals'      => 0,
        'ranger_min'    => 0,
        'ranger_max'    => 1000,
        'input_min'     => 0,
        'input_max'     => 1000,
        'unit_position' => 'postfix',
        'unit_thousand' => ',',
        'mode'          => 2,
        'start'         => '',
        'step'          => 1,
    ];

    $data = array_merge($default, $data);

    extract($data);
    ?>
    <label class="opalestate-label opalestate-label--<?php echo sanitize_title($label); ?>"><?php echo esc_html($label); ?></label>
    <div class="opal-slide-ranger" data-unit="<?php echo esc_attr($unit); ?>" data-unitpos="<?php echo esc_attr($unit_position); ?>" data-decimals="<?php echo esc_attr($decimals); ?>"
         data-thousand="<?php echo esc_attr($unit_thousand); ?>" data-step="<?php echo esc_attr($step); ?>">
        <label class="slide-ranger-label">
            <span class="slide-ranger-min-label"></span>
            <?php echo ($mode == 2) ? '<i>-</i>' : ''; ?>
            <span class="slide-ranger-max-label"></span>
        </label>

        <div class="slide-ranger-bar" data-min="<?php echo $ranger_min; ?>" data-max="<?php echo $ranger_max; ?>" data-mode="<?php echo $mode; ?>" data-start="<?php echo $start; ?>"></div>

        <?php if ($mode == 1) : ?>
            <input type="hidden" class="slide-ranger-min-input ranger-<?php echo $id; ?>" name="<?php echo $id; ?>" autocomplete="off" value="<?php echo (int)$input_min; ?>"/>
        <?php else : ?>
            <input type="hidden" class="slide-ranger-min-input ranger-<?php echo $id; ?>" name="min_<?php echo $id; ?>" autocomplete="off" value="<?php echo (int)$input_min; ?>"/>
            <input type="hidden" class="slide-ranger-max-input ranger-<?php echo $id; ?>" name="max_<?php echo $id; ?>" autocomplete="off" value="<?php echo (int)$input_max; ?>"/>
        <?php endif; ?>
    </div>
    <?php
}
