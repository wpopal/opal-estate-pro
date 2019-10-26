<?php
$paged  = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
$rowcls = apply_filters( 'opalestate_row_container_class', 'opal-row' );

$slocation = isset( $_GET['location'] ) ? sanitize_text_field( $_GET['location'] ) : opalestate_get_session_location_val();
$stypes    = isset( $_GET['types'] ) ? sanitize_text_field( $_GET['types'] ) : -1;
$sstatus   = isset( $_GET['status'] ) ? sanitize_text_field( $_GET['status'] ) : -1;

$search_min_price = isset( $_GET['min_price'] ) ? sanitize_text_field( $_GET['min_price'] ) : opalestate_options( 'search_min_price', 0 );
$search_max_price = isset( $_GET['max_price'] ) ? sanitize_text_field( $_GET['max_price'] ) : opalestate_options( 'search_max_price', 10000000 );

$showareasize = opalestate_options( OPALESTATE_PROPERTY_PREFIX . 'areasize_opt', 1 );
$showprice    = opalestate_options( OPALESTATE_PROPERTY_PREFIX . 'price_opt', 1 );
$fields       = OpalEstate_Search::get_setting_search_fields();

?>
<div class="ajax-map-search full-width">
    <div class="inner">
        <div class="ajax-search-form" data-parent=".ajax-map-search">
            <div class="<?php echo $rowcls; ?>">
                <form id="opalestate-search-form" class="opalestate-search-form ajax-form" method="get">
                    <div class="col-lg-2 col-md-3 col-sm-4 ajax-change">
						<?php Opalestate_Taxonomy_Status::dropdown_list( $sstatus ); ?>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-4 ajax-change">
						<?php Opalestate_Taxonomy_Location::dropdown_list( $slocation ); ?>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-4 ajax-change">
						<?php Opalestate_Taxonomy_Type::dropdown_list( $stypes ); ?>
                    </div>
                    <input type="hidden" name="paged" value="1">
                </form>
                <form id="opalestate-more-search-form" class="opalestate-more-search-form pull-left" method="get">
                    <div class="opalestate-popup">
                        <div class="popup-head"><span><?php esc_html_e( 'More', 'opalestate-pro' ); ?></span><i class="fa"></i></div>
                        <div class="popup-body">
                            <div class="popup-close"><i class="fa fa-times" aria-hidden="true"></i></div>

                            <div class="ajax-advanded-search">
								<?php if ( $fields ): ?>
									<?php foreach ( $fields as $key => $label ): ?>
                                        <div class="form-group">
                                            <label><?php echo $label; ?></label>
											<?php opalestate_property_render_field_template( $key, esc_html__( "Any", 'opalestate-pro' ) ); ?>
                                        </div>
									<?php endforeach; ?>
								<?php endif; ?>

								<?php if ( $showprice ): ?>
                                    <div class="form-group">
                                        <div class="cost-price-content">
											<?php

											$data = [
												'id'         => 'price',
												'unit'       => opalestate_currency_symbol() . ' ',
												'ranger_min' => opalestate_options( 'search_min_price', 0 ),
												'ranger_max' => opalestate_options( 'search_max_price', 10000000 ),
												'input_min'  => $search_min_price,
												'decimals'   => opalestate_get_price_decimals(),
												'input_max'  => $search_max_price,
											];
											opalesate_property_slide_ranger_template( esc_html__( "Price:", 'opalestate-pro' ), $data );
											?>
                                        </div>
                                    </div>
								<?php endif; ?>
								<?php if ( $showareasize ): ?>
                                    <div class="form-group">
                                        <div class="area-range-content">
											<?php opalestate_property_areasize_field_template(); ?>
                                        </div>
                                    </div>
								<?php endif; ?>
                                <button type="submit" class="btn btn-danger btn-sm btn-search btn-3d btn-block">
									<?php esc_html_e( 'Apply', 'opalestate-pro' ); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="col-lg-1 col-md-2 col-sm-3 col-xs-12">
					<?php echo opalestate_load_template_path( 'user-search/render-form' ); ?>
                </div>
                <div class="col-lg-3 col-md-5 col-sm-6 col-xs-12">
					<?php do_action( 'opalestate_before_render_search_properties_result' ); ?>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="<?php echo esc_attr( $rowcls ); ?>">

        <div class="col-lg-6 col-md-12">
            <div class="opalesate-properties-ajax opalesate-properties-results" data-mode="html">
				<?php echo opalestate_load_template_path( 'shortcodes/ajax-map-search-result' ); ?>
            </div>
        </div>

        <div class="col-lg-6 col-md-12">
            <div class="opalestate-sticky" data-parent=".ajax-map-search">
                <div id="opalestate-map-preview" style="min-height:800px;" data-page="<?php echo esc_attr( $paged ); ?>">
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
