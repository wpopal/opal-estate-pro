<?php
global $property;

if ( 'on' !== $property->get_block_setting( 'walkscores' ) ) {
	return;
}

$walkscore = opalestate_get_property_walkscore_results( $property );

?>
<div class="opalestate-box-content property-walkscore-session">
    <h4 class="outbox-title" id="block-scores"><?php esc_html_e( 'Walk Scores', 'opalestate-pro' ); ?></h4>
    <div class="walkscores-logo"><img src="<?php echo esc_url( OPALESTATE_PLUGIN_URL . 'assets/images/walk-score.png' ); ?>" alt="walkscore"></div>
    <div class="opalestate-box">
        <div class="box-info">
			<?php if ( isset( $walkscore->walkscore ) ) : ?>
                <div class="walk_details">
                    <div class="number-holder">
                        <h4 class="scores-label"><?php echo esc_html( $walkscore->walkscore ); ?></h4>
                    </div>
                    <div class="text-holder">
                        <h6><a href="<?php echo esc_url( $walkscore->ws_link ); ?>" target="_blank"><?php echo esc_html__( 'Walk Scores', 'opalestate-pro' ) ?></a></h6>
                        <span><?php echo esc_html( $walkscore->description ); ?></span>
                    </div>
                    <a href="<?php echo esc_url( $walkscore->ws_link ); ?>" class="walk-more-details" target="_blank"><?php esc_html_e( 'more details here', 'opalestate-pro' ) ?></a>
                </div>
			<?php endif; ?>

			<?php if ( isset( $walkscore->transit ) && $walkscore->transit->score ) : ?>
                <div class="walk_details">
                    <div class="number-holder">
                        <h4 class="scores-label"><?php echo esc_html( $walkscore->transit->score ); ?></h4>
                    </div>
                    <div class="text-holder">
                        <h6><a href="<?php echo esc_url( $walkscore->ws_link ); ?>" target="_blank"><?php echo esc_html__( 'Transit Scores', 'opalestate-pro' ) ?></a></h6>
                        <span><?php echo esc_html( $walkscore->transit->description ); ?></span>
                    </div>
                    <a href="<?php echo esc_url( $walkscore->ws_link ); ?>" target="_blank"><?php esc_html_e( 'more details here', 'opalestate-pro' ) ?></a>
                </div>
			<?php endif; ?>

			<?php if ( isset( $walkscore->bike ) ) : ?>
                <div class="walk_details">
                    <div class="number-holder">
                        <h4 class="scores-label"><?php echo esc_html( $walkscore->bike->score ); ?></h4>
                    </div>
                    <div class="text-holder">
                        <h6><a href="<?php echo esc_url( $walkscore->ws_link ); ?>" target="_blank"><?php echo esc_html__( 'Bikeable Scores', 'opalestate-pro' ) ?></a></h6>
                        <span><?php echo esc_html( $walkscore->bike->description ); ?></span>
                    </div>
                    <a href="<?php echo esc_url( $walkscore->ws_link ); ?>" target="_blank"><?php esc_html_e( 'More details here', 'opalestate-pro' ) ?></a>
                </div>
			<?php endif; ?>
        </div>
    </div>
</div>
