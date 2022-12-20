<?php

/**
 * Class Opalestate_Rating_Helper
 */
class Opalestate_Rating_Helper {
    public static function get_average_rating($comment, $cpt_feature) {
        $features = static::get_features($cpt_feature);
        if ($features) {
            $sum = 0;
            foreach ($features as $feature_slug => $feature_title) {
                $sum += absint(get_comment_meta($comment->comment_ID, $cpt_feature . '_' . $feature_slug, true));
            }

            $average = number_format($sum / count($features), 1);
        } else {
            $average = absint(get_comment_meta($comment->comment_ID, 'opalestate_rating', true));
        }

        return $average;
    }

    /**
     * Get property rating for a property. Please note this is not cached.
     *
     * @return float
     */
    public static function get_average_rating_for_post($post_id, $cpt_feature) {
        $comments = get_comments(['post_id' => $post_id, 'status' => 'approve']);
        if (!$comments) {
            return 0;
        }

        $sum = 0;
        foreach ($comments as $comment) {
            $sum += static::get_average_rating($comment, $cpt_feature);
        }

        return number_format($sum / static::get_review_count_for_post($post_id), 2);
    }

    /**
     * Get property review count for a property (not replies). Please note this is not cached.
     */
    public static function get_review_count_for_post($post_id) {
        global $wpdb;

        $count = $wpdb->get_var(
            $wpdb->prepare(
                "
			SELECT COUNT(*) FROM $wpdb->comments
			WHERE comment_parent = 0
			AND comment_post_ID = %d
			AND comment_approved = '1'
				",
                $post_id
            )
        );

        return $count;
    }

    /**
     * Get property rating count for a property. Please note this is not cached.
     */
    public static function get_rating_counts_for_post($post_id, $cpt_feature) {
        $features = static::get_features($cpt_feature);
        if ($features) {
            return static::get_review_count_for_post($post_id) * count($features);
        }

        return static::get_review_count_for_post($post_id);
    }

    public static function get_rating_count_stats_for_post($post_id, $cpt_feature) {
        $output = [
            5 => 0,
            4 => 0,
            3 => 0,
            2 => 0,
            1 => 0,
        ];

        $features = static::get_features($cpt_feature);

        for ($i = 5; $i >= 1; $i--) {
            $args = [
                'post_id' => $post_id,
                'count'   => true,
                'status'  => 'approve',
            ];

            if ($features) {
                $features_query = [];
                foreach ($features as $feature_slug => $feature_title) {
                    $features_query[] = [
                        'key'   => $cpt_feature . '_' . $feature_slug,
                        'value' => $i,
                    ];
                }
                $args['meta_query']             = $features_query;
                $args['meta_query']['relation'] = 'OR';
            } else {
                $args['meta_query'] = [
                    [
                        'key'   => 'opalestate_rating',
                        'value' => $i,
                    ],
                ];
            }

            $output[$i] = get_comments($args);
        }

        return $output;
    }

    public static function get_rating_average_stats_by_features_for_post($post_id, $cpt_feature, $meta_prefix) {
        global $wpdb;

        $output   = [];
        $count    = get_post_meta($post_id, $meta_prefix . 'review_count', true);
        $features = static::get_features($cpt_feature);

        if (!$features || !$count) {
            return $output;
        }

        foreach ($features as $feature_slug => $feature_title) {
            $meta_key = $cpt_feature . '_' . $feature_slug;

            $ratings               = $wpdb->get_var(
                $wpdb->prepare(
                    "
				SELECT SUM(meta_value) FROM $wpdb->commentmeta
				LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
				WHERE meta_key = %s
				AND comment_post_ID = %d
				AND comment_approved = '1'
				AND meta_value > 0
					",
                    $meta_key,
                    $post_id
                )
            );
            $average               = number_format($ratings / $count, 2, '.', '');
            $output[$feature_slug] = $average;
        }

        return $output;
    }

    public static function get_features($cpt_feature, $posts_per_page = -1) {
        $args = [
            'post_type'      => $cpt_feature,
            'post_status'    => 'publish',
            'posts_per_page' => $posts_per_page,
            'order'          => 'ASC',
            'orderby'        => 'meta_value_num',
            'meta_key'       => 'opalestate_feature_order',
        ];

        $features = get_posts($args);

        return wp_list_pluck($features, 'post_title', 'post_name');
    }
}

new Opalestate_Rating_Helper();
