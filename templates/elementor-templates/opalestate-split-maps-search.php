<?php
$paged  = (get_query_var('paged')) ? get_query_var('paged') : 1;
$rowcls = apply_filters('opalestate_row_container_class', 'opal-row');

$atts = [
    'paged'       => $paged,
    'search_form' => $settings['search_form'],
];
?>
<?php echo opalestate_load_template_path('shortcodes/search-split-maps', $atts); ?>
