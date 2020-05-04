<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'CMB2_Uploader_Button' ) ) {
	/**
	 * Class CMB2_Uploader_Button
	 */
	class CMB2_Uploader_Button {

		/**
		 * Constructor
		 */
		public function __construct() {
			add_action( 'cmb2_render_uploader', [ $this, 'callback' ], 10, 5 );
			add_action( 'admin_head', [ $this, 'admin_head' ] );
			add_action( 'wp_enqueue_scripts', [ $this, 'scripts_styles' ], 99 );
		}

		/**
		 * Register javascript file for processing upload images/files
		 */
		public function scripts_styles() {
			wp_register_script(
				'cmb2-uploader',
				OPALESTATE_PLUGIN_URL . 'assets/js/frontend/uploader.js',
				[
					'jquery',
				],
				'4.4.3',
				true
			);
		}

		/**
		 * Render Preview is image or icon with its name
		 */
		private function render_image_or_icon( $escaped_value, $show_icon ) {
			$cls = $show_icon ? "preview-icon" : "preview-image";
			echo '<div class="inner ' . $cls . '">';
			echo '      <span class="btn-close fa fa-close"></span> ';
			if ( $show_icon ) {
				echo '<i class="fas fa-paperclip"></i> ' . basename( get_attached_file( $escaped_value ) );
			} else {
				echo wp_get_attachment_image( $escaped_value, 'thumbnail' );
			}

			echo '</div>';
		}

		/**
		 * Render content input field.
		 */
		public function callback( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
			wp_enqueue_script( 'cmb2-uploader' );

			$field_name = $field->_name();

			$args = [
				'type'  => 'checkbox',
				'id'    => $field_name,
				'name'  => $field_name,
				'desc'  => '',
				'value' => 'on',
			];

			if ( $escaped_value == 'on' || $escaped_value == 1 ) {
				$args['checked'] = 'checked';
			}

			$single = isset( $field->args['single'] ) && $field->args['single'];
			$attrs  = $single ? "" : 'multiple="multiple"';
			$size   = '';


			if ( isset( $field->args['accept'] ) && $field->args['accept'] ) {
				$attrs .= ' accept="' . $field->args['accept'] . '" ';


				$info = [
					'size'   => opalestate_options( 'upload_other_max_size', 0.5 ),
					'number' => opalestate_options( 'upload_other_max_files', 10 ),
				];

				$class = 'upload-file-wrap';
			} else {
				$attrs .= ' accept="image/*"  ';
				$class = 'upload-image-wrap';

				$info = [
					'size'   => opalestate_options( 'upload_image_max_size', 0.5 ),
					'number' => opalestate_options( 'upload_image_max_files', 10 ),
				];
			}
			if ( $single ) {
				$info['number'] = 1;
			}
			$show_icon = isset( $field->args['show_icon'] ) && $field->args['show_icon'] ? $field->args['show_icon'] : false;
			?>
            <div class="cmb2-uploader-files <?php echo $class; ?>" data-name="<?php echo $args['id']; ?>" data-single="<?php echo $single; ?>" data-show-icon="<?php echo $show_icon; ?>">
				<?php if ( $escaped_value && is_array( $escaped_value ) ): ?>
					<?php foreach ( $escaped_value as $key => $url ): ?>
                        <div class="uploader-item-preview">

							<?php echo $this->render_image_or_icon( $key, $show_icon ); ?>
                            <input type="hidden" name="<?php echo $field_name; ?>[<?php echo $key; ?>]" value="<?php echo $url; ?>">
                        </div>
					<?php endforeach; ?>
				<?php elseif ( $escaped_value && ! is_array( $escaped_value ) ): ?>
                    <div class="uploader-item-preview">

						<?php echo $this->render_image_or_icon( $escaped_value, $show_icon ); ?>

                        <input type="hidden" name="<?php echo $field_name; ?>" value="<?php echo $escaped_value; ?>">
                    </div>
				<?php elseif ( empty( $escaped_value ) && isset( $field->args['value'] ) && (int) $field->args['value'] ):
					$image_id = $field->args['value'];
					?>
                    <div class="uploader-item-preview">

						<?php echo $this->render_image_or_icon( $image_id, $show_icon ); ?>
                        <input type="hidden" name="<?php echo $field_name; ?>" value="<?php echo $image_id; ?>">
                    </div>
				<?php endif; ?>
                <div class="button-placehold">
                    <div class="button-placehold-content">
                        <i class="fa fa-plus"></i>
                        <span><?php esc_html_e( "Upload", "opalestate-pro" ); ?></span>
                    </div>
                </div>
                <input type="file" name="<?php echo $args['id']; ?>" <?php echo $attrs; ?> class="select-file" style="visibility: hidden;">


            </div>
            <p class="cmb2-metabox-description">
                <i>
					<?php
					echo sprintf( esc_html__( 'Allow upload file have size < %s MB and maximum number of files: %s', 'opalestate-pro' ),
						'<strong>' . $info['size'] . '</strong>', '<strong>' . $info['number'] . '</strong>' ); ?>
                </i>
            </p>
			<?php
		}

		/**
		 *
		 */
		public function admin_head() {
			?>
			<?php
		}
	}

	$uploader = new CMB2_Uploader_Button();
}
