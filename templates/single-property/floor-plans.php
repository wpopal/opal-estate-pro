<?php
global $property;

if ( 'on' !== $property->get_block_setting( 'floor_plans' ) ) {
	return;
}

$floor_plans = $property->get_floor_plans();
if ( ! $floor_plans ) {
	return;
}

?>
<div class="opalestate-box-content property-floorplans-session">
    <h4 class="outbox-title" id="block-floor-plans"><?php esc_html_e( 'Floor Plans', 'opalestate-pro' ); ?></h4>
    <div class="opalestate-box">
        <div class="box-info">
            <div class="opalestate-tab">
                <div class="opalestate-tab-head">
                    <?php foreach ( $floor_plans as $key => $plan ) : ?>
                        <a href="#plan-<?php echo absint( $key ); ?>" class="tab-item <?php echo ( 0 == $key ) ? 'active' : ''; ?>">
                            <?php echo isset( $plan[ OPALESTATE_PROPERTY_PREFIX . 'floor_name' ] ) ? esc_html( $plan[ OPALESTATE_PROPERTY_PREFIX . 'floor_name' ] ) : sprintf( esc_html__( 'Plan %s',
                                'opalestate-pro' ), $key
                            ); ?>
                        </a>
                    <?php endforeach; ?>
                </div>

                <div class="opalestate-floorplans-content">
                    <?php foreach ( $floor_plans as $key => $plan ) : ?>
                        <div class="opalestate-tab-content <?php echo ( 0 == $key ) ? 'active' : ''; ?>" id="plan-<?php echo absint( $key ); ?>">

                            <h2 class="plan-name">
                                <?php echo isset( $plan[ OPALESTATE_PROPERTY_PREFIX . 'floor_name' ] ) ? esc_html( $plan[ OPALESTATE_PROPERTY_PREFIX . 'floor_name' ] ) : sprintf( esc_html__( 'Plan %s',
                                    'opalestate-pro' ), $key
                                ); ?>
                            </h2>
                            
                            <ul class="list-inline">
                                <?php if ( isset( $plan[ OPALESTATE_PROPERTY_PREFIX . 'floor_price' ] ) && $plan[ OPALESTATE_PROPERTY_PREFIX . 'floor_price' ] ) : ?>
                                    <li class="plan-price">
                                        <label class="plan-label"><?php esc_html_e( 'Price:', 'opalestate-pro' ); ?></label>
                                        <?php echo esc_html( $plan[ OPALESTATE_PROPERTY_PREFIX . 'floor_price' ] ); ?>
                                    </li>
                                <?php endif; ?>

                                <?php if ( isset( $plan[ OPALESTATE_PROPERTY_PREFIX . 'floor_size' ] ) && $plan[ OPALESTATE_PROPERTY_PREFIX . 'floor_size' ] ) : ?>
                                    <li class="plan-size">
                                        <label class="plan-label"><?php esc_html_e( 'Size:', 'opalestate-pro' ); ?></label>
                                        <?php echo esc_html( $plan[ OPALESTATE_PROPERTY_PREFIX . 'floor_size' ] ); ?>
                                    </li>
                                <?php endif; ?>

                                <?php if ( isset( $plan[ OPALESTATE_PROPERTY_PREFIX . 'floor_room' ] ) && $plan[ OPALESTATE_PROPERTY_PREFIX . 'floor_room' ] ) : ?>
                                    <li class="plan-room">
                                        <label class="plan-label"><?php esc_html_e( 'Rooms:', 'opalestate-pro' ); ?></label>
                                        <?php echo esc_html( $plan[ OPALESTATE_PROPERTY_PREFIX . 'floor_room' ] ); ?>
                                    </li>
                                <?php endif; ?>

                                <?php if ( isset( $plan[ OPALESTATE_PROPERTY_PREFIX . 'floor_bath' ] ) && $plan[ OPALESTATE_PROPERTY_PREFIX . 'floor_bath' ] ) : ?>
                                    <li class="plan-bath">
                                        <label class="plan-label"><?php esc_html_e( 'Baths:', 'opalestate-pro' ); ?></label>
                                        <?php echo esc_html( $plan[ OPALESTATE_PROPERTY_PREFIX . 'floor_bath' ] ); ?>
                                    </li>
                                <?php endif; ?>
                            </ul>
                            
                            <?php if ( isset( $plan[ OPALESTATE_PROPERTY_PREFIX . 'floor_content' ] ) && $plan[ OPALESTATE_PROPERTY_PREFIX . 'floor_content' ] ) : ?>
                                <div class="plan-content">
                                    <?php echo esc_html( $plan[ OPALESTATE_PROPERTY_PREFIX . 'floor_content' ] ); ?>
                                </div>
                            <?php endif; ?>

                            <?php if ( isset( $plan[ OPALESTATE_PROPERTY_PREFIX . 'floor_image_id' ] ) && $plan[ OPALESTATE_PROPERTY_PREFIX . 'floor_image_id' ] ) : ?>
                                <div class="plan-image">
                                    <?php echo wp_get_attachment_image( $plan[ OPALESTATE_PROPERTY_PREFIX . 'floor_image_id' ], 'full' ); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
