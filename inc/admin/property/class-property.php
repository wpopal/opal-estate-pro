<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Opalestate_Admin_Property {
    /**
     * @var $tab
     */
    private $tab;

    /**
     * Opalestate_Admin_Property constructor.
     */
    public function __construct() {

        add_filter('cmb2_admin_init', [$this, 'metaboxes']);

        add_action('transition_opalestate_property_status', [$this, 'process_publish_property'], 10, 1);
        /* property column */
        add_filter('manage_opalestate_property_posts_columns', [$this, 'columns']);
        add_action('manage_opalestate_property_posts_custom_column', [$this, 'custom_columns'], 10, 2);

        add_action('admin_menu', [$this, 'remove_meta_boxes']);

        // add_action( 'transition_post_status', array( __CLASS__, 'save_post' ), 10, 3  );
        add_action('parse_query', [$this, 'search_custom_fields']);

        add_filter('display_post_states', [$this, 'display_post_states'], 10, 2);
    }

    /**
     *
     */
    public static function save_post($new_status, $old_status, $post) {
        if ($new_status == 'publish' && $post->post_type == "opalestate_property") {
            $user_id = $post->post_author;
            $user    = get_user_by('id', $user_id);
            if (!is_object($user)) {
                $from_name  = opalestate_get_option('from_name');
                $from_email = opalestate_get_option('from_email');
                $subject    = opalestate_get_option('publish_submission_email_subject');

                $headers = sprintf("From: %s <%s>\r\n Content-type: text/html", $from_name, $from_email);

                $property_link = get_permalink($post);
                $tags          = ["{first_name}", "{last_name}", "{property_link}"];
                $values        = [$user->first_name, $user->last_name, $property_link];

                $body    = opalestate_get_option('publish_submission_email_body');
                $body    = html_entity_decode($body);
                $message = str_replace($tags, $values, $body);

                return wp_mail($user->user_email, $subject, $message, $headers);
            }
        }
    }

    /**
     *
     */
    public function metaboxes() {

        global $pagenow;
        if (($pagenow == 'post.php' || $pagenow == 'post-new.php')) {
            $metabox = new Opalestate_Property_MetaBox();

            return $metabox->register_admin_fields();
        }
    }


    private function add_fields_to_tab($fields, $tab) {

        foreach ($fields as $field) {
            $field['tab']           = $tab;
            $field['render_row_cb'] = ['CMB2_Tabs', 'tabs_render_row_cb'];

            $this->tab->add_field($field);

        }
    }

    /**
     *
     */
    public function columns($columns) {
        $comments = $columns['comments'];
        unset($columns['author'], $columns['date'], $columns['comments']);
        $columns['featured']    = esc_html__('Featured', 'opalestate-pro');
        $columns['sku']         = esc_html__('Sku', 'opalestate-pro');
        $columns['address']     = esc_html__('Address', 'opalestate-pro');
        $columns['comments']    = $comments;
        $columns['author']      = esc_html__('Author', 'opalestate-pro');
        $columns['date']        = esc_html__('Date', 'opalestate-pro');
        $columns['expiry_date'] = esc_html__('Expiry Date', 'opalestate-pro');

        return $columns;
    }

    /**
     *
     */
    public function custom_columns($column, $post_id) {
        $property = new Opalestate_Property($post_id);
        $nonce    = wp_create_nonce('opalestate_property');
        switch ($column) {
            case 'featured':
                if ($property->featured) {
                    $url = add_query_arg([
                        'action'      => 'opalestate_remove_feature_property',
                        'property_id' => $post_id,
                        'nonce'       => $nonce,
                    ], admin_url('admin-ajax.php'));
                    echo '<a href="' . esc_url($url) . '">';
                    echo '<i class="dashicons dashicons-star-filled"></i>';
                    echo '</a>';
                } else {
                    $url = add_query_arg([
                        'action'      => 'opalestate_set_feature_property',
                        'property_id' => $post_id,
                        'nonce'       => $nonce,
                    ], admin_url('admin-ajax.php'));
                    echo '<a href="' . esc_url($url) . '">';
                    echo '<i class="dashicons dashicons-star-empty"></i>';
                    echo '</a>';
                }
                break;

            case 'sku':
                if ($property->sku) {
                    echo sprintf('%s', $property->sku);
                }
                break;

            case 'address':
                if ($property->address) {
                    echo sprintf('%s', $property->address);
                }
                break;

            case 'expiry_date':
                if ($property->get_expiry_date()) {
                    $expired_time = $property->get_expiry_date();
                    echo date_i18n(__('Y/m/d g:i:s a', 'opalestate-pro'), $expired_time);
                } else {
                    echo esc_html_x('---', 'expired property', 'opalestate-pro');
                }
                break;

            default:
                # code...
                break;
        }
    }

    public function remove_meta_boxes() {
        remove_meta_box('authordiv', 'opalestate_property', 'normal');
    }

    /**
     * Query custom fields as well as content.
     *
     * @param \WP_Query $wp The WP_Query object.
     *
     * @access private
     */
    public function search_custom_fields($wp) {
        global $pagenow;

        if ('edit.php' !== $pagenow
            || empty($wp->query_vars['s'])
            || 'opalestate_property' !== $wp->query_vars['post_type']
            || !isset($_GET['s'])) {
            return;
        }

        $post_ids = opalestate_search_property_by_term(opalestate_clean(wp_unslash($_GET['s']))); // WPCS: input var ok, sanitization ok.

        if (!empty($post_ids)) {
            // Remove "s" - we don't want to search order name.
            unset($wp->query_vars['s']);

            // Query by found posts.
            $wp->query_vars['post__in'] = array_merge($post_ids, [0]);
        }
    }

    /**
     * Filters the default post display states used in the posts list table.
     *
     * @param string[] $post_states An array of post display states.
     * @param WP_Post $post The current post object.
     */
    public function display_post_states($post_states, $post) {
        if ('expired' == $post->post_status) {
            $post_states['expired'] = _x('Expired', 'post status', 'opalestate-pro');
        }

        return $post_states;
    }
}

new Opalestate_Admin_Property();
