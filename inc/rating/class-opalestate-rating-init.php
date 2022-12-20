<?php

/**
 * Class Opalestate_Rating_Init
 */
class Opalestate_Rating_Init {
    /**
     * @var string
     */
    protected $cpt_support;

    /**
     * @var string
     */
    protected $cpt_feature;

    /**
     * @var string
     */
    protected $meta_prefix;

    /**
     * Opalestate_Rating_Init constructor.
     *
     * @param string $cpt_support Custom post type supported name
     * @param string $cpt_feature Custom post type feature rating name
     * @param string $meta_prefix Meta data prefix.
     */
    public function __construct($cpt_support, $cpt_feature, $meta_prefix) {
        $this->cpt_support = $cpt_support;
        $this->cpt_feature = $cpt_feature;
        $this->meta_prefix = $meta_prefix;

        // Check comment fields.
        add_filter('comments_open', [$this, 'comments_open'], 10, 2);
        add_filter('preprocess_comment', [$this, 'check_comment_rating'], 0);

        // Add comment rating.
        add_action('comment_post', [$this, 'add_comment_rating'], 1);

        // Clear transients.
        add_action('wp_update_comment_count', [$this, 'clear_transients']);

        // Support avatars for `review` comment type.
        add_filter('get_avatar_comment_types', [$this, 'add_avatar_for_review_comment_type']);

        // Set comment type.
        add_filter('preprocess_comment', [$this, 'update_comment_type'], 1);

        // Remove comment meta boxes.
        add_action('admin_menu', [$this, 'remove_meta_boxes']);

        // Clean
        add_action('trashed_post', [$this, 'trash_feature']);
        add_action('untrashed_post', [$this, 'trash_feature']);
        add_action('delete_post', [$this, 'clear_meta']);
    }

    /**
     * Gets comment type.
     *
     * @return string
     */
    public function get_comment_type() {
        return str_replace('opalestate_', '', $this->cpt_support) . '_review';
    }

    /**
     * See if comments are open.
     *
     * @param bool $open Whether the current post is open for comments.
     * @param int $post_id Post ID.
     * @return bool
     */
    public function comments_open($open, $post_id) {
        if ($this->cpt_support === get_post_type($post_id) && !post_type_supports($this->cpt_support, 'comments')) {
            $open = false;
        }

        return $open;
    }

    /**
     * Validate the comment ratings.
     *
     * @param array $comment_data Comment data.
     * @return array
     */
    public function check_comment_rating($comment_data) {
        // If posting a comment (not trackback etc) and not logged in.
        $features = Opalestate_Rating_Helper::get_features($this->cpt_feature);
        if ($features) {
            foreach ($features as $feature_slug => $feature_title) {
                $post = $this->cpt_feature . '_' . $feature_slug;
                if (!is_admin() && isset($_POST['comment_post_ID'], $_POST[$post], $comment_data['comment_type']) && $this->cpt_support === get_post_type(absint($_POST['comment_post_ID'])) && empty($_POST[$post]) && '' === $comment_data['comment_type']) { // WPCS: input var ok, CSRF ok.
                    wp_die(esc_html__('Please rate all features.', 'opalestate-pro'));
                    exit;
                }
            }
        } else {
            if (!is_admin() && isset($_POST['comment_post_ID'], $_POST['opalestate_rating'], $comment_data['comment_type']) && $this->cpt_support === get_post_type(absint($_POST['comment_post_ID'])) && empty($_POST['opalestate_rating']) && '' === $comment_data['comment_type']) { // WPCS: input var ok, CSRF ok.
                wp_die(esc_html__('Please rate.', 'opalestate-pro'));
                exit;
            }
        }

        return $comment_data;
    }

