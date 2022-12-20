<?php
$args = [
    'number'  => $settings['limit'],
    'orderby' => $settings['orderby'],
    'order'   => $settings['order'],
];

if ($settings['categories']) {
    $args['slug'] = $settings['categories'];
}

$terms = Opalestate_Taxonomy_Categories::get_list($args);

$attrs = $this->get_render_attribute_string('wrapper-style');

?>
<div class="category-list-collection ">
    <div <?php echo wp_kses_post($attrs); ?>>
        <?php foreach ($terms as $category): ?>
            <?php
            $tax_link = get_term_link($category->term_id);
            $image    = wp_get_attachment_image_url(get_term_meta($category->term_id, 'opalestate_category_image_id', true), 'full');
            ?>
            <div class="column-item  property-category ">
                <a href="<?php echo esc_url($tax_link); ?>" class="category-overlay"></a>
                <?php
                $style = '';
                if ($image) {
                    $style = 'style="background-image:url(' . esc_url($image) . ')"';
                } else {
                    $style = 'style="background-image:url(' . opalestate_get_image_placeholder_src() . ')"';
                }
                ?>

                <div class="property-category-bg" <?php echo wp_kses_post($style); ?>>
                </div>
                <div class="static-content">
                    <div class="property-category-info text-center">
                        <?php if ($category->name) : ?>
                            <h4 class="property-category-title">
                                <a href="<?php echo esc_url($tax_link); ?>"><?php echo esc_html($category->name); ?></a>
                            </h4>
                        <?php endif; ?>

                        <?php if ($category->count) : ?>
                            <div class="property-category-count">
                                <?php
                                printf(
                                /* translators: 1: number of properties */
                                    _nx(
                                        '%1$s Property',
                                        '%1$s Properties',
                                        $category->count,
                                        'count properties',
                                        'opalestate-pro'
                                    ),
                                    number_format_i18n($category->count)
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
