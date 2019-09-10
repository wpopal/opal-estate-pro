<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $property, $post;

$locations = $property->getLocations();

if ( !empty($locations) ) :
?>
	
	<?php foreach ($locations as $location) : ?>
		<a href="<?php echo esc_url( get_term_link( $location ) ); ?>" title="<?php echo esc_attr( $location->name ); ?>">
			<?php echo esc_html( $location->name ); ?>
		</a>
	<?php endforeach; ?>

<?php endif; ?>
