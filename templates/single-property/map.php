<?php
global $property;

if ( 'on' !== $property->get_block_setting( 'map' ) ) {
	return;
}

$maps = $property->get_map();

if ( empty( $maps ) ) {
	return;
}
$id = time();
?>
<div class="opalestate-box-content property-map-section">
    <h4 class="outbox-title" id="block-map"><?php esc_html_e( 'Property on Map', 'opalestate-pro' ); ?></h4>
    <div class="opalestate-box">
        <div class="box-content">


                <div class="google-map-tabs opalestate-tab">
                    <div class="opalestate-tab-wrap" >

                        <div class="nav opalestate-tab-head" role="tablist">
                            <a aria-expanded="false" href="#tab-google-map" role="tab" class="tab-item"><i class="fa fa-map"></i> <span><?php esc_html_e( 'Map', 'opalestate-pro' ); ?></span></a>
                            <?php if ( $property->enable_google_mapview() ) : ?>
                                <a aria-expanded="true" href="#property-street-view-map" role="tab" class="tab-item tab-google-street-view-btn"><i class="fa fa-street-view"></i>
                                    <span><?php esc_html_e( 'Street View', 'opalestate-pro' ); ?></span></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>    
                <div class="opalestate-tabs-content">
                    <div class="opalestate-tab-content" id="tab-google-map">
                        <div class="property-map-section" style="position: relative;">

                       
                        <div  id="property-map<?php echo esc_attr($id); ?>" class="property-preview-map"  style="height:500px" data-latitude="<?php echo( isset( $maps['latitude'] ) ? $maps['latitude'] : '' ); ?>"
                             data-longitude="<?php echo( isset( $maps['longitude'] ) ? $maps['longitude'] : '' ); ?>" data-icon="<?php echo esc_url( OPALESTATE_CLUSTER_ICON_URL ); ?>"></div>

                                                    
                           <div id="property-search-places" class="property-search-places">
                                <div class="place-buttons">
                                    <div class="nearby-container">
                                        <div class="btn-map-search" data-group="hospital" data-type="hospital" data-icon="hospital.png">
                                            <i class="fa fa-hospital-o" aria-hidden="true"></i>
                                            <span><?php esc_html_e( 'Hospital', 'opalestate-pro' ); ?></span>
                                        </div>
                                    </div>
                                    <div class="nearby-container">
                                        <div class="btn-map-search" data-group="library" data-type="library" data-icon="libraries.png">
                                            <i class="fa fa-bank" aria-hidden="true"></i>
                                            <span><?php esc_html_e( 'Library', 'opalestate-pro' ); ?></span>
                                        </div>
                                    </div>
                                    <div class="nearby-container">
                                        <div class="btn-map-search" data-group="pharmacy" data-type="pharmacy" data-icon="pharmacy.png">
                                            <i class="fa fa-plus-square" aria-hidden="true"></i>
                                            <span><?php esc_html_e( 'Pharmacy', 'opalestate-pro' ); ?></span>
                                        </div>
                                    </div>
                                    <div class="nearby-container">

                                        <div class="btn-map-search" data-group="school" data-type="school, university" data-icon="school.png">
                                            <i class="fa fa-graduation-cap" aria-hidden="true"></i>
                                            <span><?php esc_html_e( 'School', 'opalestate-pro' ); ?></span>
                                        </div>
                                    </div>

                                    <div class="nearby-container">
                                        <div class="btn-map-search" data-group="shopping" data-type="grocery_or_supermarket, shopping_mall" data-icon="supermarket.png">
                                            <i class="fa fa-shopping-basket" aria-hidden="true"></i>
                                            <span><?php esc_html_e( 'Shopping', 'opalestate-pro' ); ?></span>
                                        </div>
                                    </div>
                                    <div class="nearby-container">
                                        <div class="btn-map-search" data-group="trainstation" data-type="bus_station', subway_station, train_station, airport" data-icon="transportation.png">
                                            <i class="fa fa-subway" aria-hidden="true"></i>
                                            <span><?php esc_html_e( 'Trainstation', 'opalestate-pro' ); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>    
                    </div>

                    <?php if ( $property->enable_google_mapview() ) : ?>
                        <div class="opalestate-tab-content" id="property-street-view-map" style="height:500px;">
                                <div class="property-preview property-preview-street-map" data-latitude="<?php echo (isset($maps['latitude']) ? $maps['latitude'] : ''); ?>" data-longitude="<?php echo (isset($maps['longitude']) ? $maps['longitude'] : ''); ?>" id="property-streep-map-<?php echo esc_attr($id); ?>">
                                </div>
                        </div>
                    <?php endif; ?>
                </div>
        </div>
    </div>
</div>


