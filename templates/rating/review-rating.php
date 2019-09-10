<?php
/**
 * The template to display the reviewers star rating in reviews
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $comment;
$post_id   = get_the_ID();
$post_type = get_post_type( $post_id );
$supports  = Opalestate_Rating::get_rating_supports();

$cpt_feature = $supports[ $post_type ]['features_cpt'];
$features    = Opalestate_Rating_Helper::get_features( $cpt_feature );
$average     = Opalestate_Rating_Helper::get_average_rating( $comment, $cpt_feature );

?>
<?php if ( $features ) : ?>
    <div class="opalestate-review__ratings">
        <div class="opalestate-tooltip" data-tooltip-content="#tooltip_content_<?php echo absint( $comment->comment_ID ); ?>">
			<?php
			if ( $average ) {
				echo opalestate_get_rating_html( $average ); // WPCS: XSS ok.
			}
			?>
        </div>

        <div class="opalestate-rating-detail-container">
            <ul class="opalestate-rating-detail" id="tooltip_content_<?php echo absint( $comment->comment_ID ); ?>">
				<?php foreach ( $features as $feature_slug => $feature_title ) : $feature_key = $cpt_feature . '_' . $feature_slug; ?>
                    <li class="opalestate-rating-detail__item">
                        <label><?php echo esc_html( $feature_title ); ?>:</label>
                        <span><?php echo absint( get_comment_meta( get_comment_ID(), $feature_key, true ) ); ?></span>
                    </li>
				<?php endforeach; ?>
            </ul>
        </div>
    </div>
<?php else : ?>
	<?php
	if ( $average ) {
		echo opalestate_get_rating_html( $average ); // WPCS: XSS ok.
	} ?>
<?php endif; ?>
