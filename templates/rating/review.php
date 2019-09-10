<?php
/**
 * Review Comments Template
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">

	<div id="comment-<?php comment_ID(); ?>" class="comment_container">

		<?php
		/**
		 * The opalestate_review_before hook
		 *
		 * @hooked opalestate_review_display_gravatar - 10
		 */
		do_action( 'opalestate_review_before', $comment );
		?>

		<div class="comment-text">

			<?php
			/**
			 * The opalestate_review_before_comment_meta hook.
			 *
			 * @hooked opalestate_review_display_rating - 10
			 */
			do_action( 'opalestate_review_before_comment_meta', $comment );

			/**
			 * The opalestate_review_meta hook.
			 *
			 * @hooked opalestate_review_display_meta - 10
			 */
			do_action( 'opalestate_review_meta', $comment );

			do_action( 'opalestate_review_before_comment_text', $comment );

			/**
			 * The opalestate_review_comment_text hook
			 *
			 * @hooked opalestate_review_display_comment_text - 10
			 */
			do_action( 'opalestate_review_comment_text', $comment );

			do_action( 'opalestate_review_after_comment_text', $comment ); ?>

		</div>
	</div>
