<?php
global $property;
$maps = $property->get_map();

if ( !empty($maps) ):
$id = time();
?>
<div class="property-preview property-preview-custom-size">
 
          <div  id="property-map<?php echo esc_attr($id); ?>" class="property-preview-map"  data-latitude="<?php echo (isset($maps['latitude']) ? $maps['latitude'] : ''); ?>" data-longitude="<?php echo (isset($maps['longitude']) ? $maps['longitude'] : ''); ?>" data-icon="<?php echo esc_url(OPALESTATE_CLUSTER_ICON_URL);?>"></div>

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
<?php endif;?>
