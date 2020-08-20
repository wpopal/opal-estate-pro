<?php if ( ! opalesate_check_has_add_listing( $user_id ) ): ?>
    <div class="alert alert-warning">
        <p><?php esc_html_e( 'Your package has 0 listings left, you can\'t add any more until you upgrade your plan', 'opalestate-pro' ); ?></p>
        <p><a href="<?php echo opalmembership_get_membership_page_uri(); ?>" class="btn btn-primary"><?php esc_html_e( 'Click this link to see plans', 'opalestate-pro' ); ?></a></p>
    </div>
<?php endif; ?>
