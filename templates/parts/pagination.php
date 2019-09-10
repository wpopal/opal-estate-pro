<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $wp_query;

if ( $wp_query->max_num_pages <= 1 ) {
	return;
}

$args = apply_filters( 'opalestate_pagination_args', [
	'prev_text' => __( '&laquo;' ),
	'next_text' => __( '&raquo;' ),
	'type'      => 'list',
] );

?>

<nav class="opalestate-pagination">
	<?php print paginate_links( $args ); // WPCS: xss ok. ?>
</nav>


