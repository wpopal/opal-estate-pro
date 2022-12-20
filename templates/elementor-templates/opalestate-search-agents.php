<?php
$settings = $this->get_settings_for_display();
$layout   = $settings['item_layout'];
$form     = $settings['search_form'] ? "search-agents-form-" . $settings['search_form'] : "search-agents-form";

?>
<div class="search-agents-form-wrap">
    <?php echo opalestate_load_template_path('parts/' . $form, array('current_uri' => $settings['current_uri'])); ?>
</div>
 