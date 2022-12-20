<?php
function opalestate_get_message_by_user($args = []) {
    return [
        'items' => OpalEstate_User_Message::get_instance()->get_list($args),
        'total' => OpalEstate_User_Message::get_instance()->get_total($args),
    ];
}

function opalestate_get_member_email_data($post_id) {
    $type = get_post_meta($post_id, OPALESTATE_PROPERTY_PREFIX . 'author_type', true);

    $receiver_id = 0;
    switch ($type) {
        case 'agent':
            $agent_id = get_post_meta($post_id, OPALESTATE_PROPERTY_PREFIX . 'agent', true);
            if (!$agent_id) {
                $post        = get_post($post_id);
                $user        = get_user_by('id', $post->post_author);
                $email       = $user->data->user_email;
                $name        = $user->data->display_name;
                $receiver_id = $post->post_author;
            } else {
                $user_id = get_post_meta($agent_id, OPALESTATE_AGENT_PREFIX . 'user_id', true);
                if ($user_id) {
                    $post        = get_post($post_id);
                    $user        = get_user_by('id', $user_id);
                    $email       = $user->data->user_email;
                    $name        = $user->data->display_name;
                    $receiver_id = $post->post_author;
                } else {
                    $post  = get_post($agent_id);
                    $name  = $post->post_title;
                    $email = get_post_meta($agent_id, OPALESTATE_AGENT_PREFIX . 'email', true);
                }
            }

            break;

        case 'agency':
            $agency_id = get_post_meta($post_id, OPALESTATE_PROPERTY_PREFIX . 'agency', true);

            if (!$agency_id) {
                $post        = get_post($post_id);
                $user        = get_user_by('id', $post->post_author);
                $email       = $user->data->user_email;
                $name        = $user->data->display_name;
                $receiver_id = $post->post_author;
            } else {
                $user_id = get_post_meta($agency_id, OPALESTATE_AGENCY_PREFIX . 'user_id', true);
                if ($user_id) {
                    $post        = get_post($post_id);
                    $user        = get_user_by('id', $user_id);
                    $email       = $user->data->user_email;
                    $name        = $user->data->display_name;
                    $receiver_id = $post->post_author;
                } else {
                    $post  = get_post($agency_id);
                    $name  = $post->post_title;
                    $email = get_post_meta($agency_id, OPALESTATE_AGENCY_PREFIX . 'email', true);
                }
            }

            break;
        default:
            $post        = get_post($post_id);
            $user        = get_user_by('id', $post->post_author);
            $email       = $user->data->user_email;
            $name        = $user->data->display_name;
            $receiver_id = $post->post_author;

            break;
    }

    return [
        'receiver_email' => $email,
        'receiver_name'  => $name,
        'receiver_id'    => $receiver_id,
    ];
}
