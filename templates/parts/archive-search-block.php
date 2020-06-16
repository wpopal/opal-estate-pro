<?php
$layout   = apply_filters( 'opalestate_archive_search_block_form_layout', 'collapse-city' );
$settings = [];
?>
<div class="opalestate-archive-search-block">
    <div class="container">
        <div class="search-properies-form">
			<?php echo opalestate_load_template_path( 'search-box/' . $layout, $settings ); ?>
        </div>
    </div>
</div>
