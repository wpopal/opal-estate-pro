<?php
global $property;
$maps = $property->get_map();

if ( ! empty( $maps ) ):

	$id = time();

	?>
    <div class="opalestate-preview opalestate-preview-tabs">
        <div class="google-map-tabs opalestate-tab" data-active="tab-<?php echo esc_attr( $tab_active ); ?>">
            <div class="opalestate-tab-wrap">

                <div class="nav opalestate-tab-head" role="tablist">
                    <a aria-expanded="false" href="#tab-gallery-slider" role="tab" class="tab-item"><i class="fa fa-picture-o"></i> <span><?php esc_html_e( 'Gallery', 'opalestate-pro' ); ?></span></a>

                    <a aria-expanded="false" href="#tab-google-map" role="tab" class="tab-item"><i class="fa fa-map"></i> <span><?php esc_html_e( 'Map', 'opalestate-pro' ); ?></span></a>
					<?php if ( $property->enable_google_mapview() ) : ?>
                        <a aria-expanded="true" href="#tab-street-view-map" role="tab" class="tab-item tab-google-street-view-btn" data-target=".tab-google-street-view-btn"><i
                                    class="fa fa-street-view"></i> <span><?php esc_html_e( 'Street View', 'opalestate-pro' ); ?></span></a>
					<?php endif; ?>
                </div>
            </div>

            <div class="opalestate-map-content">
                <div class="opalestate-tab-content" id="tab-gallery-slider">
					<?php echo opalestate_load_template_path( 'single-property/preview/gallery-slider' ); ?>
                </div>

                <div class="opalestate-tab-content" id="tab-google-map">
                    <div class="property-preview property-preview-custom-size">

                        <div id="property-map<?php echo esc_attr( $id ); ?>" class="property-preview-map" data-latitude="<?php echo( isset( $maps['latitude'] ) ? $maps['latitude'] : '' ); ?>"
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

                                    <div class="btn-map-search" data-group="school" data-type="school" data-icon="school.png">
                                        <i class="fa fa-graduation-cap" aria-hidden="true"></i>
                                        <span><?php esc_html_e( 'School', 'opalestate-pro' ); ?></span>
                                    </div>
                                </div>

                                <div class="nearby-container">
                                    <div class="btn-map-search" data-group="shopping" data-type="grocery_or_supermarket" data-icon="supermarket.png">
                                        <i class="fa fa-shopping-basket" aria-hidden="true"></i>
                                        <span><?php esc_html_e( 'Shopping', 'opalestate-pro' ); ?></span>
                                    </div>
                                </div>
                                <div class="nearby-container">
                                    <div class="btn-map-search" data-group="trainstation" data-type="bus_station" data-icon="transportation.png">
                                        <i class="fa fa-subway" aria-hidden="true"></i>
                                        <span><?php esc_html_e( 'Trainstation', 'opalestate-pro' ); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
				<?php if ( $property->enable_google_mapview() ) : ?>

                    <div class="opalestate-tab-content" id="tab-street-view-map">
                        <div class="property-preview property-preview-custom-size property-preview-street-map" style="min-height:580px"
                             data-latitude="<?php echo( isset( $maps['latitude'] ) ? $maps['latitude'] : '' ); ?>"
                             data-longitude="<?php echo( isset( $maps['longitude'] ) ? $maps['longitude'] : '' ); ?>" id="property-streep-map-<?php echo esc_attr( $id ); ?>">
                        </div>
                    </div>

				<?php endif; ?>
            </div>
        </div>
    </div>

<?php endif; ?>
