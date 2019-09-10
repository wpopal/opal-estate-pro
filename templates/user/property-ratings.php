<?php
/**
 * Display single property reviews (comments)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! isset( $comments ) || ! $comments ) {
	echo '<p class="opalestate-my-reviews-no-reviews">' . esc_html__( 'You have not written any reviews yet.', 'opalestate-pro' ) . '</p>';

	return;
}

wp_enqueue_style( "tooltipster" );
wp_enqueue_script( "tooltipster" );
?>

<div class="opalestate-my-reviews opalestate-box">
    <ol class="commentlist opalestate-my-reviews-list">
		<?php foreach ( $comments as $comment ) : ?>
            <li class="property_review opalestate-my-reviews-item" id="li-comment-41">
                <div class="opal-row">
                    <div id="comment-41" class="col-lg-9 col-sm-9 comment_container opalestate-my-reviews-item__comment">
                        <?php echo get_avatar( $comment, apply_filters( 'opalestate_review_gravatar_size', '60' ), '' ); ?>

                        <div class="comment-text">
                            
                            <div class="wrapperss">
                            <p class="meta">
                                <span class="opalestate-review__author"><?php echo esc_html( $comment->comment_author ); ?> </span>
                                <span class="opalestate-review__dash">–</span>
                                <time class="opalestate-review__published-date" datetime="<?php echo esc_attr( get_comment_date( 'c', $comment->comment_ID ) ); ?>">
                                    <?php echo esc_html( get_comment_date( opalestate_date_format(), $comment->comment_ID ) ); ?>
                                </time>
                            </p>

                            <?php
                            $cpt_feature = 'opalestate_rating_ft';
                            $average     = Opalestate_Rating_Helper::get_average_rating( $comment, $cpt_feature );
                            $features    = opalestate_get_property_rating_features();
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
                                                    <label><?php echo esc_html( $feature_title ); ?></label>
                                                    <span><?php echo absint( get_comment_meta( $comment->comment_ID, $feature_key, true ) ); ?></span>
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
                            </div>

                            <div class="description">
                                <p><?php echo esc_html( $comment->comment_content ); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-sm-3 opalestate-my-reviews-item__property">
                        <?php $post_title = get_the_title( $comment->comment_post_ID ); ?>
                        <?php if ( $post_title ) : ?>
                            <h6 class="opalestate-my-reviews-item_property-name">
                                <a href="<?php echo esc_url( get_permalink( $comment->comment_post_ID ) ); ?>" title="<?php echo esc_attr( $post_title ); ?>" target="_blank">
                                    <?php echo esc_html( $post_title ); ?>
                                </a>
                            </h6>
                        <?php endif; ?>

                        <div class="opalestate-my-reviews-item_property-view">
                            <a href="<?php echo esc_url( get_permalink( $comment->comment_post_ID ) ); ?>" title="<?php echo esc_attr( $post_title ); ?>" target="_blank">
                                <?php esc_html_e( 'View Property', 'opalestate-pro' ); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </li><!-- #comment-## -->
		<?php endforeach; ?>
    </ol>
</div>
