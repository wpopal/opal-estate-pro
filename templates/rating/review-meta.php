<?php
/**
 * The template to display the reviewers meta data (name, verified owner, review date)
 */

defined( 'ABSPATH' ) || exit;

global $comment;

if ( '0' === $comment->comment_approved ) { ?>

	<p class="meta">
		<em class="opalestate-review__awaiting-approval">
			<?php esc_html_e( 'Your review is awaiting approval', 'opalestate-pro' ); ?>
		</em>
	</p>

<?php } else { ?>

	<p class="meta">
		<span class="opalestate-review__author"><?php comment_author(); ?> </span>
		<span class="opalestate-review__dash">&ndash;</span> <time class="opalestate-review__published-date" datetime="<?php echo esc_attr( get_comment_date( 'c' ) ); ?>"><?php echo esc_html(
		        get_comment_date( opalestate_date_format() ) ); ?></time>
	</p>

<?php
}
