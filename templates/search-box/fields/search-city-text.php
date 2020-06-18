<?php
$max_range      = isset( $max_range ) && $max_range ? $max_range : apply_filters( 'opalestate_search_geo_max_range', 10 );
$range_unit     = isset( $range_unit ) ? $range_unit : 'km';
$start          = 1;
$location_text  = isset( $_GET['location_text'] ) ? sanitize_text_field( $_GET['location_text'] ) : '';
$geo_lat        = isset( $_GET['geo_lat'] ) ? sanitize_text_field( $_GET['geo_lat'] ) : '';
$geo_long       = isset( $_GET['geo_long'] ) ? sanitize_text_field( $_GET['geo_long'] ) : '';
$max_geo_radius = isset( $_GET['geo_radius'] ) ? sanitize_text_field( $_GET['geo_radius'] ) : $start;
$radius_measure = isset( $_GET['radius_measure'] ) ? sanitize_text_field( $_GET['radius_measure'] ) : $range_unit;

$data_range = [
	'id'         => 'geo_radius',
	'decimals'   => 0,
	'unit'       => $range_unit,
	'ranger_min' => 0,
	'ranger_max' => $max_range,
	'input_min'  => 0,
	'input_max'  => $max_range,
	'mode'       => 1,
	'start'      => $max_geo_radius,
];

?>
<div class="input-search-city opalestate-search-opal-map">
    <label class="opalestate-label opalestate-label--geo-location"><?php esc_html_e( 'Location', 'opalestate-pro' ); ?></label>
    <input class="form-control opal-map-search" name="location_text" value="<?php echo esc_attr( $location_text ); ?>" placeholder="<?php esc_attr_e( 'Type City or Area', 'opalestate-pro' ); ?>">
    <input class="opal-map-latitude" name="geo_lat" type="hidden" value="<?php echo esc_attr( $geo_lat ); ?>">
    <input class="opal-map-longitude" name="geo_long" type="hidden" value="<?php echo esc_attr( $geo_long ); ?>">
    <input class="opal-map-radius-measure" name="radius_measure" type="hidden" value="<?php echo esc_attr( $radius_measure ); ?>">
    <div class="map-remove"><i class="fa fa-close"></i></div>
    <div class="opalestate-popup opalestate-popup-geo-location">
        <div class="popup-head">
            <span class="radius-status">
                <span class="radius-status__number"><?php echo absint( $max_geo_radius ); ?></span>
                <span class="radius-status__unit"><?php echo esc_html( $range_unit ); ?></span>
            </span>
            <span><i class="fa fa-location-arrow"></i></span>
        </div>
        <div class="popup-body">
            <div class="popup-close"><i class="fa fa-times" aria-hidden="true"></i></div>
            <div class="contact-share-form-container">
                <h6><?php esc_html_e( 'Show with in', 'opalestate-pro' ); ?></h6>
                <div class="box-content ">
					<?php opalesate_property_slide_ranger_template( esc_html__( 'Radius', 'opalestate-pro' ), $data_range ); ?>
                    <p><?php esc_html_e( 'Of My Location', 'opalestate-pro' ); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
