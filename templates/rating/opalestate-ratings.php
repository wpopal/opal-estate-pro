<?php
/**
 * Display single reviews (comments)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! comments_open() ) {
	return;
}

$post_id   = get_the_ID();
$post_type = get_post_type( $post_id );
$supports  = Opalestate_Rating::get_rating_supports();

if ( ! isset( $supports[ $post_type ] ) ) {
	return;
}

switch ( $supports[ $post_type ]['post_type'] ) {
	case 'opalestate_property':
		global $property;
		$object = $property;
		break;

	case 'opalestate_agency':
		global $agency;
		$object = $agency;
		break;

	case 'opalestate_agent':
		global $agent;
		$object = $agent;
		break;
}

if( !is_object($object) ) {
	return; 
}	
$cpt_feature = $supports[ $post_type ]['features_cpt'];
$features    = Opalestate_Rating_Helper::get_features( $cpt_feature );

$counts = [
	5 => 0,
	4 => 0,
	3 => 0,
	2 => 0,
	1 => 0,
];

$average_stats = [];
if ( $features ) {
	foreach ( $features as $feature_slug => $feature_title ) {
		$average_stats[ $feature_slug ] = '0.00';
	}
}

$count         = $object->get_rating_counts();
$counts        = $object->get_rating_count_stats() ? $object->get_rating_count_stats() : $counts;
$average       = $object->get_average_rating();
$average_stats = $object->get_rating_average_stats() ? $object->get_rating_average_stats() : $average_stats;
?>
<div id="reviews" class="opalestate-box-content opalestate-reviews">
    <h4 class="outbox-title opalestate-reviews-title" id="block-reviews">
		<?php esc_html_e( 'Ratings & Reviews', 'opalestate-pro' ); ?>
    </h4>
    <div class="opalestate-box">
        <div id="comments">
            <div class="opalestate-rating-header">
                <div class="opalestate-rating-percent">
					<?php if ( $counts ) : ?>
						<?php foreach ( $counts as $key => $value ) : ?>
							<?php $pc = $count == 0 ? 0 : ( ( $value / $object->get_rating_counts() ) * 100 ); ?>
                            <div class="opalestate-rating-percent__item">
                                <label class="opalestate-rating-percent__label">
									<span class="star-number">
										<?php echo absint( $key ); ?>
									</span>
                                    <span class="star-text">
										<?php esc_html_e( 'star', 'opalestate-pro' ); ?>
									</span>
                                </label>

                                <div class="opalestate-process-bar">
                                    <div class="opalestate-process-bar__item" style="width: <?php echo esc_attr( $pc ); ?>%;">
										<?php echo round( $pc, 2 ); ?>%
                                    </div>
                                </div>
                                <span class="opalestate-process-text">
									<?php echo round( $pc, 0 ); ?>%
								</span>
                            </div>
						<?php endforeach; ?>
					<?php endif; ?>
                </div>

                <div class="opalestate-overall">
                    <div class="opalestate-overall__info">
                        <div class="opalestate-overall__point">
                            <h3 class="point-number">
								<?php echo esc_html( $object->get_average_rating() ? $object->get_average_rating() : '0.00' ); ?>
                            </h3>
                        </div>

                        <div class="opalestate-overall__star">
                            <h5 class="opalestate-overall__heading">
								<?php esc_html_e( 'Overall rating', 'opalestate-pro' ); ?>
                            </h5>

							<?php
							if ( $average ) {
								echo opalestate_get_rating_html( $average ); // WPCS: XSS ok.
							}
							?>
                            <span class="opalestate-overall__rating-count <?php echo ! $count ? 'no-rating' : ''; ?>">
								<?php
								printf(
								/* translators: %s number of ratings */
									_nx(
										'%s rating',
										'%s ratings',
										absint( $count ),
										'rating numbers',
										'opalestate-pro'
									),
									number_format_i18n( absint( $count ) )
								);
								?>
							</span>

							<?php if ( $count ) : ?>
                                <span class="opalestate-overall__rating-desc">
									<?php esc_html_e( 'based on all ratings', 'opalestate-pro' ); ?>
								</span>
							<?php endif; ?>
                        </div>
                    </div>

					<?php if ( $average_stats ) : ?>
                        <div class="opalestate-overall-features">
							<?php foreach ( $average_stats as $feature_slug => $average_stars ) : ?>
								<?php
								$args = [
									'name'        => $feature_slug,
									'post_type'   => $cpt_feature,
									'post_status' => 'publish',
									'numberposts' => 1,
								];

								$feature = get_posts( $args );
								if ( ! $feature || ! isset( $feature[0] ) ) {
									continue;
								}
								?>
                                <div class="opalestate-overall-features__item">
                                    <label class="opalestate-overall-features__label">
										<?php echo esc_html( $feature[0]->post_title ) ?>
                                    </label>

                                    <div class="opalestate-overall-features__percent">
										<?php echo number_format( $average_stars / 5 * 100, 2 ); ?>%
                                    </div>
                                </div>
							<?php endforeach; ?>
                        </div>
					<?php endif; ?>
                </div>
            </div>

            <h5 class="opalestate-review-count">
				<?php
				printf(
				/* translators: %s number of reviews */
					_nx(
						'%s review',
						'%s reviews',
						absint( $object->get_review_count() ),
						'review numbers',
						'opalestate-pro'
					),
					number_format_i18n( absint( $object->get_review_count() ) )
				);
				?>
            </h5>

			<?php if ( have_comments() ) : ?>
                <ol class="commentlist">
					<?php wp_list_comments( apply_filters( $post_type . '_review_list_args', [ 'callback' => 'opalestate_comments' ] ) ); ?>
                </ol>

				<?php
				if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
					echo '<nav class="opalestate-pagination">';
					paginate_comments_links(
						apply_filters(
							'opalestate_comment_pagination_args',
							[
								'prev_text' => '&larr;',
								'next_text' => '&rarr;',
								'type'      => 'list',
							]
						)
					);
					echo '</nav>';
				endif;
				?>
			<?php else : ?>
                <p class="opalestate-noreviews"><?php esc_html_e( 'There are no reviews yet.', 'opalestate-pro' ); ?></p>
			<?php endif; ?>
        </div>

		<?php if ( is_user_logged_in() ) : ?>
            <?php $current_user_id = absint( get_current_user_id() );?>
			<?php if ( $current_user_id !== absint( $post->post_author ) ) : ?>
				<?php
				$count_comment_reviewed = get_comments( [
					'author__in' => $current_user_id,
					'post_id'    => $object->get_id(),
					'status'     => 'approve',
					'count'      => true,
				] );
				?>
				<?php if ( ! $count_comment_reviewed ) : ?>
                    <div id="review_form_wrapper">
                        <div id="review_form">
							<?php
							$commenter = wp_get_current_commenter();

							$comment_form = [
								/* translators: %s is property title */
								'title_reply'         => have_comments() ? esc_html__( 'Add a review', 'opalestate-pro' ) : sprintf( esc_html__( 'Be the first to review &ldquo;%s&rdquo;', 'opalestate-pro' ), get_the_title() ),
								/* translators: %s is property title */
								'title_reply_to'      => esc_html__( 'Leave a Reply to %s', 'opalestate-pro' ),
								'title_reply_before'  => '<h5 id="reply-title" class="comment-reply-title">',
								'title_reply_after'   => '</h5>',
								'comment_notes_after' => '',
								'fields'              => [
									'author' => '<p class="comment-form-author"><label for="author">' . esc_html__( 'Name', 'opalestate-pro' ) . '&nbsp;<span class="required">*</span></label> ' .
									            '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" required /></p>',
									'email'  => '<p class="comment-form-email"><label for="email">' . esc_html__( 'Email', 'opalestate-pro' ) . '&nbsp;<span class="required">*</span></label> ' .
									            '<input id="email" name="email" type="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30" required /></p>',
								],
								'label_submit'        => esc_html__( 'Submit', 'opalestate-pro' ),
								'logged_in_as'        => '',
								'comment_field'       => '',
							];

							if ( $features ) {
								$feature_inputs = '';
								foreach ( $features as $feature_slug => $feature_title ) {
									$feature_inputs .= '<div class="comment-form-rating"><label for="' . $cpt_feature . '_' . $feature_slug . '">' . esc_html( $feature_title ) . '</label><select class="opalestate_rating" name="' . $cpt_feature . '_' . $feature_slug . '" id="' . $cpt_feature . '_' . $feature_slug . '" required>
							<option value="">' . esc_html__( 'Rate&hellip;', 'opalestate-pro' ) . '</option>
							<option value="5">' . esc_html__( 'Perfect', 'opalestate-pro' ) . '</option>
							<option value="4">' . esc_html__( 'Good', 'opalestate-pro' ) . '</option>
							<option value="3">' . esc_html__( 'Average', 'opalestate-pro' ) . '</option>
							<option value="2">' . esc_html__( 'Not that bad', 'opalestate-pro' ) . '</option>
							<option value="1">' . esc_html__( 'Very poor', 'opalestate-pro' ) . '</option>
						</select></div>';
								}

								$comment_form['comment_field'] = $feature_inputs;
							} else {
								$comment_form['comment_field'] = '<div class="comment-form-rating"><label for="opalestate_rating">' . esc_html__( 'Your rating', 'opalestate-pro' ) . '</label><select class="opalestate_rating" name="opalestate_rating" id="opalestate_rating" required>
							<option value="">' . esc_html__( 'Rate&hellip;', 'opalestate-pro' ) . '</option>
							<option value="5">' . esc_html__( 'Perfect', 'opalestate-pro' ) . '</option>
							<option value="4">' . esc_html__( 'Good', 'opalestate-pro' ) . '</option>
							<option value="3">' . esc_html__( 'Average', 'opalestate-pro' ) . '</option>
							<option value="2">' . esc_html__( 'Not that bad', 'opalestate-pro' ) . '</option>
							<option value="1">' . esc_html__( 'Very poor', 'opalestate-pro' ) . '</option>
						</select></div>';
							}

							$comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . esc_html__( 'Your review',
									'opalestate-pro' ) . '&nbsp;<span class="required">*</span></label><textarea id="comment" name="comment" cols="45" rows="8" required></textarea></p>';

							comment_form( apply_filters( 'opalestate_property_review_comment_form_args', $comment_form ) );
							?>
                        </div>
                    </div>
				<?php else : ?>
                    <p class="opalestate-reviewed-notice"><?php esc_html_e( 'Your review already exists!', 'opalestate-pro' ); ?></p>
				<?php endif; ?>
            <?php else : ?>
                <p class="opalestate-reviewed-notice"><?php esc_html_e( 'You cannot write review on your own post.', 'opalestate-pro' ); ?></p>
            <?php endif; ?>
		<?php else : ?>
            <p class="opalestate-login-required">
				<?php esc_html_e( 'You must be logged in to review.', 'opalestate-pro' ); ?>
                <a href="#" class="opalestate-need-login"><?php esc_html_e( 'Click here to login', 'opalestate-pro' ); ?></a>
            </p>
		<?php endif; ?>

        <div class="clear"></div>
    </div>
</div>
