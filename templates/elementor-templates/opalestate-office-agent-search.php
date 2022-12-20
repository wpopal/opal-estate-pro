<?php
extract($settings);
return;
?>
<?php if (isset($description)): ?>
    <div class="search-agent-form-description"><?php echo $description; ?></div>
<?php endif; ?>

<?php if (class_exists("Opalestate_Template_Loader")) : ?>
    <div class="opalestate-search-tabs">
        <ul class="nav nav-tabs tab-v8" role="tablist">
            <li class="active">
                <a aria-expanded="false" href="#search-agent" role="tab" class="tab-item">
                    <span><?php esc_html_e('Find An Agent', 'opalestate-pro'); ?></span>
                </a>
            </li>
            <li>
                <a aria-expanded="true" href="#search-agency" role="tab" class="tab-item">
                    <span><?php esc_html_e('Find An Agency', 'opalestate-pro'); ?></span>
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade out active in" id="search-agent">
                <?php echo opalestate_load_template_path('parts/search-agents-form'); ?>
            </div>
            <div class="tab-pane fade out" id="search-agency">
                <?php echo opalestate_load_template_path('parts/search-agency-form'); ?>
            </div>
        </div>
    </div>
<?php endif; ?>


