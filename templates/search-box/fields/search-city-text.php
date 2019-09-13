<?php 
$max_price = 10;
$start = 1;
$unit = 'KM';
$location_text = isset( $_GET['location_text'] ) ? sanitize_text_field( $_GET['location_text'] ): "";
$geo_lat  = isset( $_GET['geo_lat'] ) ? sanitize_text_field( $_GET['geo_lat'] ): "";
$geo_long = isset( $_GET['geo_long'] ) ? sanitize_text_field( $_GET['geo_long'] ): "";
$max_geo_radius = isset( $_GET['geo_radius'] ) ? sanitize_text_field( $_GET['geo_radius'] ): $start;
$data_deposit = [
	'id'         => 'geo_radius',
	'decimals'   => 0,
	'unit'       => $unit,
	'ranger_min' => 0,
	'ranger_max' => $max_price,
	'input_min'  => 0,
	'input_max'  => $max_price,
	'mode'       => 1,
	'start'      => $max_geo_radius,
];

?>
<div class="input-search-city opalestate-search-opal-map">
    <label class="opalestate-label opalestate-label--geo-location"><?php esc_html_e( 'Location', 'opalestate-pro' ); ?></label>
	<input class="form-control opal-map-search" name="location_text" value="<?php echo esc_attr( $location_text ); ?>" placeholder="<?php esc_attr_e( 'Type City or Area', 'opalestate-pro' ); ?>">
	<input class="form-control opal-map-latitude" name="geo_lat" value="<?php echo esc_attr( $geo_lat ); ?>" type="hidden">
	<input class="form-control opal-map-longitude" name="geo_long" type="hidden" value="<?php echo esc_attr( $geo_long ); ?>">
	<div class="map-remove"><i class="fa fa-close"></i></div>
	<div class="opalestate-popup opalestate-popup-geo-location">
	    <div class="popup-head">
            <span class="radius-status">
                <span class="radius-status__number"><?php echo absint( $max_geo_radius ); ?></span>
                <span class="radius-status__unit"><?php echo esc_html( $unit ); ?></span>
            </span>
	    	<span><i class="fa fa-location-arrow"></i></span>
	    </div>
	    <div class="popup-body">
	        <div class="popup-close"><i class="fa fa-times" aria-hidden="true"></i></div>
            <div class="contact-share-form-container">
                <h6><?php esc_html_e( 'Show with in', 'opalestate-pro' ); ?></h6>
                <div class="box-content ">
                    <?php opalesate_property_slide_ranger_template( esc_html__( 'Radius', 'opalestate-pro' ), $data_deposit ); ?>
                    <p><?php esc_html_e( 'Of My Location', 'opalestate-pro' ); ?></p>
                </div>
            </div>
	    </div>
	</div>
</div>
