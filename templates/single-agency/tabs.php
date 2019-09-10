<?php $team = get_post_meta( get_the_ID(), OPALESTATE_AGENCY_PREFIX . 'team', true ); ?>
<div class="agency-tabs">
	<ul class="nav nav-tabs" role="tablist">
		<li class="active">
			<a aria-expanded="false" href="#agency-properties" role="tab" class="tab-item">
				<span><?php esc_html_e( 'Properties', 'opalestate-pro'  ); ?></span>
			</a>
		</li>
		<li>
			<a aria-expanded="true" href="#agency-team" class="tab-google-street-view-btn" role="tab" class="tab-item">
				<span><?php esc_html_e('Team','opalestate-pro'); ?></span>
			</a>
		</li>

		<li >
			<a aria-expanded="true" href="#agency-review" class="tab-google-street-view-btn" role="tab" class="tab-item">
				<span><?php esc_html_e('Review','opalestate-pro'); ?></span>
			</a>
		</li>

	</ul>
	<div class="tab-content">
		<div class="tab-pane fade out active in" id="agency-properties">
			 <?php echo opalestate_load_template_path( 'single-agency/properties' ); ?>
		</div>

		<?php if(  $team ): ?> 
		<div class="tab-pane fade out" id="agency-team">
			 <?php echo opalestate_load_template_path( 'single-agency/team' ); ?>
		</div>
		<?php endif; ?>
		<?php if ( comments_open() || get_comments_number() ) : ?>
		<div class="tab-pane fade out" id="agency-review">
			 <?php echo opalestate_load_template_path( 'single-agency/review' ); ?>
		</div>
		<?php endif; ?>
	</div>	

</div>

