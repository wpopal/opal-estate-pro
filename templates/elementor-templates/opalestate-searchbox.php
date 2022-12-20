<?php
if (!class_exists('OpalEstate_Search')) {
    return;
}

?>
<div class="search-properies-form">
    <?php echo opalestate_load_template_path('search-box/' . $settings['style'], $settings); ?>
</div>
