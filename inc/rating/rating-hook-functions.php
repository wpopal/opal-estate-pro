<?php
/**
 * Reviews for opalestate.
 *
 * @see opalestate_review_display_gravatar()
 * @see opalestate_review_display_rating()
 * @see opalestate_review_display_meta()
 * @see opalestate_review_display_comment_text()
 */
add_action('opalestate_review_before', 'opalestate_review_display_gravatar', 10);
add_action('opalestate_review_before_comment_meta', 'opalestate_review_display_meta', 10);
add_action('opalestate_review_meta', 'opalestate_review_display_rating', 10);
add_action('opalestate_review_comment_text', 'opalestate_review_display_comment_text', 10);
