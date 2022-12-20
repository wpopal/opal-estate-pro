<?php
$args = [
    'number'  => $settings['limit'],
    'orderby' => $settings['orderby'],
    'order'   => $settings['order'],
];

if ($settings['categories']) {
    $args['slug'] = $settings['categories'];
}

$terms = Opalestate_Taxonomy_City::get_list($args);

$attrs = $this->get_render_attribute_string('wrapper-style');

?>
<div class="city-list-collection ">
    <div <?php echo wp_kses_post($attrs); ?>>
        <?php foreach ($terms as $city): ?>
            <?php
            $tax_link = get_term_link($city->term_id);
            $image    = wp_get_attachment_image_url(get_term_meta($city->term_id, 'opalestate_city_image_id', true), 'full');
            ?>
            <div class="column-item  property-city ">
                <a href="<?php echo esc_url($tax_link); ?>" class="city-overlay"></a>
                <?php
                $style = '';
                if ($image) {
                    $style = 'style="background-image:url(' . esc_url($image) . ')"';
                } else {
                    $style = 'style="background-image:url(' . opalestate_get_image_placeholder_src() . ')"';
                }
                ?>

                <div class="property-city-bg" <?php echo wp_kses_post($style); ?>>
                </div>
                <div class="static-content">
                    <div class="property-city-info text-center">
                        <?php if ($city->name) : ?>
                            <h4 class="property-city-title">
                                <a href="<?php echo esc_url($tax_link); ?>"><?php echo esc_html($city->name); ?></a>
                            </h4>
                        <?php endif; ?>

                        <?php if ($city->count) : ?>
                            <div class="property-city-count">
                                <?php
                                printf(
                                /* translators: 1: number of properties */
                                    _nx(
                                        '%1$s Property',
                                        '%1$s Properties',
                                        $city->count,
                                        'count properties',
                                        'opalestate-pro'
                                    ),
                                    number_format_i18n($city->count)
                                );
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
