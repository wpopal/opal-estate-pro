<?php
global $property;
if ( 'on' !== $property->get_block_setting( 'apartments' ) ) {
	return;
}

$apartments = $property->get_apartments();
if ( ! $apartments ) {
	return;
}

?>
<div class="opalestate-box-content property-apartments-session">
    <h4 class="outbox-title" id="block-apartments"><?php esc_html_e( 'Apartments', 'opalestate-pro' ); ?></h4>
    <div class="opalestate-box">
        <div class="box-info">
            <div class="opalestate-aparments-table">
                <div class="table-responsive">
                    <table>
                        <thead>
                        <tr>
                            <th><?php esc_html_e( 'Plot', 'opalestate-pro' ); ?></th>
                            <th><?php esc_html_e( 'Beds', 'opalestate-pro' ); ?></th>
                            <th><?php esc_html_e( 'Price From', 'opalestate-pro' ); ?></th>
                            <th><?php esc_html_e( 'Floor', 'opalestate-pro' ); ?></th>
                            <th><?php esc_html_e( 'Building / Address', 'opalestate-pro' ); ?></th>
                            <th><?php esc_html_e( 'Status', 'opalestate-pro' ); ?></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ( $apartments as $key => $apartment ) : ?>
                            <tr>
                                <td><?php echo isset( $apartment[ OPALESTATE_PROPERTY_PREFIX . 'apartment_plot' ] ) ? esc_html( $apartment[ OPALESTATE_PROPERTY_PREFIX . 'apartment_plot' ] ) : ''; ?></td>
                                <td><?php echo isset( $apartment[ OPALESTATE_PROPERTY_PREFIX . 'apartment_beds' ] ) ? esc_html( $apartment[ OPALESTATE_PROPERTY_PREFIX . 'apartment_beds' ] ) : ''; ?></td>
                                <td><?php echo isset( $apartment[ OPALESTATE_PROPERTY_PREFIX . 'apartment_price_from' ] ) ? esc_html( $apartment[ OPALESTATE_PROPERTY_PREFIX . 'apartment_price_from' ] ) : ''; ?></td>
                                <td><?php echo isset( $apartment[ OPALESTATE_PROPERTY_PREFIX . 'apartment_floor' ] ) ? esc_html( $apartment[ OPALESTATE_PROPERTY_PREFIX . 'apartment_floor' ] ) : ''; ?></td>
                                <td><?php echo isset( $apartment[ OPALESTATE_PROPERTY_PREFIX . 'apartment_building_address' ] ) ? esc_html( $apartment[ OPALESTATE_PROPERTY_PREFIX . 'apartment_building_address' ] ) : ''; ?></td>
                                <td><?php echo isset( $apartment[ OPALESTATE_PROPERTY_PREFIX . 'apartment_status' ] ) ? esc_html( $apartment[ OPALESTATE_PROPERTY_PREFIX . 'apartment_status' ] ) : ''; ?></td>
                                <td>
                                    <?php if ( isset( $apartment[ OPALESTATE_PROPERTY_PREFIX . 'apartment_link' ] ) ) : ?>
                                        <a class="view-btn" href="<?php echo esc_url( $apartment[ OPALESTATE_PROPERTY_PREFIX . 'apartment_link' ] ); ?>">
                                            <?php esc_html_e( 'view', 'opalestate-pro' ); ?>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
