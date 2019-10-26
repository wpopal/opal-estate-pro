<?php
$fields = OpalEstate_Search::get_setting_search_fields();
if ( ! $fields ) {
	return;
}

$type   = isset( $type ) ? $type : '';
$column = isset( $GLOBALS['group-info-column'] ) ? $GLOBALS['group-info-column'] : 3;
if ( $type != 'input' ) {
	$col_class = 'col-lg-' . ( 12 / absint( $column ) ) . ' col-md-' . ( 12 / absint( $column ) ) . ' col-sm-' . ( 12 / absint( $column ) );
} else {
	$col_class = 'column-item';
}

foreach ( $fields as $key => $label ): ?>
	<?php if ( 'areasize' == $key ) : continue; endif; ?>
    <div class="<?php echo esc_attr( $col_class ); ?>">
		<?php opalestate_property_render_field_template( $key, $label, $type ); ?>
    </div>
<?php endforeach; ?>

