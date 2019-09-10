<?php
global $property;
$infos = $property->get_metabox_info();

$types  = $property->get_types();
$status = $property->get_status();

?>
ha cong
<div class="box-info">
    <h3 class="box-heading"><?php esc_html_e( 'Property Information', 'opalestate-pro' ); ?></h3>
    <div class="box-content">
        <ul class="list-info">
			<?php if ( $infos ): ?>

				<?php foreach ( $infos as $key => $info ) : ?>
					<?php if ( $info['value'] ) : ?>
                        <li class="icon-<?php echo esc_attr( $key ); ?>"><span><?php echo esc_html( $info['label'] ); ?></span> <?php echo apply_filters( 'opalestate-pro' . $key . '_unit_format',
								trim( $info['value'] )
							); ?></li>
					<?php endif; ?>
				<?php endforeach; ?>

			<?php endif; ?>
			<?php if ( ! empty( $types ) ): ?>
                <li class="icon-type">
					<span>
						<?php esc_html_e( 'Type', 'opalestate-pro' ); ?>
					</span>
					<?php foreach ( $types as $type ) : ?>
                        <a href="<?php echo esc_url( get_term_link( $type ) ); ?>" title="<?php echo esc_attr( $type->name ); ?>">
							<?php echo esc_html( $type->name ); ?>
                        </a>
					<?php endforeach; ?>
                </li>
			<?php endif; ?>
			<?php if ( ! empty( $status ) ): ?>
                <li class="icon-status">
					<span>
						<?php esc_html_e( 'Status', 'opalestate-pro' ); ?>
					</span>
					<?php foreach ( $status as $type ) : ?>
                        <a href="<?php echo esc_url( get_term_link( $type ) ); ?>" title="<?php echo esc_attr( $type->name ); ?>">
							<?php echo esc_html( $type->name ); ?>
                        </a>
					<?php endforeach; ?>
                </li>
			<?php endif; ?>
        </ul>
    </div>
</div>		
