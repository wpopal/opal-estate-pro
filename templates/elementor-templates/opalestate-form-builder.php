<?php
/**
 * The template for form builder.
 *
 * @version 1.0.0
 */

defined('ABSPATH') || exit;

?>
<form class="opalestate-search-form" action="<?php echo opalestate_get_search_link(); ?>" method="get">
    <?php if (isset($settings['fields'])) : ?>
        <div class="opal-row">
            <?php foreach ($settings['fields'] as $field): ?>
                <?php if ($field['field']): ?>
                    <div class="col-md-<?php echo $field['column']; ?> col-sm-12">
                        <?php echo opalestate_load_template_path('search-box/fields/' . $field['field']); ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php do_action('opalestate_after_search_properties_form'); ?>
</form>
<?php if (0): ?>
    <div class="opalesate-properties-ajax opalesate-properties-results" data-mode="html">
        <?php echo opalestate_load_template_path('shortcodes/ajax-map-search-result'); ?>
    </div>
<?php endif; ?>
