<?php

class OpalEstate_User_Statistics {
    public $user_id;

    public function __construct() {
        $this->user_id = get_current_user_id();
    }

    public function get_count_properties() {
        $query = Opalestate_Query::get_properties_by_user([], $this->user_id);

        return $query->found_posts;
    }

    public function get_count_featured() {
        $query = Opalestate_Query::get_properties_by_user([
            'featured' => 1,
        ], $this->user_id);

        return $query->found_posts;
    }

    public function get_count_published_properties() {
        $query = Opalestate_Query::get_properties_by_user([
            'post_status' => 'publish',
        ], $this->user_id);

        return $query->found_posts;
    }

    public function get_count_pending_properties() {
        $query = Opalestate_Query::get_properties_by_user([
            'post_status' => 'pending',
        ], $this->user_id);

        return $query->found_posts;
    }
}
