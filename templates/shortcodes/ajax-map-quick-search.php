<?php
$paged     = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
$rowcls    = apply_filters( 'opalestate_row_container_class', 'opal-row' );
$slocation = isset( $_GET['location'] ) ? sanitize_text_field( $_GET['location'] ) : opalestate_get_session_location_val();
$stypes    = isset( $_GET['types'] ) ? sanitize_text_field( $_GET['types'] ) : -1;
?>
<div class="ajax-map-search full-width">
    <div class="inner">
        <div class="ajax-search-form">
            <form id="opalestate-search-form" class="opalestate-search-form" method="get">
                <div class="<?php echo $rowcls; ?>">
                    <div class="col-lg-3 col-sm-3">
                        <input class="form-control" name="search_text">
                    </div>
                    <div class="col-lg-2 col-sm-3">
						<?php Opalestate_Taxonomy_Location::dropdown_list( $slocation ); ?>
                    </div>
                    <div class="col-lg-2">
						<?php Opalestate_Taxonomy_Type::dropdown_list( $stypes ); ?>
                    </div>
                    <div class="col-lg-2">
                        <button type="submit" class="btn btn-danger btn-sm btn-search">
							<?php esc_html_e( 'Search', 'opalestate-pro' ); ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <hr>
        <div class="<?php echo esc_attr( $rowcls ); ?>">
            <div class="col-lg-6 col-md-6">
                <div class="opalesate-properties-ajax opalesate-properties-results" data-mode="html">
					<?php echo opalestate_load_template_path( 'shortcodes/ajax-map-search-result' ); ?>
                </div>
            </div>

            <div class="col-lg-6 col-md-6">
                <div id="opalestate-map-preview" style="height:500px;" data-page="<?php echo esc_attr( $paged ); ?>">
                    <div id="mapView">
                        <div class="mapPlaceholder">
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
        </div>
    </div>
</div>
