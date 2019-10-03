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
	var_dump($post_type_object->cap->$cap);

	return apply_filters( 'opalestate_rest_check_permissions', $permission, $context, $object_id, $post_type );
}
