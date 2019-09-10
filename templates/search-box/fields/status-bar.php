<?php
$statuses = Opalestate_Taxonomy_Status::get_list();

if ( ! $statuses ) {
	return;
}

$sstatus = isset( $_GET['status'] ) ? sanitize_text_field( $_GET['status'] ): -1;
$label   = isset( $label ) ? esc_html( $label ) : esc_html__( 'All', 'opalestate-pro' );
$style   = isset( $style ) && $style ? $style : '';
?>

<div class="search-status-bar <?php echo esc_attr( $style ) ? 'search-status-bar--' . $style : ''; ?>">
    <ul class="list-inline clearfix list-property-status">
		<?php if ( ! isset( $hide_default_status ) ) : ?>
            <li class="status-item  <?php if ( $sstatus == -1 ): ?> active<?php endif; ?>" data-id="-1">
                <span><?php echo esc_html( $label ); ?></span>
            </li>
		<?php endif; ?>

		<?php foreach ( $statuses as $status ): ?>
            <li class="status-item <?php if ( $sstatus == $status->slug ) : ?> active<?php endif; ?>" data-id="<?php echo esc_attr( $status->slug ); ?>">
                <span><?php echo esc_html( $status->name ); ?> </span>
            </li>
		<?php endforeach; ?>
    </ul>
    <input type="hidden" value="<?php echo esc_attr( $sstatus ); ?>" name="status"/>
</div>
