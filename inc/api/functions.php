<?php
/**
 * Check permissions of posts on REST API.
 *
 * @param string $post_type Post type.
 * @param string $context   Request context.
 * @param int    $object_id Post ID.
 * @return bool
 */
function opalestate_rest_check_post_permissions( $post_type, $context = 'read', $object_id = 0 ) {
	$contexts = [
		'read'   => 'read_private_posts',
		'create' => 'publish_posts',
		'edit'   => 'edit_post',
		'delete' => 'delete_post',
		'batch'  => 'edit_others_posts',
	];

	if ( 'revision' === $post_type ) {
		$permission = false;
	} else {
		$cap              = $contexts[ $context ];
		$post_type_object = get_post_type_object( $post_type );
		$permission       = current_user_can( $post_type_object->cap->$cap, $object_id );
	}

	return apply_filters( 'opalestate_rest_check_permissions', $permission, $context, $object_id, $post_type );
}

/**
 * The opalestate_property post object, generate the data for the API output
 *
 * @param object $property_info The Download Post Object
 *
 * @return array                Array of post data to return back in the API
 * @since  1.0
 *
 */
function opalestate_api_get_property_data( $property_info ) {
	$property['id']            = $property_info->ID;
	$property['name']          = $property_info->post_title;
	$property['slug']          = $property_info->post_name;
	$property['created_date']  = $property_info->post_date;
	$property['modified_date'] = $property_info->post_modified;
	$property['status']        = $property_info->post_status;
	$property['permalink']     = html_entity_decode( $property_info->guid );
	$property['content']       = $property_info->post_content;
	$property['thumbnail']     = wp_get_attachment_url( get_post_thumbnail_id( $property_info->ID ) );

	$data          = opalesetate_property( $property_info->ID );
	$gallery       = $data->get_gallery();
	$gallery_count = $data->get_gallery_count();

	$gallery_data = [];
	if ( $gallery_count ) {
		foreach ( $gallery as $id => $url ) {
			$gallery_data[] = [
				'id'  => $id,
				'url' => $url,
			];
		}
	}

	$property['gallery']            = $gallery_data;
	$property['price']              = opalestate_price_format( $data->get_price() );
	$property['saleprice']          = opalestate_price_format( $data->get_sale_price() );
	$property['before_price_label'] = $data->get_before_price_label();
	$property['price_label']        = $data->get_price_label();
	$property['featured']           = $data->is_featured();
	$property['map']                = $data->get_map();
	$property['address']            = $data->get_address();
	$property['short_info']         = $data->get_meta_shortinfo();
	$property['full_info']          = $data->get_meta_fullinfo();
	$property['video']              = $data->get_video_url();
	$property['virtual_tour']       = $data->get_virtual_tour();
	$property['attachments']        = $data->get_attachments();
	$property['floor_plans']        = $data->get_floor_plans();
	$property['statuses']           = $data->get_status();
	$property['labels']             = $data->get_labels();
	$property['locations']          = $data->get_locations();
	$property['facilities']         = $data->get_facilities();
	$property['amenities']          = $data->get_amenities();
	$property['types']              = $data->get_types_tax();
	$property['author_type']        = $data->get_author_type();
	$property['author_data']        = $data->get_author_link_data();

	$limit                  = opalestate_get_option( 'single_views_statistics_limit', 8 );
	$stats                  = new Opalestate_View_Stats( $data->get_id(), $limit );
	$array_label            = json_encode( $stats->get_traffic_labels() );
	$array_values           = json_encode( $stats->get_traffic_data_accordion() );
	$property['view_stats'] = [
		'labels' => $array_label,
		'values' => $array_values,
	];

	return apply_filters( 'opalestate_api_properties_property', $property );
}

/**
 * Generate a rand hash.
 *
 * @return string
 */
function opalestate_rand_hash() {
	if ( ! function_exists( 'openssl_random_pseudo_bytes' ) ) {
		return sha1( wp_rand() );
	}

	return bin2hex( openssl_random_pseudo_bytes( 20 ) ); // @codingStandardsIgnoreLine
}

/**
 * Opalestate API - Hash.
 *
 * @param  string $data Message to be hashed.
 * @return string
 */
function opalestate_api_hash( $data ) {
	return hash_hmac( 'sha256', $data, 'estate-api' );
}

/**
 * Encodes a value according to RFC 3986.
 * Supports multidimensional arrays.
 *
 * @param string|array $value The value to encode.
 * @return string|array       Encoded values.
 */
function opalestate_rest_urlencode_rfc3986( $value ) {
	if ( is_array( $value ) ) {
		return array_map( 'opalestate_rest_urlencode_rfc3986', $value );
	}

	return str_replace( array( '+', '%7E' ), array( ' ', '~' ), rawurlencode( $value ) );
}
