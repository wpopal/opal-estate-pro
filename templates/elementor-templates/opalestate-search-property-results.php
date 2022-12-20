<?php
$display = [
    'style'      => $settings['style'],
    'style_list' => $settings['style_list'],
    'column'     => $settings['column'],
];

?>
<div class="opalesate-properties-ajax opalesate-properties-results" data-mode="html">
    <?php echo opalestate_load_template_path('shortcodes/ajax-map-search-result', $display); ?>
</div>
