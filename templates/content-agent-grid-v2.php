<?php
$agent = new OpalEstate_Agent();
?>
<article <?php post_class( 'agent-grid-style' ); ?>>
    <div class="team-v2 agent-inner">
        <header class="team-header agent-header">
			<?php opalestate_get_loop_agent_thumbnail( opalestate_get_option( 'agent_image_size', 'large' ) ); ?>
			<?php if ( $agent->is_featured() ): ?>
                <span class="agent-featured" data-toggle="tooltip" data-placement="top" title="<?php esc_attr_e( 'Featured Agent', 'opalestate-pro' ); ?>">
				<span class="agent-label">
					<span><?php esc_html_e( 'Featured', 'opalestate-pro' ); ?></span>
				</span>
			</span>
			<?php endif; ?>

			<?php if ( $agent->get_trusted() ): ?>
                <span class="trusted-label hint--top" aria-label="<?php esc_attr_e( 'Trusted Member', 'opalestate-pro' ); ?>" title="<?php esc_attr_e( 'Trusted Member', 'opalestate-pro' ); ?>">
				    <i class="fa fa-star"></i>
			    </span>
			<?php endif; ?>
        </header>
        <div class="team-body agent-body">

            <div class="team-body-content">
                <h5 class="agent-box-title">
                    <a href="<?php the_permalink(); ?>"><?php the_title() ?></a>
                </h5><!-- /.agent-box-title -->

                <h3 class="team-name hide"><?php the_title(); ?></h3>
            </div>
        </div>
    </div>
</article>	
