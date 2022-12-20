<?php
/**
 * Opalestate_Rating_MetaBox
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

/**
 * Class Opalestate_Rating_MetaBox
 */
class Opalestate_Rating_MetaBox {

    public function register_admin_comment_fields() {
        $rating_supports = Opalestate_Rating::get_rating_supports();

        foreach ($rating_supports as $key => $support) {
            $this->register_comment_metabox($support['post_type'], $support['features_cpt']);
        }
    }

    public function register_admin_feature_fields() {
        $rating_supports = Opalestate_Rating::get_rating_supports();

        foreach ($rating_supports as $key => $support) {
            $this->register_feature_metabox($support['features_cpt']);
        }
    }

    /**
     * Hook in and register a metabox for the admin comment edit page.
     */
    public function register_comment_metabox($cpt_support, $cpt_feature) {
        if (!isset($_GET['c'])) {
            return;
        }

        $comment_type = get_comment_type(sanitize_text_field($_GET['c']));

        if ($comment_type !== $this->get_comment_type($cpt_support)) {
            return;
        }

        $features = Opalestate_Rating_Helper::get_features($cpt_feature);

        $cmb = new_cmb2_box([
            'id'           => $cpt_support . '_comment_meta',
            'title'        => $features ? esc_html__('Rating features', 'opalestate-pro') : esc_html__('Rating', 'opalestate-pro'),
            'object_types' => ['comment'],
        ]);

        if ($features) {
            foreach ($features as $feature_slug => $feature_title) {
                $id = $cpt_feature . '_' . $feature_slug;

                $cmb->add_field([
                    'id'      => $id,
                    'type'    => 'select',
                    'name'    => $feature_title,
                    'options' => [
                        '1' => '1&nbsp;&#9733;',
                        '2' => '2&nbsp;&#9733;&#9733;',
                        '3' => '3&nbsp;&#9733;&#9733;&#9733;',
                        '4' => '4&nbsp;&#9733;&#9733;&#9733;&#9733;',
                        '5' => '5&nbsp;&#9733;&#9733;&#9733;&#9733;&#9733;',
                    ],
                    // 'show_on_cb' => function ( $cmb ) {
                    // 	return isset( $_GET['c'] );
                    // },
                ]);
            }
        } else {
            $cmb->add_field([
                'id'      => 'opalestate_rating',
                'type'    => 'select',
                'options' => [
                    '1' => '1&nbsp;&#9733;',
                    '2' => '2&nbsp;&#9733;&#9733;',
                    '3' => '3&nbsp;&#9733;&#9733;&#9733;',
                    '4' => '4&nbsp;&#9733;&#9733;&#9733;&#9733;',
                    '5' => '5&nbsp;&#9733;&#9733;&#9733;&#9733;&#9733;',
                ],
                // 'show_on_cb' => function ( $cmb ) {
                // 	return isset( $_GET['c'] );
                // },
            ]);
        }
    }

    /**
     * Hook in and register a metabox for the admin comment edit page.
     */
    public function register_feature_metabox($cpt_feature) {
        $cmb = new_cmb2_box([
            'id'           => $cpt_feature . '_meta',
            'title'        => esc_html__('Data', 'opalestate-pro'),
            'object_types' => [$cpt_feature],
        ]);

        $cmb->add_field([
            'name' => esc_html__('Description', 'opalestate-pro'),
            'id'   => 'opalestate_feature_desc',
            'type' => 'textarea_small',
        ]);

        $cmb->add_field([
            'name'       => esc_html__('Order', 'opalestate-pro'),
            'desc'       => esc_html__('Set a priority to display', 'opalestate-pro'),
            'id'         => 'opalestate_feature_order',
            'type'       => 'text_small',
            'attributes' => [
                'type' => 'number',
            ],
            'default'    => 0,
        ]);
    }

    /**
     * Save meta box data
     *
     * @param mixed $data Data to save.
     * @return mixed
     */
    public static function save($data) {
        if (!isset($data['comment_post_ID']) || !$data['comment_post_ID']) {
            return $data;
        }

        $comment_post_ID = $data['comment_post_ID'];
        $cpt_support     = get_post_type($comment_post_ID);
        $rating_supports = Opalestate_Rating::get_rating_supports();

        if (!isset($rating_supports[$cpt_support]) || !isset($rating_supports[$cpt_support]['features_cpt'])) {
            return $data;
        }

        $cpt_feature = $rating_supports[$cpt_support]['features_cpt'];
        $comment_id  = $data['comment_ID'];
        $features    = Opalestate_Rating_Helper::get_features($cpt_feature);

        if ($features) {
            foreach ($features as $feature_slug => $feature_title) {
                $id = $cpt_feature . '_' . $feature_slug;
                if (isset($_POST[$id]) && ($_POST[$id] > 0) && ($_POST[$id] <= 5)) {
                    update_comment_meta($comment_id, $id, intval(wp_unslash($_POST[$id]))); // WPCS: input var ok.
                }
            }
        } else {
            if (isset($_POST['opalestate_rating']) && ($_POST['opalestate_rating'] > 0) && ($_POST['opalestate_rating'] <= 5)) {
                update_comment_meta($comment_id, 'opalestate_rating', intval(wp_unslash($_POST['opalestate_rating']))); // WPCS: input var ok.
            }
        }

        // Return regular value after updating.
        return $data;
    }

    /**
     * Gets comment type.
     *
     * @return string
     */
    public function get_comment_type($cpt_support) {
        return str_replace('opalestate_', '', $cpt_support) . '_review';
    }
}
