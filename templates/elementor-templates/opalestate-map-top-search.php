<?php if (isset($settings['enable_static']) && $settings['enable_static']): ?>
<div class="opalestate-map-preview-wrap maps-container-fixed">
    <?php else : ?>
    <div class="opalestate-map-preview-wrap">
        <?php endif; ?>
        <div id="opalestate-map-preview" style="min-height:700px">
            <div id="mapView">
                <div class="mapPlaceholder">
                    <!-- <span class="fa fa-spin fa-spinner"></span> <?php //esc_html_e( 'Loading map...', 'opalestate-pro' ); ?> -->
                    <div class="sk-folding-cube">
                        <div class="sk-cube1 sk-cube"></div>
                        <div class="sk-cube2 sk-cube"></div>
                        <div class="sk-cube4 sk-cube"></div>
                        <div class="sk-cube3 sk-cube"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>