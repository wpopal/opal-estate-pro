<?php global $property, $post;

$property = opalesetate_property( get_the_ID() );
$header   = apply_filters( 'opalestate_single_show_heading', true );
?>
<div class="property-single-top ">
	<?php
	/**
	 * opalestate_before_single_property_summary hook
	 */
	do_action( 'opalestate_single_property_preview' );
	?>
    <div class="property-single-stick-bars keep-top-bars ">
        <div class="container">
            <ul class="list-inline opalestate-scroll-elements">
                <li><a href="#block-description"><?php esc_html_e( 'Description', 'opalestate-pro' ); ?></a></li>
				<?php if ( $property->get_block_setting( 'video' ) ) : ?>
                    <li><a href="#block-video"><?php esc_html_e( 'Video', 'opalestate-pro' ); ?></a></li>
				<?php endif; ?>
				<?php if ( $property->get_block_setting( 'map' ) && apply_filters( 'opalestate_single_show_map', true ) ) : ?>
                    <li><a href="#block-map"><?php esc_html_e( 'Map', 'opalestate-pro' ); ?></a></li>
				<?php endif; ?>
				<?php if ( $property->get_block_setting( 'walkscores' ) ) : ?>
                    <li><a href="#block-scores"><?php esc_html_e( 'Scores', 'opalestate-pro' ); ?></a></li>
				<?php endif; ?>
				<?php if ( $property->get_block_setting( 'views_statistics' ) ) : ?>
                    <li><a href="#block-statistics"><?php esc_html_e( 'Statistics', 'opalestate-pro' ); ?></a></li>
				<?php endif; ?>
				<?php if ( $property->get_block_setting( 'apartments' ) && $property->get_apartments() ) : ?>
                    <li><a href="#block-apartments"><?php esc_html_e( 'Apartments', 'opalestate-pro' ); ?></a></li>
				<?php endif; ?>
				<?php if ( $property->get_block_setting( 'floor_plans' ) && $property->get_floor_plans() ) : ?>
                    <li><a href="#block-floor-plans"><?php esc_html_e( 'Floor Plans', 'opalestate-pro' ); ?></a></li>
				<?php endif; ?>
                <li><a href="#reviews"><?php esc_html_e( 'Reviews', 'opalestate-pro' ); ?></a></li>
                <li class="single-property-buttons">
					<?php opalestate_property_request_viewing_button(); ?>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="container">
    <article id="property-<?php the_ID(); ?>" itemscope itemtype="http://schema.org/Property" <?php post_class( "opalestate-single-property opalestate-single-property--version-5" ); ?>>
        <meta itemprop="url" content="<?php the_permalink(); ?>"/>
        <div class="opal-row">
            <div class="col-lg-8 col-md-8 col-sm-12">
				<?php if ( $header ): ?>
                    <header class="property-single-header">
                        <div class="property-single-info">
                            <div class="single-price-content"><?php opalestate_property_loop_price(); ?></div>
                            <div class="group-items">
	                            <?php do_action( 'opalestate_before_single_property_title' ); ?>

								<?php the_title( '<h2 class="entry-title">', '</h2>' ); ?>
	                            <?php opalestate_property_label(); ?>
	                            <?php opalestate_property_status(); ?>

	                            <?php do_action( 'opalestate_after_single_property_title' ); ?>

                                <div class="property-meta">
                                    <div class="property-meta__list">
		                                <?php opalestate_property_types_list(); ?>
		                                <?php opalestate_property_categories_list(); ?>
                                    </div>

                                    <div class="property-address clearfix">
										<?php if ( $property->latitude && $property->longitude ) : ?>
                                            <a href="<?php echo esc_url( $property->get_google_map_link() ); ?>" rel="nofollow" target="_blank">
                                                <span class="property-address__view-map property-view-map"><i class="fas fa-map-marker-alt"></i></span>
                                            </a>
										<?php endif; ?>

										<?php if ( $property->get_address() ) : ?>
                                            <span class="property-address__text"><?php echo esc_html( $property->get_address() ); ?></span>
										<?php endif; ?>
                                    </div>
                                    <div class="property-date">
										<?php
										printf(
										/* translators: %s: property date */
											__( 'Posted: %s', 'opalestate-pro' ),
											esc_html( $property->get_posted() )
										);
										?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </header>
				<?php endif; ?>
                <div class="opalestate-box-content">
                    <h4 class="outbox-title" id="block-description"><?php esc_html_e( 'Property Description', 'opalestate-pro' ); ?></h4>
                    <div class="summary entry-summary opalestate-rows">
                        <div class="property-meta-top">
                            <ul class="list-inline property-meta-top__list">
	                            <?php do_action( 'opalestate_before_property_meta_top_list' ); ?>

								<?php if ( $property->get_sku() ) : ?>
                                    <li class="list-inline__sku">
                                        <span><?php esc_html_e( 'Property ID: ', 'opalestate-pro' ) ?></span>
                                        <span class="property-sku"><?php echo esc_html( $property->get_sku() ); ?></span>
                                    </li>
								<?php endif; ?>
                                <li class="list-inline__print property-meta-top__button">
									<?php opalestate_property_print_button( $property->get_id() ); ?>
                                </li>
                                <li class="list-inline__favorite property-meta-top__button">
									<?php echo do_shortcode( '[opalestate_favorite_button property_id=' . get_the_ID() . ']' ); ?>
                                </li>

	                            <?php do_action( 'opalestate_after_property_meta_top_list' ); ?>
                            </ul>
                        </div>

						<?php
						/**
						 * opalestate_single_property_summary hook
						 */
						do_action( 'opalestate_single_property_summary' );
						?>
                        <div class="content-bottom">
							<?php do_action( 'opalestate_single_content_bottom' ); ?>
                        </div>
                    </div><!-- .summary -->
                </div>
				<?php
				/**
				 * opalestate_after_single_property_summary hook
				 */
				do_action( 'opalestate_after_single_property_summary' );
				?>
                <div class="clear clearfix"></div>
            </div>

            <div class="col-lg-4 col-md-4 col-sm-12  single-property-sidebar">
                <div class="inner">
					<?php do_action( 'opalestate_single_property_sidebar' ); ?>
                </div>
            </div>
        </div>
    </article><!-- #post-## -->
</div>		
