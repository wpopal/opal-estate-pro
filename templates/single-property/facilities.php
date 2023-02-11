<?php
global $property, $post;

$facilities = $property->get_facilities();

?>

<?php if ('on' === $property->get_block_setting('facilities') && $facilities && isset($facilities[0]) && !empty($facilities[0])): ?>
    <div class="property-facilities box-inner-summary">
        <h5 class="list-group-item-heading"><?php esc_html_e('Facilities', 'opalestate-pro'); ?></h5>
        <div class="list-group-item-text">
            <div class="<?php echo apply_filters('opalestate_row_container_class', 'opal-row'); ?>">
                <?php foreach ($facilities as $facility):
                    $facilities_key   = isset($facility[OPALESTATE_PROPERTY_PREFIX . 'public_facilities_key']) ? $facility[OPALESTATE_PROPERTY_PREFIX . 'public_facilities_key'] : '';
                    $facilities_value = isset($facility[OPALESTATE_PROPERTY_PREFIX . 'public_facilities_value']) ? $facility[OPALESTATE_PROPERTY_PREFIX . 'public_facilities_value'] : '';
                    if ($facilities_key && $facilities_value) :
                        ?>
                        <div class="col-lg-6 col-sm-6 active">
                            <span>
                            <i class="fa fa-check"></i>
							<?php echo esc_html($facilities_key); ?> :
                        </span>

                            <span><?php echo esc_html($facilities_value); ?></span>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>
