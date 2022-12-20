<?php
/**
 * OpalEstate_Agent
 *
 * @package    opalestate
 * @author     Opal  Team <info@wpopal.com >
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * @class   OpalEstate_Agent
 *
 * @version 1.0
 */
class OpalEstate_Agent {

    /**
     * @var String $author_name
     *
     * @access protected
     */
    protected $author_name;

    /**
     * @var Boolean $is_featured
     *
     * @access protected
     */
    protected $is_featured;

    /**
     * @var Boolean $is_featured
     *
     * @access protected
     */
    public $post_id;

    /**
     *  Constructor
     */
    public function __construct($post_id = null) {
        global $post;

        if ($post) {
            $this->post_id     = $post_id ? $post_id : get_the_ID();
            $this->post        = $post;
            $this->author      = get_userdata($post->post_author);
            $this->author_name = !empty($this->author) ? sprintf('%s %s', $this->author->first_name, $this->author->last_name) : null;
        } else {
            $this->post_id     = $post_id;
            $this->post        = get_post($post_id);
            $this->author      = get_userdata($this->post->post_author);
            $this->author_name = !empty($this->author) ? sprintf('%s %s', $this->author->first_name, $this->author->last_name) : null;
        }

        $this->is_featured = $this->get_meta('featured');
        $this->is_trusted  = $this->get_meta('trusted');
    }

    public function get_id() {
        return $this->post_id;
    }

    /**
     * Get Collection Of soicals with theirs values
     */
    public function get_socials() {
        $socials = [
            'facebook'  => '',
            'twitter'   => '',
            'pinterest' => '',
            'google'    => '',
            'instagram' => '',
            'linkedIn'  => '',
        ];

        $output = [];

        foreach ($socials as $social => $k) {
            $data = $this->get_meta($social);
            if ($data && $data != "#" && !empty($data)) {
                $output[$social] = $data;
            }
        }

        return $output;
    }

    /**
     * Get url of user avatar by agent id
     */
    public static function get_avatar_url($userID, $size = 'thumbnail') {
        $id = get_post_meta($userID, OPALESTATE_AGENT_PREFIX . 'avatar_id', true);;
        $url = wp_get_attachment_image_url($id, $size);

        if ($url) {
            return $url;
        }
    }

    /**
     * Render list of levels of agent
     */
    public function render_level() {
        $levels = wp_get_post_terms($this->get_id(), 'opalestate_agent_level');

        if (empty($levels)) {
            return;
        }

        $output = '<span class="agent-levels">';
        foreach ($levels as $key => $value) {
            $output .= '<span class="agent-label"><span>' . $value->name . '</span></span>';
        }
        $output .= '</span>';

        echo $output;
    }

    /**
     * get meta data value of key without prefix
     */
    public function get_meta($key) {
        return get_post_meta($this->get_id(), OPALESTATE_AGENT_PREFIX . $key, true);
    }


    /**
     *  return true if this agent is featured
     */
    public function is_featured() {
        return 'on' === $this->is_featured;
    }

    /**
     *  render block information by id
     */
    public static function render_box_info($post_id) {

    }

    public static function get_link($agent_id) {
        $agent = get_post($agent_id);

        if (!$agent) {
            return [
                'name'   => '',
                'avatar' => '',
                'link'   => '',
            ];
        }

        $url = self::get_avatar_url($agent_id);

        return [
            'name'   => $agent->post_title,
            'avatar' => $url,
            'link'   => get_permalink($agent->ID),
        ];
    }

    public static function metaboxes_fields() {
        $metabox = new Opalestate_Agent_MetaBox();
        $fields  = $metabox->metaboxes_admin_fields();

        return array_merge_recursive($fields, $metabox->get_social_fields(OPALESTATE_AGENT_PREFIX));
    }

    /**
     * @return mixed
     */
    public function get_trusted() {
        return 'on' === $this->is_trusted;
    }

