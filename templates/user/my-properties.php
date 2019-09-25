<?php
$statuses = [
	'all'       => esc_html__( 'All', 'opalestate-pro' ),
	'published' => esc_html__( 'Published', 'opalestate-pro' ),
	'pending'   => esc_html__( 'Pending', 'opalestate-pro' ),
	'expired'   => esc_html__( 'Expired', 'opalestate-pro' ),
];

$gstatus = isset( $_GET['status'] ) ? $_GET['status'] : 'all';
?>
<?php do_action( "opalestate_submission_listing_before" ); ?>
    <div class="property-listing my-properties">

        <div class="list-tabs">
            <div class="tabs">
                <ul class="clearfix">
					<?php foreach ( $statuses as $status => $label ): ?>
                        <li <?php if ( $status == $gstatus ): ?> class="active" <?php endif; ?>>
                            <a href="<?php echo esc_url( opalestate_get_current_url( [ 'status' => $status ] ) ); ?>"><?php echo esc_attr( $label ); ?></a>
                        </li>
					<?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div class="opalestate-admin-box">
            <div class="box-content">
				<?php if ( $loop->have_posts() ): ?>
                    <div class="opalestate-rows">
                        <div class="<?php echo apply_filters( 'opalestate_row_container_class', 'opal-row' ); ?>">
							<?php $cnt = 0;
							while ( $loop->have_posts() ) : $loop->the_post();
								global $post; ?>
                                <div class="col-lg-12 col-md-12 col-sm-12">
									<?php echo opalestate_load_template_path( 'user/content-property' ); ?>
                                </div>
							<?php endwhile; ?>
                        </div>
                    </div>
					<?php opalestate_pagination( $loop->max_num_pages ); ?>

				<?php else : ?>
                    <div class="opalestate-message">
						<?php esc_html_e( 'You have not submited any property.', 'opalestate-pro' ); ?>
                    </div>
				<?php endif; ?>
            </div>
        </div>
    </div>
<?php wp_reset_postdata(); ?>
<?php do_action( 'opalestate_submission_listing_after' ); ?>
