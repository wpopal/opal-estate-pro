<?php

class Opalestate_Rating {
    public function __construct() {
        $this->includes();
        $this->process();

        // Template loader.
        add_filter('comments_template', [$this, 'comments_template_loader']);

        // Add shortcode User property reviews.
        add_shortcode('opalestate_user_property_reviews', [$this, 'property_reviews_template']);
        add_filter('opalestate_user_content_reviews_page', [$this, 'property_reviews_template']);
    }

    public function includes() {
        require_once 'class-opalestate-rating-features-posttype.php';
        require_once 'class-opalestate-rating-metabox.php';
        require_once 'class-opalestate-rating-helper.php';
        require_once 'class-opalestate-rating-init.php';
        require_once 'rating-functions.php';
        require_once 'rating-hook-functions.php';
    }

    public static function get_rating_supports() {
        return [
            // Support property rating.
            'opalestate_property' => [
                'post_type'    => 'opalestate_property',
                'features_cpt' => 'opalestate_rating_ft',
                'prefix'       => OPALESTATE_PROPERTY_PREFIX,
            ],
            // Support agency rating.
            'opalestate_agency'   => [
                'post_type'    => 'opalestate_agency',
                'features_cpt' => 'opalestate_agency_ft',
                'prefix'       => OPALESTATE_AGENCY_PREFIX,
            ],
            // Support agent rating.
            'opalestate_agent'    => [
                'post_type'    => 'opalestate_agent',
                'features_cpt' => 'opalestate_agent_ft',
                'prefix'       => OPALESTATE_AGENT_PREFIX,
            ],
        ];
    }

    public function process() {
        $rating_supports = static::get_rating_supports();
        foreach ($rating_supports as $key => $support) {
            new Opalestate_Rating_Init($support['post_type'], $support['features_cpt'], $support['prefix']);
        }
    }

    /**
     * Load comments template.
     *
     * @param string $template template to load.
     * @return string
     */
    public function comments_template_loader($template) {
        $supports           = static::get_rating_supports();
        $post_type_supports = array_keys($supports);

        if (!in_array(get_post_type(), $post_type_supports)) {
            return $template;
        }

        $check_dirs = [
            trailingslashit(get_stylesheet_directory()) . 'opalestate/rating/',
            trailingslashit(get_template_directory()) . 'opalestate/rating/',
            trailingslashit(get_stylesheet_directory()),
            trailingslashit(get_template_directory()),
            trailingslashit(OPALESTATE_PLUGIN_DIR) . 'templates/rating/',
        ];

        foreach ($check_dirs as $dir) {
            $file = 'opalestate-ratings.php';
            if (file_exists(trailingslashit($dir) . $file)) {
                return trailingslashit($dir) . $file;
            }
        }
    }

    public function property_reviews_template() {
        if (!is_user_logged_in() || !$current_user_id = get_current_user_id()) {
            return '';
        }

        $args = [
            'post_author__in' => [$current_user_id],
            'status'          => 'approve',
            'type'            => 'property_review',
        ];

        $comments = get_comments($args);

        return opalestate_load_template_path('user/property-ratings', ['comments' => $comments]);
    }
}

new Opalestate_Rating();
