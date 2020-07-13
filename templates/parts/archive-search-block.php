<?php
$default_search_form = opalestate_get_option( 'default_search_form', 'collapse-city' );
$layout              = apply_filters( 'opalestate_archive_search_block_form_layout', $default_search_form );
$settings            = [];
?>
<div class="opalestate-archive-search-block">
    <div class="container">
        <div class="search-properies-form">
			<?php echo opalestate_load_template_path( 'search-box/' . $layout, $settings ); ?>
        </div>
    </div>
</div>
