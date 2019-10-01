<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post, $property;


if (  opalestate_get_option( 'enable_single_author_box' , 'on') != 'on' ) {
	return;
}

$type   = $property->get_author_type();
$data   = get_userdata( $post->post_author );
$layout = '';
switch ( $type ) {
	case 'hide':
		return;
		break;
	case 'agent' :
		$agent_id = $property->get_metabox_value( 'agent' );

		$author_info = opalestate_load_template_path( 'single-property/user/author-member-box', [
			'author'           => $data,
			'id'               => $agent_id,
			'prefix'           => OPALESTATE_AGENT_PREFIX,
			'picture'          => '',
			'type'             => 'agent',
			'hide_description' => true,
		] );
		break;
	case 'agency' :
		$agency_id   = $property->get_metabox_value( 'agency' );
		$author_info = opalestate_load_template_path( 'single-property/user/author-member-box', [
			'author'           => $data,
			'id'               => $agency_id,
			'picture'          => '',
			'type'             => 'agency',
			'hide_description' => true,
		] );
		break;
	default:
		$author_info = opalestate_load_template_path( 'single-property/user/author-user-box', [
			'author'           => $data,
			'hide_description' => true,
		],
			$layout
		);

		break;
}
?>

<div class="opalestate-box-content property-agent-section property-author-v2">
    <div class="opalestate-box">
        <div class="author-content-box">
            <div class="property-agent-info">
				<?php echo wp_kses_post( $author_info ); ?>
            </div>
        </div>
    </div>
</div>
