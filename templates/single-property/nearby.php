<?php
global $property;

if ( 'on' !== $property->get_block_setting( 'nearby' ) ) {
	return;
}

if ( ! Opalestate_Yelp::get_client_id() || ! Opalestate_Yelp::get_app_key() ) {
	return;
}

$categories = Opalestate_Yelp::get_categories();
if ( ! $categories ) {
	return;
}

$map = $property->get_map();

if ( ! $map || ! is_array( $map ) || ! isset( $map['latitude'] ) || ! isset( $map['longitude'] ) ) {
	return;
}

$latitude  = $map['latitude'];
$longitude = $map['longitude'];
if ( ! $latitude || ! $longitude ) {
	return;
}

?>
<div class="opalestate-box-content property-nearby-session">
    <h4 class="outbox-title" id="block-nearby"><?php esc_html_e( 'What\'s nearby', 'opalestate-pro' ); ?></h4>
    <div class="opalestate-box">
        <div class="box-info">
            <div id="opalestate-yelp" class="loading"></div>
            <script>
                jQuery( document ).ready( function () {
                    function opalestate_load_yelp_places( property_id ) {
                        jQuery.ajax( {
                            type: 'POST',
                            dataType: 'json',
                            url: opalesateJS.ajaxurl,
                            data: 'action=opalestate_load_yelp_places&property_id=' + property_id,
                            success: function ( response ) {
                                jQuery( '#opalestate-yelp' ).removeClass( 'loading' ).html( response.result );
                                jQuery( '#opalestate-yelp' ).html( response.result );
                            },
                            error: function ( response ) {
                            }
                        } );
                    }

                    opalestate_load_yelp_places(<?php echo absint( $property->get_id() ); ?>);
                } );
            </script>
        </div>
    </div>
</div>
