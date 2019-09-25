<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $post, $property;

$type   = $property->get_author_type();
$data   = get_userdata( $post->post_author );
$layout = 'list';
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
<div class="opalestate-box-content property-agent-section">
    <div class="opalestate-box">
		<?php if ( opalestate_get_option( 'enable_single_author_box' ) ) : ?>
            <div class="author-content-box">
                <div class="property-agent-info">
					<?php echo wp_kses_post( $author_info ); ?>
                </div>
            </div>
		<?php endif; ?>

		<?php if ( opalestate_get_option( 'enable_single_enquire_form' ) ) : ?>
			<?php echo opalestate_load_template_path( 'messages/enquiry-form', [ 'nowrap' => true ] ); ?>
		<?php endif; ?>
    </div>
</div>
