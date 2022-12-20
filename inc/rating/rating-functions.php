<?php
/**
 * Check if reviews are enabled.
 *
 * @return bool
 */
function opalestate_property_reviews_enabled() {
    return 'on' === opalestate_get_option('enable_property_reviews', 'on');
}

/**
 * Check if reviews are enabled.
 *
 * @return bool
 */
function opalestate_agency_reviews_enabled() {
    return 'on' === opalestate_get_option('enable_agency_reviews', 'on');
}

/**
 * Check if reviews are enabled.
 *
 * @return bool
 */
function opalestate_agent_reviews_enabled() {
    return 'on' === opalestate_get_option('enable_agent_reviews', 'on');
}

if (!function_exists('opalestate_comments')) {

    /**
     * Output the Review comments template.
     *
     * @param WP_Comment $comment Comment object.
     * @param array $args Arguments.
     * @param int $depth Depth.
     */
    function opalestate_comments($comment, $args, $depth) {
        $GLOBALS['comment'] = $comment; // WPCS: override ok.
        echo opalestate_load_template_path(
            'rating/review',
            [
                'comment' => $comment,
                'args'    => $args,
                'depth'   => $depth,
            ]
        );
    }
}

if (!function_exists('opalestate_review_display_gravatar')) {
    /**
     * Display the review authors gravatar
     *
     * @param array $comment WP_Comment.
     * @return void
     */
    function opalestate_review_display_gravatar($comment) {
        echo get_avatar($comment, apply_filters('opalestate_review_gravatar_size', '60'), '');
    }
}

if (!function_exists('opalestate_review_display_rating')) {
    /**
     * Display the reviewers star rating
     *
     * @return void
     */
    function opalestate_review_display_rating() {
        echo opalestate_load_template_path('rating/review-rating');
    }
}

if (!function_exists('opalestate_review_display_meta')) {
    /**
     * Display the review authors meta (name, verified owner, review date)
     *
     * @return void
     */
    function opalestate_review_display_meta() {
        echo opalestate_load_template_path('rating/review-meta');
    }
}

if (!function_exists('opalestate_review_display_comment_text')) {

    /**
     * Display the review content.
     */
    function opalestate_review_display_comment_text() {
        echo '<div class="description">';
        comment_text();
        echo '</div>';
    }
}

function opalestate_get_property_rating_features() {
    return Opalestate_Rating_Helper::get_features('opalestate_rating_ft');
}

function opalestate_get_agency_rating_features() {
    return Opalestate_Rating_Helper::get_features('opalestate_agency_ft');
}

function opalestate_get_agent_rating_features() {
    return Opalestate_Rating_Helper::get_features('opalestate_agent_ft');
}
