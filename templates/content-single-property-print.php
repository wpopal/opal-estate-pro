<?php global $property, $post;
$property_id = isset( $property_id ) ? $property_id : get_the_ID();
$property    = opalesetate_property( $property_id );

?>

<div class="property-single-top">
    <div class="container">
        <header class="property-single-header">
            <div class="property-single-info">
                <div class="group-items">
                    <h2 class="entry-title"><?php echo get_the_title( $property_id ); ?></h2>
	                <?php opalestate_property_label(); ?>
	                <?php opalestate_property_status(); ?>

                    <div class="property-meta">
                        <div class="property-meta__list">
		                    <?php opalestate_property_types_list(); ?>
		                    <?php opalestate_property_categories_list(); ?>
                        </div>

                        <div class="property-address clearfix">
                            <div class="pull-left">
								<?php if ( $property->latitude && $property->longitude ) : ?>
                                    <a href="<?php echo esc_url( $property->get_google_map_link() ); ?>" rel="nofollow" target="_blank">
                                        <span class="property-address__view-map property-view-map"><i class="fas fa-map-marker-alt"></i></span>
                                    </a>
								<?php endif; ?>

								<?php if ( $property->get_address() ) : ?>
                                    <span class="property-address__text"><?php echo esc_html( $property->get_address() ); ?></span>
								<?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="single-price-content"><?php opalestate_property_loop_price(); ?></div>
            </div>
        </header>
		<?php if ( has_post_thumbnail( $property_id ) ): ?>
			<?php echo get_the_post_thumbnail( $property_id, 'full' ); ?>
		<?php endif; ?>
    </div>
</div>

<div class="container">
    <article id="property-<?php echo absint( $property_id ); ?>" <?php post_class( "opalestate-single-property opalestate-single-property--print" ); ?>>
        <div class="opalestate-box-content">
            <h4 class="outbox-title"><?php esc_html_e( 'Property Description', 'opalestate-pro' ); ?></h4>
            <div class="summary entry-summary opalestate-rows">
                <div class="property-meta-top">
                    <div class="property-id">
						<?php if ( $property->get_sku() ) : ?>
                            <span><?php esc_html_e( 'Property ID: ', 'opalestate-pro' ) ?></span>
                            <span class="property-sku"><strong><?php echo esc_html( $property->get_sku() ); ?></strong></span>
						<?php endif; ?>
                    </div>
                </div>

				<?php
				opalestate_get_single_short_meta();
				opalestate_property_content();
				opalestate_property_information();
				opalestate_property_amenities();
				opalestate_property_facilities();
				?>
            </div><!-- .summary -->
			<?php
			opalestate_property_apartments();
			opalestate_property_floor_plans();
			?>
        </div>
        <div class="clear clearfix"></div>
    </article><!-- #post-## -->
</div>
<style>
    .breadcrumbs,
    #wrapper-navbar,
    #wrapper-footer {
        display: none;
    }
</style>