    /**
     * Rating field for comments.
     *
     * @param int $comment_id Comment ID.
     */
    public function add_comment_rating($comment_id) {
        if (!isset($_POST['comment_post_ID']) || ($this->cpt_support !== get_post_type(absint($_POST['comment_post_ID'])))) {
            return;
        }

        $features = Opalestate_Rating_Helper::get_features($this->cpt_feature);
        if ($features) {
            foreach ($features as $feature_slug => $feature_title) {
                $post = $this->cpt_feature . '_' . $feature_slug;
                if (isset($_POST[$post])) {
                    if (!$_POST[$post] || $_POST[$post] > 5 || $_POST[$post] < 0) { // WPCS: input var ok, CSRF ok, sanitization ok.
                        continue;
                    }
                    add_comment_meta($comment_id, $post, intval($_POST[$post]), true); // WPCS: input var ok, CSRF ok.
                }
            }
        } else {
            if (isset($_POST['opalestate_rating'])) { // WPCS: input var ok, CSRF ok.
                if (!$_POST['opalestate_rating'] || $_POST['opalestate_rating'] > 5 || $_POST['opalestate_rating'] < 0) { // WPCS: input var ok, CSRF ok, sanitization ok.
                    return;
                }
                add_comment_meta($comment_id, 'opalestate_rating', intval($_POST['opalestate_rating']), true); // WPCS: input var ok, CSRF ok.
            }
        }

        $post_id = isset($_POST['comment_post_ID']) ? absint($_POST['comment_post_ID']) : 0; // WPCS: input var ok, CSRF ok.
        if ($post_id) {
            $this->clear_transients($post_id);
        }
    }

    /**
     * Make sure WP displays avatars for comments with the `$this->cpt_support` type.
     *
     * @param array $comment_types Comment types.
     * @return array
     */
    public function add_avatar_for_review_comment_type($comment_types) {
        return array_merge($comment_types, [$this->get_comment_type()]);
    }

    /**
     * Ensure property average rating and review count is kept up to date.
     *
     * @param int $post_id Post ID.
     */
    public function clear_transients($post_id) {
        if ($this->cpt_support === get_post_type($post_id)) {
            do_action('opalestate_rating_before_clear_transients', $post_id, $this->cpt_support, $this->cpt_feature);

            update_post_meta($post_id, $this->meta_prefix . 'rating_count', Opalestate_Rating_Helper::get_rating_counts_for_post($post_id, $this->cpt_feature));
            update_post_meta($post_id, $this->meta_prefix . 'average_rating', Opalestate_Rating_Helper::get_average_rating_for_post($post_id, $this->cpt_feature));
            update_post_meta($post_id, $this->meta_prefix . 'review_count', Opalestate_Rating_Helper::get_review_count_for_post($post_id));
            update_post_meta($post_id, $this->meta_prefix . 'rating_count_stats', Opalestate_Rating_Helper::get_rating_count_stats_for_post($post_id, $this->cpt_feature));
            update_post_meta($post_id, $this->meta_prefix . 'rating_average_stats', Opalestate_Rating_Helper::get_rating_average_stats_by_features_for_post($post_id, $this->cpt_feature,
                $this->meta_prefix));

            do_action('opalestate_rating_after_clear_transients', $post_id, $this->cpt_support, $this->cpt_feature);
        }
    }

    /**
     * Update comment type of property reviews.
     *
     * @param array $comment_data Comment data.
     * @return array
     */
    public function update_comment_type($comment_data) {
        if (!is_admin() && isset($_POST['comment_post_ID'], $comment_data['comment_type']) && '' === $comment_data['comment_type'] && $this->cpt_support === get_post_type(absint($_POST['comment_post_ID']))) { // WPCS: input var ok, CSRF ok.
            $comment_data['comment_type'] = $this->get_comment_type();
        }

        return $comment_data;
    }

    public function remove_meta_boxes() {
        // remove_meta_box( 'commentstatusdiv', $this->cpt_support, 'normal' );
        remove_meta_box('commentsdiv', $this->cpt_support, 'normal');
    }

    public function trash_feature($id) {
        if (!$id) {
            return;
        }

        $post_type = get_post_type($id);

        if ($post_type !== $this->cpt_feature) {
            return;
        }

        $this->clean_and_recal();
    }

    public function clear_meta($id) {
        if (!current_user_can('delete_posts') || !$id) {
            return;
        }

        $post_type = get_post_type($id);

        if ($post_type !== $this->cpt_feature) {
            return;
        }

        global $wpdb;
        $feature = get_post($id);
        if (!$feature) {
            return;
        }

        $meta_key = $this->cpt_feature . '_' . str_replace('__trashed', '', $feature->post_name);
        if ($meta_key && (0 !== $meta_key)) {
            $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->commentmeta} WHERE meta_key = %s;", $meta_key));
        }

        $this->clean_and_recal();
    }

    protected function clean_and_recal() {
        $posts = get_posts([
            'post_type'      => $this->cpt_support,
            'posts_per_page' => -1,
            'post_status'    => 'any',
        ]);

        foreach ($posts as $post) {
            $this->clear_transients($post->ID);
        }
    }
}