    /**
     * Update user data.
     *
     * @param int $user_id User ID.
     */
    public static function update_user_data($user_id) {
        $fields = self::metaboxes_fields();

        $others = [
            'avatar_id'          => '',
            'opalestate_agt_map' => '',
            'map'                => '',
        ];

        foreach ($fields as $key => $field) {
            $kpos = $field['id'];
            $tmp  = str_replace(OPALESTATE_AGENT_PREFIX, '', $field['id']);
            if (isset($_POST[$kpos]) && $tmp) {
                $data = is_string($_POST[$kpos]) ? sanitize_text_field($_POST[$kpos]) : $_POST[$kpos];
                update_user_meta($user_id, OPALESTATE_USER_PROFILE_PREFIX . $tmp, $data);
            }
        }

        static::update_user_object_terms($user_id);

        // update for others
        foreach ($others as $key => $value) {
            $kpos = OPALESTATE_AGENT_PREFIX . $key;
            if (isset($_POST[$kpos])) {
                $data = is_string($_POST[$kpos]) ? sanitize_text_field($_POST[$kpos]) : $_POST[$kpos];
                update_user_meta($user_id, OPALESTATE_USER_PROFILE_PREFIX . $key, $data);
            }
        }
    }

    /**
     * Update data from user.
     *
     * @param int $related_id Post ID.
     */
    public static function update_data_from_user($related_id) {
        $fields = self::metaboxes_fields();

        $others = [
            'avatar_id' => '',
            'map'       => '',
        ];

        foreach ($fields as $key => $field) {
            $tmp  = str_replace(OPALESTATE_AGENT_PREFIX, '', $field['id']);
            $kpos = OPALESTATE_USER_PROFILE_PREFIX . $tmp;

            if (isset($_POST[$kpos]) && $tmp) {
                $data = is_string($_POST[$kpos]) ? sanitize_text_field($_POST[$kpos]) : $_POST[$kpos];
                update_post_meta($related_id, OPALESTATE_AGENT_PREFIX . $tmp, $data);
            }
        }

        static::update_post_object_terms($related_id);

        // update for others
        foreach ($others as $key => $value) {
            $kpos = OPALESTATE_USER_PROFILE_PREFIX . $key;
            if (isset($_POST[$kpos])) {
                $data = is_string($_POST[$kpos]) ? sanitize_text_field($_POST[$kpos]) : $_POST[$kpos];
                update_post_meta($related_id, OPALESTATE_AGENT_PREFIX . $key, $data);
            }
        }

        do_action('opalestate_update_agent_data_from_user', $related_id);
    }

    /**
     * Update object terms.
     *
     * @param int $related_id Post ID.
     */
    public static function update_post_object_terms($related_id) {
        $terms = [
            'location',
            'state',
            'city',
        ];

        foreach ($terms as $term) {
            if (isset($_POST[OPALESTATE_USER_PROFILE_PREFIX . $term])) {
                wp_set_object_terms($related_id, $_POST[OPALESTATE_USER_PROFILE_PREFIX . $term], 'opalestate_' . $term);
            }
        }
    }

    /**
     * Update object terms.
     *
     * @param int $related_id Post ID.
     */
    public static function update_user_object_terms($user_id) {
        $terms = [
            'location',
            'state',
            'city',
        ];

        foreach ($terms as $term) {
            if (isset($_POST[OPALESTATE_AGENT_PREFIX . $term])) {
                wp_set_object_terms($user_id, $_POST[OPALESTATE_AGENT_PREFIX . $term], 'opalestate_' . $term);
            }
        }
    }

    /**
     * Get rating count.
     *
     * @param string $context What the value is for. Valid values are view and edit.
     * @return int
     */
    public function get_rating_counts() {
        return $this->get_meta('rating_count') ? $this->get_meta('rating_count') : 0;
    }

    /**
     * Get average rating.
     *
     * @param string $context What the value is for. Valid values are view and edit.
     * @return float
     */
    public function get_average_rating() {
        return $this->get_meta('average_rating') ? $this->get_meta('average_rating') : 0;
    }

    /**
     * Get review count.
     *
     * @param string $context What the value is for. Valid values are view and edit.
     * @return int
     */
    public function get_review_count() {
        return $this->get_meta('review_count') ? $this->get_meta('review_count') : 0;
    }

    /**
     *
     */
    public function get_rating_count_stats() {
        return $this->get_meta('rating_count_stats') ? $this->get_meta('rating_count_stats') : [
            5 => 0,
            4 => 0,
            3 => 0,
            2 => 0,
            1 => 0,
        ];
    }

    /**
     *
     */
    public function get_rating_average_stats() {
        return $this->get_meta('rating_average_stats');
    }

    public function get_count_listing() {
        return 4;
    }
}
