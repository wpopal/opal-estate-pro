<?php
$team         = get_post_meta( get_the_ID(), OPALESTATE_AGENCY_PREFIX . 'team', true );

if ( $team ) :
	$column = 2;
	$colclass = floor( 12 / $column );
	?>
    <div class="opalesate-agents opalestate-box">
		<?php if ( $team ): ?>
            <div class="row">
				<?php
				foreach ( $team as $user_id ):
					$agent_id = get_user_meta( $user_id, OPALESTATE_USER_PROFILE_PREFIX . 'related_id', true );
					?>
                    <div class="col-lg-<?php echo esc_attr( $colclass ); ?> col-md-<?php echo esc_attr( $colclass ); ?> col-sm-<?php echo esc_attr( $colclass ); ?>">
						<?php echo opalestate_load_template_path( 'content-user-grid', [ 'user_id' => $user_id ] ); ?>
                    </div>
				<?php endforeach; ?>
            </div>

		<?php else: ?>
			<?php get_template_part( 'content', 'none' ); ?>
		<?php endif; ?>
    </div>
	<?php wp_reset_postdata(); ?>
<?php endif; ?>
