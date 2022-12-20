<?php
$settings = array_merge([
    'limit'      => 5,
    'column'     => 3,
    'paged'      => 1,
    'showsortby' => false,
    'style'      => 'grid',
    'orderby'    => 'post_date',
    'order'      => 'DESC',
], $settings);
extract($settings);

if (is_front_page()) {
    $paged = (get_query_var('page')) ? get_query_var('page') : 1;
} else {
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
}

$args = [
    'posts_per_page' => $posts_per_page,
    'paged'          => $paged,
    'cities'         => $cities,
    'categories'     => $categories,
    'operator'       => $cat_operator,
    'types'          => $types,
    'statuses'       => $statuses,
    'showmode'       => $showmode,
    'labels'         => $labels,
    'orderby'        => $orderby,
    'order'          => $order,
];

$query = Opalestate_Query::get_property_query($args);

$class = 'column-item';
?>
<?php if (isset($showsortby) && $showsortby): ?>
    <?php echo opalestate_load_template_path('collection-navigator', ['mode' => 'list']); ?>
<?php endif; ?>
<div class="opalesate-property-collection">

    <?php if ($query->have_posts()): ?>
        <div <?php echo $this->get_render_attribute_string('wrapper-style'); ?>>
            <?php while ($query->have_posts()) : $query->the_post(); ?>
                <div class="column-item">
                    <?php echo opalestate_load_template_path('content-property-' . $style); ?>
                </div>
            <?php endwhile; ?>
        </div>

        <?php if (isset($pagination) && $pagination && (!isset($enable_carousel) || !$enable_carousel)): ?>
            <div class="w-pagination"><?php opalestate_pagination($pagination_page_limit); ?></div>
        <?php endif; ?>
    <?php else: ?>
        <?php echo opalestate_load_template_path('content-no-results'); ?>
    <?php endif; ?>
</div>
<?php wp_reset_postdata(); ?>
