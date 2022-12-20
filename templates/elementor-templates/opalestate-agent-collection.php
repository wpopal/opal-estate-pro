<?php
$settings = $this->get_settings_for_display();
extract($settings);
$layout = $settings['item_layout'];
$attrs  = $this->get_render_attribute_string('wrapper-style');
if (isset($_GET['display']) && $_GET['display'] == 'grid') {
    $layout = 'grid';

} elseif (isset($_GET['display']) && $_GET['display'] == 'list') {
    $layout = 'list';
    $attrs  = 'class="column-list"';
}

if (is_front_page()) {
    $paged = (get_query_var('page')) ? get_query_var('page') : 1;
} else {
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
}

$onlyfeatured = 0;

// if ( isset( $_GET['s_agents'] ) ) {
$query = Opalestate_Query::get_agents(["posts_per_page" => $posts_per_page, 'paged' => $paged], $onlyfeatured);
// } else {
// 	$query = OpalEstate_Search::get_search_agents_query();
// }

$form   = $settings['search_form'] ? "search-agents-form-" . $settings['search_form'] : "search-agents-form";
$rowcls = apply_filters('opalestate_row_container_class', 'opal-row');
?>

<?php if ($settings['enable_sortable_bar']): ?>
    <div class="opalesate-archive-top">
        <div class="<?php echo esc_attr($rowcls); ?>">
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="collection-counter">
                    <span><?php echo sprintf(esc_html__('Found %s Agents', 'opalestate-pro'), '<span class="text-primary">' . $query->found_posts . '</span>') ?></span>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6  text-right">
                <div class="opalestate-sortable">
                    <?php echo opalestate_render_sortable_dropdown(); ?>
                </div>
                <?php opalestate_show_display_modes(); ?>
            </div>
        </div>
    </div>
<?php endif; ?>
<div class="agents-collection-wrap">
    <?php if ($query->have_posts()): ?>
        <div class="agents-container">
            <div <?php echo $attrs; ?>>
                <?php $cnt = 0;
                while ($query->have_posts()): $query->the_post(); ?>
                    <div class="column-item ">
                        <?php echo opalestate_load_template_path('content-agent-' . $layout); ?>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
        <?php if (isset($pagination) && $pagination && (!isset($enable_carousel) || !$enable_carousel)): ?>
            <div class="w-pagination"><?php opalestate_pagination($pagination_page_limit); ?></div>
        <?php endif; ?>
    <?php else: ?>
        <div class="agents-results">
            <?php echo opalestate_load_template_path('content-no-results'); ?>
        </div>
    <?php endif; ?>
</div>
<?php wp_reset_postdata(); ?>
