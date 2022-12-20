<?php
/**
 * HTML elements
 *
 * A helper class for outputting common HTML elements, such as product drop downs
 *
 * @package     Opalestate
 * @subpackage  Classes/HTML
 * @copyright   Copyright (c) 2015, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.5
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Opalestate_HTML_Elements Class
 *
 * @since 1.5
 */
class Opalestate_HTML_Elements {

    public $form_id = '';

    /**
     * Renders an ajax user search field
     *
     * @param array $args
     * @return string text field with ajax search
     * @since 2.0
     *
     */
    public function ajax_user_search($args = []) {

        $defaults = [
            'id'           => 'user_id',
            'name'         => 'user_id',
            'value'        => isset($args['default']) ? $args['default'] : null,
            'placeholder'  => esc_html__('Enter username', 'opalestate-pro'),
            'label'        => null,
            'desc'         => null,
            'class'        => '',
            'disabled'     => false,
            'autocomplete' => 'off',
            'data'         => false,
        ];

        $args = wp_parse_args($args, $defaults);

        $args['class'] = 'opalestate-ajax-user-search ' . $args['class'];

        $output = '<span class="opalestate_user_search_wrap">';
        $output .= $this->text_field($args);
        $output .= '<span class="opalestate_user_search_results hidden"><a class="opalestate-ajax-user-cancel" aria-label="' . esc_html__('Cancel',
                'opalestate-pro') . '" href="#">x</a><span></span></span>';
        $output .= '</span>';

        return $output;
    }

    /**
     * Text Field
     *
     * Renders an HTML Text field.
     *
     * @param array $args Arguments for the text field.
     *
     * @return string      The text field.
     * @since  1.0
     * @access public
     *
     */
    public function text_field($field_args, $args = []) {
        $defaults = [
            'id'           => '',
            'value'        => isset($field_args['default']) ? $field_args['default'] : null,
            'name'         => '',
            'description'  => null,
            'placeholder'  => '',
            'class'        => 'regular-text form-control',
            'disabled'     => false,
            'autocomplete' => 'off',
            'data'         => false,
            'default'      => '',
            'required'     => false,
        ];

        $args = wp_parse_args($field_args, $defaults);

        $disabled = '';
        if ($args['disabled']) {
            $disabled = ' disabled="disabled"';
        }

        $data = '';
        if (!empty($args['data'])) {
            foreach ($args['data'] as $key => $value) {
                $data .= 'data-' . $key . '="' . $value . '" ';
            }
        }

        if ($args['required']) {
            $data .= ' required="required" ';
        }

        $output = '<span id="opalestate-' . sanitize_key($this->form_id . $args['id']) . '-wrap">';

        $output .= '<label class="opalestate-label" for="opalestate-' . sanitize_key($this->form_id . $args['id']) . '">' . esc_html($args['name']) . '</label>';


        $output .= '<input type="text" name="' . esc_attr($args['id']) . '" id="opalestate-' . esc_attr($this->form_id . $args['id']) . '" autocomplete="' . esc_attr($args['autocomplete']) . '" value="' . esc_attr($args['value']) . '" placeholder="' . esc_attr($args['placeholder']) . '" class="' . $args['class'] . '" ' . $data . '' . $disabled . '/>';

        if (!empty($args['description'])) {
            $output .= '<span class="opalestate-description">' . esc_html($args['description']) . '</span>';
        }

        $output .= '</span>';

        return $output;
    }

    /**
     * Date Picker
     *
     * Renders a date picker field.
     *
     * @param array $args Arguments for the date picker.
     *
     * @return string      The date picker.
     * @since  1.5
     * @access public
     *
     */
    public function date_field($args = []) {

        if (empty($args['class'])) {
            $args['class'] = 'opalestate-datepicker form-control';
        } elseif (!strpos($args['class'], 'opalestate-datepicker')) {
            $args['class'] .= ' opalestate-datepicker form-control';
        }

        return $this->text_field($args);
    }

    /**
     * Textarea
     *
     * Renders an HTML textarea.
     *
     * @param array $args Arguments for the textarea.
     *
     * @return string      The textarea.
     * @since  1.0
     * @access public
     *
     */
    public function textarea_field($args = []) {
        $defaults = [
            'name'        => '',
            'value'       => isset($args['default']) ? $args['default'] : null,
            'label'       => null,
            'description' => null,
            'class'       => 'large-text',
            'disabled'    => false,
        ];

        $args = wp_parse_args($args, $defaults);

        $disabled = '';
        if ($args['disabled']) {
            $disabled = ' disabled="disabled"';
        }

        $output = '<span id="opalestate-' . sanitize_key($this->form_id . $args['id']) . '-wrap">';

        $output .= '<label class="opalestate-label" for="opalestate-' . sanitize_key($this->form_id . $args['id']) . '">' . esc_html($args['name']) . '</label>';

        $data = '';
        if ($args['required']) {
            $data .= ' required="required" ';
        }

        $output .= '<textarea name="' . esc_attr($args['id']) . '" id="opalestate-' . esc_attr($this->form_id . $args['id']) . '" class="' . $args['class'] . '"' . $disabled . ' ' . $data . ' >' . esc_attr($args['value']) . '</textarea>';

        if (!empty($args['description'])) {
            $output .= '<span class="opalestate-description">' . esc_html($args['description']) . '</span>';
        }

        $output .= '</span>';

        return $output;
    }

