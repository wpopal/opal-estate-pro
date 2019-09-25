<?php
/**
 * Opalestate_Field_Iconpicker
 *
 * @package    opalestate
 * @author     Opal  Team <info@wpopal.com >
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Opalestate_Field_Iconpicker {

	/**
	 * Current version number
	 */
	const VERSION = '1.0.0';

	/**
	 * Initialize the plugin by hooking into CMB2
	 */
	public static function init() {
		add_filter( 'cmb2_render_opal_iconpicker', [ __CLASS__, 'render_iconpicker' ], 10, 5 );
		add_filter( 'cmb2_sanitize_opal_iconpicker', [ __CLASS__, 'sanitize_map' ], 10, 4 );
	}

	/**
	 * Render field
	 */
	public static function render_iconpicker( $field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object ) {
		self::setup_admin_scripts();

		$users = $field->value;

		echo '<div class="opalestate-add-user-field ' . apply_filters( 'opalestate_row_container_class', 'row opal-row' ) . '"> '; ?>
        <div class="col-lg-12">
            <h5 class=""><?php esc_html_e( 'As an author, you can add other users to your agency.', 'opalestate-pro' ); ?></h5>
            <div>
                <p><?php esc_html_e( 'Add someone to your agency, please enter extractly username in below input:', 'opalestate-pro' ); ?></p>

                <div class="<?php echo apply_filters( 'opalestate_row_container_class', 'row opal-row' ); ?>">

                    <div class="col-lg-8"><input class="regular-text opalestate-adduser-search" name="opalestate_adduser_search" id="opalestate_adduser_search" value="" type="text"></div>
                    <div class="col-lg-4"><input name="search" class="button button-primary button-large pull-left" id="publish" value="<?php esc_html_e( 'add', 'opalestate-pro' ); ?>" type="button">
                    </div>
                </div>
                <div class="clear clearfix"></div>
            </div>
            <div class="adduser-team">
				<?php if ( $users ): $users = array_unique( $users ); ?>
					<?php foreach ( $users as $user_id ): $user = get_user_by( 'id', $user_id );
						$user = $user->data ?>
                        <div class="user-team">
                            <input type="hidden" name="<?php echo $field->args( '_name' ) ?>[]" value="<?php echo $user_id; ?>">
                            <div>
                                <img src="<?php echo get_avatar_url( $user_id ); ?>">
                                <a href="<?php get_author_posts_url( $user_id ); ?>" target="_blank"> <?php echo $user->user_login; ?> </a></div>
                            <div><span class="remove-user" data-alert="<?php esc_html_e( 'Are you sure to delete this', 'opalestate-pro' ); ?>"><?php esc_html_e( 'Remove',
										'opalestate-pro' ); ?></span></div>
                        </div>
					<?php endforeach; ?>

				<?php endif; ?>
            </div>
        </div>

        <script type="text/html" id="tmpl-adduser-team-template">
            <div class="user-team">
                <input type="hidden" name="<?php echo $field->args( '_name' ) ?>[]" value="{{{data.ID}}}">
                <div><img src="{{{data.avatar}}}"> <a href="{{{data.author_link}}}" target="_blank"> {{{data.user_login}}} </a></div>
                <div><span class="remove-user" data-alert="<?php esc_html_e( 'Are you sure to delete this', 'opalestate-pro' ); ?>"><?php esc_html_e( 'Remove', 'opalestate-pro' ); ?></span></div>
            </div>
        </script>

		<?php echo '</div>';
	}

	/**
	 * Optionally save the latitude/longitude values into two custom fields
	 */
	public static function sanitize_map( $override_value, $value, $object_id, $field_args ) {
		return $value;
	}

	/**
	 * Enqueue scripts and styles
	 */
	public static function setup_admin_scripts() {
		wp_enqueue_script( 'opalestate-adduser', plugins_url( 'assets/script.js', __FILE__ ), [], self::VERSION );
		wp_enqueue_style( 'opalestate-adduser', plugins_url( 'assets/style.css', __FILE__ ), [], self::VERSION );
	}
}

Opalestate_Field_Iconpicker::init();
