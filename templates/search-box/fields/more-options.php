<?php
/**
 * The template for more options search
 *
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

$unique_id = esc_attr( opalestate_unique_id() );
$amenities = Opalestate_Taxonomy_Amenities::get_list();

if ( ! $amenities ) {
	return;
}

?>
<div class="search-more-options">
    <a href="#" class="opal-collapse-button" data-collapse="#more-options-<?php echo esc_attr( $unique_id ); ?>"><?php esc_html_e( 'More Search Options', 'opalestate-pro' ); ?></a>

    <div id="more-options-<?php echo esc_attr( $unique_id ); ?>" class="opal-collapse-container more-options-container">
        <div class="more-options-items">
	        <?php foreach ( $amenities as $amenity ) : ?>
                <div class="more-options-item">
                    <label class="more-options-label">
                        <input type="checkbox" name="amenities[]" value="<?php echo esc_attr( $amenity->slug ); ?>">
				        <?php echo esc_html( $amenity->name ); ?>
                    </label>
                </div>
	        <?php endforeach; ?>
        </div>
    </div>
</div>