    /**
     * Dropdown
     *
     * Renders an HTML Dropdown.
     *
     * @param array $args Arguments for the dropdown.
     *
     * @return string      The dropdown.
     * @since  1.0
     * @access public
     *
     */
    public function select_field($field_args = []) {
        $defaults = [
            'options'          => [],
            'name'             => null,
            'class'            => 'form-control',
            'id'               => '',
            'autocomplete'     => 'off',
            'selected'         => 0,
            'chosen'           => false,
            'placeholder'      => null,
            'multiple'         => false,
            'select_atts'      => false,
            'show_option_all'  => esc_html__('All', 'opalestate-pro'),
            'show_option_none' => esc_html__('None', 'opalestate-pro'),
            'data'             => [],
            'readonly'         => false,
            'disabled'         => false,
            'required'         => '',
        ];

        $args = wp_parse_args($field_args, $defaults);

        $data_elements = '';
        foreach ($args['data'] as $key => $value) {
            $data_elements .= ' data-' . esc_attr($key) . '="' . esc_attr($value) . '"';
        }

        $multiple = '';
        if ($args['multiple']) {
            $multiple = 'MULTIPLE';
        }

        if ($args['chosen']) {
            $args['class'] .= ' opalestate-select-chosen';
        }

        $placeholder = '';
        if ($args['placeholder']) {
            $placeholder = $args['placeholder'];
        }

        $output = '<label class="opalestate-label" for="' . esc_attr(sanitize_key(str_replace('-', '_', $this->form_id . $args['id']))) . '">' . esc_html($args['name']) . '</label>';

        $data = '';
        if ($args['required']) {
            $data .= ' required="required" ';
        }

        $output .= sprintf(
            '<select ' . $data . ' name="%1$s" id="%2$s" autocomplete="%8$s" class="opalestate-select %3$s" %4$s %5$s data-placeholder="%6$s" %7$s>',
            esc_attr($args['id']),
            esc_attr(sanitize_key(str_replace('-', '_', $this->form_id . $args['id']))),
            esc_attr($args['class']),
            $multiple,
            $args['select_atts'],
            $placeholder,
            $data_elements,
            $args['autocomplete']
        );

        if ($args['show_option_all']) {
            if ($args['multiple']) {
                $selected = selected(true, in_array(0, $args['selected']), false);
            } else {
                $selected = selected($args['selected'], 0, false);
            }
            // $output .= '<option value="all"' . $selected . '>' . esc_html( $args['show_option_all'] ) . '</option>';
        }

        if (!empty($args['options'])) {

            if ($args['show_option_none']) {
                if ($args['multiple']) {
                    $selected = selected(true, in_array(-1, $args['selected']), false);
                } else {
                    $selected = selected($args['selected'], -1, false);
                }
                // $output .= '<option value="-1"' . $selected . '>' . esc_html( $args['show_option_none'] ) . '</option>';
            }

            foreach ($args['options'] as $key => $option) {

                if ($args['multiple'] && is_array($args['selected'])) {
                    $selected = selected(true, in_array($key, $args['selected']), false);
                } else {
                    $selected = selected($args['selected'], $key, false);
                }

                $output .= '<option value="' . esc_attr($key) . '"' . $selected . '>' . esc_html($option) . '</option>';
            }
        }

        $output .= '</select>';

        return $output;
    }

    public function hidden_field($args) {
        $defaults = [
            'id'           => '',
            'value'        => isset($args['default']) ? $args['default'] : null,
            'name'         => '',
            'description'  => null,
            'placeholder'  => '',
            'class'        => 'regular-text form-control',
            'disabled'     => false,
            'autocomplete' => 'off',
            'data'         => false,
            'default'      => '',
            'required'     => false,
        ];
        $args     = wp_parse_args($args, $defaults);

        $output = '<input type="hidden" name="' . esc_attr($args['id']) . '"  autocomplete="' . esc_attr($args['autocomplete']) . '" value="' . esc_attr($args['value']) . '"  class="' . $args['class'] . '" />';

        return $output;
    }

    public function render_field($field) {
        switch ($field['type']) {
            case 'date':

                return $this->date_field($field);

                break;

            case 'text':

                return $this->text_field($field);

                break;
            case 'hidden':

                return $this->hidden_field($field);

                break;

            case 'textarea':

                return $this->textarea_field($field);

                break;
            case 'user':

                return $this->ajax_user_search($field);

                break;

            case 'select':
                return $this->select_field($field);
                break;
            default:
                # code...
                break;
        }
    }

    public function render_form($fields) {
        $form_id = opalestate_unique_id('opalestate-form-');

        $output        = '';
        $this->form_id = $form_id;
        foreach ($fields as $field) {
            $wrap = '';
            if (isset($field['before_row'])) {
                $wrap .= $field['before_row'];
            }
            $wrap .= '<div class="form-group">';
            $wrap .= $this->render_field($field);
            $wrap .= '</div>';

            if (isset($field['after_row'])) {
                $wrap .= $field['after_row'];
            }

            $output .= $wrap;
        }

        return $output;
    }
}
