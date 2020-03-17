<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$property = opalesetate_property( get_the_ID() );

global $property, $post;
$status   = get_post_status( get_the_ID() );
$statuses = opalestate_get_property_statuses();
$meta     = $property->get_meta_shortinfo();

$current_user = wp_get_current_user();
$roles = $current_user->roles;
?>
<article itemscope itemtype="http://schema.org/Property" <?php post_class( 'my-property-list' ); ?>>
    <div class="property-list container-cols-2">
        <header>
            <div class="property-group-label">
		        <?php opalestate_property_label(); ?>
            </div>

            <div class="property-group-status">
		        <?php opalestate_property_status(); ?>
            </div>

            <div class="property-meta-bottom">
                <?php echo do_shortcode( '[opalestate_favorite_button property_id=' . get_the_ID() . ']' ); ?>
            </div>

			<?php
			if ( in_array( 'administrator', $roles ) ) {
				opalestate_get_loop_thumbnail( opalestate_get_option( 'loop_image_size', 'large' ) );
			} else {
				?>
                <div class="property-box-image">
                    <span class="property-box-image-inner">
						<?php if ( has_post_thumbnail() ) : ?>
							<?php the_post_thumbnail( apply_filters( 'opalestate_loop_property_thumbnail', opalestate_get_option( 'loop_image_size', 'large' ) ) ); ?>
						<?php else: ?>
							<?php echo opalestate_get_image_placeholder( opalestate_get_option( 'loop_image_size', 'large' ) ); ?>
						<?php endif; ?>
                    </span>
                </div>
                <?php
			}
			?>

        </header>
        <div class="abs-col-item">
            <div class="entry-content">
				<?php
                if ( in_array( 'administrator', $roles ) ) {
	                the_title( '<h4 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h4>' );
                } else {
	                the_title( '<h4 class="entry-title">', '</h4>' );
                }
                ?>

                <div class="property-address">
					<?php echo $property->get_address(); ?>
                </div>

                <div class="property-price">
                    <span><?php echo opalestate_price_format( $property->get_price() ); ?></span>

					<?php if ( $property->get_sale_price() ): ?>
                        <span class="property-saleprice"><?php echo opalestate_price_format( $property->get_sale_price() ); ?></span>
					<?php endif; ?>

					<?php if ( $property->get_price_label() ): ?>
                        <span class="property-price-label"><?php echo esc_html( $property->get_price_label() ); ?></span>
					<?php endif; ?>
                </div>

                <div class="my-properties-bottom">
                    <span class="label-post-status label <?php if ( $post->post_status == 'pending' ): ?> label-danger <?php else : ?> label-info <?php endif; ?>"> <?php echo esc_html( isset(
		                    $statuses[ $status ] ) ? $statuses[ $status ] : $status );
	                    ?>
                    </span>
                </div>
                <div class="property-meta">
                    <ul class="property-meta-list list-inline">
						<?php if ( $meta ) : ?>
							<?php foreach ( $meta as $key => $info ) : ?>
                                <li class="property-label-<?php echo esc_attr( $key ); ?>" data-toggle="tooltip" data-placement="top" title="<?php echo esc_attr( $info['label'] ); ?>">
                                    <i class="<?php echo opalestate_get_property_meta_icon( $key ); ?>"></i>
                                    <span class="label-property"><?php echo esc_html( $info['label'] ); ?></span>
                                    <span class="label-content"><?php echo apply_filters( 'opalestate-pro' . $key . '_unit_format', trim( $info['value'] ) ); ?></span>
                                </li>
							<?php endforeach; ?>
						<?php endif; ?>
                    </ul>
                </div>
            </div><!-- .entry-content -->
            <div class="button-actions">
				<?php if ( $property->featured != 1 ): ?>
                    <a href="#" class="btn btn-warning btn-toggle-featured" data-property-id="<?php echo get_the_ID() ?>" data-toggle="tooltip" data-placement="top"
                       title="<?php esc_html_e( 'Set Featured', 'opalestate-pro' ) ?>">
                        <i class="fa fa-star"></i>
                    </a>
				<?php endif; ?>

                <a href="<?php echo opalestate_submssion_page( get_the_ID() ) ?>" class="btn btn-info" data-toggle="tooltip" data-placement="top"
                   title="<?php esc_html_e( 'Edit', 'opalestate-pro' ); ?>">
                    <i class="fa fa-edit"></i>
                </a>

				<?php
				/* Delete Post Link Bypassing Trash */
				if ( current_user_can( 'delete_posts' ) ) {
					$delete_post_link = get_delete_post_link( $post->ID, '', true );
					if ( ! empty( $delete_post_link ) ) {
						?>
                        <a onclick="return confirm('<?php esc_html_e( 'Are you sure you wish to delete?', 'opalestate-pro' ); ?>')" href="<?php echo esc_url( $delete_post_link ); ?>"
                           class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="<?php esc_html_e( 'Delete Property', 'opalestate-pro' ); ?>">
                            <i class="fa fa-close"></i>
                        </a>
						<?php
					}
				} ?>
            </div>
        </div>

        <meta itemprop="url" content="<?php the_permalink(); ?>"/>
    </div>
</article><!-- #post-## -->
