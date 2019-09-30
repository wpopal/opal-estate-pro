<div class="ajax-map-search-split full-width">
    <div class="inner">
        <div class="opal-row">
            <div class="col-lg-6 col-md-12 col-sm-12 split-maps-container">
                <div id="opalestate-map-preview" style="height:800px;" data-page="<?php echo esc_attr( $paged ); ?>">
                    <div id="mapView">
                        <div class="mapPlaceholder"><span class="fa fa-spin fa-spinner"></span> <?php esc_html_e( 'Loading map...', 'opalestate-pro' ); ?>
                            <div class="sk-folding-cube">
                                <div class="sk-cube1 sk-cube"></div>
                                <div class="sk-cube2 sk-cube"></div>
                                <div class="sk-cube4 sk-cube"></div>
                                <div class="sk-cube3 sk-cube"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 col-sm-12 pull-right">
                <div class="split-search-container">
                    <div class="ajax-search-form">
						<?php echo opalestate_load_template_path( 'search-box/'.$search_form, [ 'nobutton' => true ] ); ?>
                    </div>

                    <div class="opalesate-properties-ajax opalesate-properties-results" data-mode="html">
						<?php echo opalestate_load_template_path( 'shortcodes/ajax-map-search-result', [ 'column' => 2 ] ); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
