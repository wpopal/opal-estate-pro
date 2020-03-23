<?php
global $post;

$limit = apply_filters( 'opalesate_agent_properties_limit', 5 );
$query = Opalestate_Query::get_agent_property( null, get_the_ID(), $limit );

if ( $query->have_posts() ) : ?>
    <div class="clearfix clear"></div>
    <div class="opalestate-box-content my-properties-section" id="block-my-properties">
        <h4 class="outbox-title"><?php echo sprintf( esc_html__( 'My Properties', 'opalestate-pro' ), $query->found_posts ); ?></h4>
        <div class="ajax-load-properties" data-paged="1" data-type="agent" data-id="<?php echo get_the_ID(); ?>">
            <div class="opalestate-rows">
                <div class="<?php echo apply_filters( 'opalestate_row_container_class', 'opal-row' ); ?>">
					<?php while ( $query->have_posts() ) : $query->the_post(); ?>
                        <div class="col-lg-6 col-md-6 col-sm-12">
							<?php echo opalestate_load_template_path( 'content-property-grid' ); ?>
                        </div>
					<?php endwhile; ?>
                </div>
            </div>
			<?php if ( $query->max_num_pages > 1 ): ?>
                <div class="w-pagination"><?php opalestate_pagination( $query->max_num_pages ); ?></div>
			<?php endif; ?>
        </div>
    </div>
<?php endif;
wp_reset_postdata();
