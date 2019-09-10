<?php echo opalestate_load_template_path( 'shortcodes/search-properties' );
$display = '';
?>
<div class="container">
    <div class="opalesate-properties-ajax opalesate-properties-results" data-mode="html">
		<?php echo opalestate_load_template_path( 'shortcodes/ajax-map-search-result', $display ); ?>
    </div>
</div>
