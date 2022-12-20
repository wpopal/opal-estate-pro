<div class="wrap" id="opalestate-add-ons">
    <h1><?php esc_html_e('Opal Estate Add-ons', 'opalestate-pro'); ?></h1>

    <p><?php esc_html_e('The following Add-ons extend the functionality of Opal Estate.', 'opalestate-pro'); ?></p>

    <?php
    $tag = apply_filters('opalestate_addons_tag', 'opalestate_addon');

    if (!function_exists('plugins_api')) {
        require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
    }

    $plugins_api = plugins_api('query_plugins', [
        'tag' => $tag,
    ]);

    if (!is_wp_error($plugins_api)) {
        $plugins             = $plugins_api->plugins;
        $plugins_allowedtags = [
            'a'       => [
                'href'   => [],
                'title'  => [],
                'target' => [],
            ],
            'abbr'    => ['title' => []],
            'acronym' => ['title' => []],
            'code'    => [],
            'pre'     => [],
            'em'      => [],
            'strong'  => [],
            'ul'      => [],
            'ol'      => [],
            'li'      => [],
            'p'       => [],
            'br'      => [],
        ];

        $plugins_group_titles = [
            'Performance' => _x('Performance', 'Plugin installer group title', 'opalestate-pro'),
            'Social'      => _x('Social', 'Plugin installer group title', 'opalestate-pro'),
            'Tools'       => _x('Tools', 'Plugin installer group title', 'opalestate-pro'),
        ];
        $group                = null;
        ?>
        <?php if ($plugins) : ?>
            <div id="the-list">
                <?php foreach ($plugins as $plugin) : ?>
                    <?php
                    if (is_object($plugin)) {
                        $plugin = (array)$plugin;
                    }

                    // Display the group heading if there is one
                    if (isset($plugin['group']) && $plugin['group'] != $group) {
                        if (isset($this->groups[$plugin['group']])) {
                            $group_name = $this->groups[$plugin['group']];
                            if (isset($plugins_group_titles[$group_name])) {
                                $group_name = $plugins_group_titles[$group_name];
                            }
                        } else {
                            $group_name = $plugin['group'];
                        }

                        // Starting a new group, close off the divs of the last one
                        if (!empty($group)) {
                            echo '</div></div>';
                        }

                        echo '<div class="plugin-group"><h3>' . esc_html($group_name) . '</h3>';
                        // needs an extra wrapping div for nth-child selectors to work
                        echo '<div class="plugin-items">';

                        $group = $plugin['group'];
                    }
                    $title = wp_kses($plugin['name'], $plugins_allowedtags);

                    // Remove any HTML from the description.
                    $description = strip_tags($plugin['short_description']);
                    $version     = wp_kses($plugin['version'], $plugins_allowedtags);

                    $name = strip_tags($title . ' ' . $version);

                    $author = wp_kses($plugin['author'], $plugins_allowedtags);
                    if (!empty($author)) {
                        $author = ' <cite>' . sprintf(__('By %s', 'opalestate-pro'), $author) . '</cite>';
                    }

                    $requires_php = isset($plugin['requires_php']) ? $plugin['requires_php'] : null;
                    $requires_wp  = isset($plugin['requires']) ? $plugin['requires'] : null;

                    $compatible_php = is_php_version_compatible($requires_php);
                    $compatible_wp  = is_wp_version_compatible($requires_wp);
                    $tested_wp      = (empty($plugin['tested']) || version_compare(get_bloginfo('version'), $plugin['tested'], '<='));

                    $action_links = [];

                    if (current_user_can('install_plugins') || current_user_can('update_plugins')) {
                        $status = install_plugin_install_status($plugin);

                        switch ($status['status']) {
                            case 'install':
                                if ($status['url']) {
                                    if ($compatible_php && $compatible_wp) {
                                        $action_links[] = sprintf(
                                            '<a class="install-now button" data-slug="%s" href="%s" aria-label="%s" data-name="%s">%s</a>',
                                            esc_attr($plugin['slug']),
                                            esc_url($status['url']),
                                            /* translators: %s: plugin name and version */
                                            esc_attr(sprintf(__('Install %s now', 'opalestate-pro'), $name)),
                                            esc_attr($name),
                                            __('Install Now', 'opalestate-pro')
                                        );
                                    } else {
                                        $action_links[] = sprintf(
                                            '<button type="button" class="button button-disabled" disabled="disabled">%s</button>',
                                            _x('Cannot Install', 'plugin', 'opalestate-pro')
                                        );
                                    }
                                }
                                break;

                            case 'update_available':
                                if ($status['url']) {
                                    if ($compatible_php && $compatible_wp) {
                                        $action_links[] = sprintf(
                                            '<a class="update-now button aria-button-if-js" data-plugin="%s" data-slug="%s" href="%s" aria-label="%s" data-name="%s">%s</a>',
                                            esc_attr($status['file']),
                                            esc_attr($plugin['slug']),
                                            esc_url($status['url']),
                                            /* translators: %s: plugin name and version */
                                            esc_attr(sprintf(__('Update %s now', 'opalestate-pro'), $name)),
                                            esc_attr($name),
                                            __('Update Now', 'opalestate-pro')
                                        );
                                    } else {
                                        $action_links[] = sprintf(
                                            '<button type="button" class="button button-disabled" disabled="disabled">%s</button>',
                                            _x('Cannot Update', 'plugin', 'opalestate-pro')
                                        );
                                    }
                                }
                                break;

                            case 'latest_installed':
                            case 'newer_installed':
                                if (is_plugin_active($status['file'])) {
                                    $action_links[] = sprintf(
                                        '<button type="button" class="button button-disabled" disabled="disabled">%s</button>',
                                        _x('Active', 'plugin', 'opalestate-pro')
                                    );
                                } elseif (current_user_can('activate_plugin', $status['file'])) {
                                    $button_text = __('Activate', 'opalestate-pro');
                                    /* translators: %s: plugin name */
                                    $button_label = _x('Activate %s', 'plugin', 'opalestate-pro');
                                    $activate_url = add_query_arg(
                                        [
                                            '_wpnonce' => wp_create_nonce('activate-plugin_' . $status['file']),
                                            'action'   => 'activate',
                                            'plugin'   => $status['file'],
                                        ],
                                        network_admin_url('plugins.php')
                                    );

                                    if (is_network_admin()) {
                                        $button_text = __('Network Activate', 'opalestate-pro');
                                        /* translators: %s: plugin name */
                                        $button_label = _x('Network Activate %s', 'plugin', 'opalestate-pro');
                                        $activate_url = add_query_arg(['networkwide' => 1], $activate_url);
                                    }

                                    $action_links[] = sprintf(
                                        '<a href="%1$s" class="button activate-now" aria-label="%2$s">%3$s</a>',
                                        esc_url($activate_url),
                                        esc_attr(sprintf($button_label, $plugin['name'])),
                                        $button_text
                                    );
                                } else {
                                    $action_links[] = sprintf(
                                        '<button type="button" class="button button-disabled" disabled="disabled">%s</button>',
                                        _x('Installed', 'plugin', 'opalestate-pro')
                                    );
                                }
                                break;
                        }
                    }

                    $details_link = self_admin_url(
                        'plugin-install.php?tab=plugin-information&amp;plugin=' . $plugin['slug'] .
                        '&amp;TB_iframe=true&amp;width=600&amp;height=550'
                    );

                    $action_links[] = sprintf(
                        '<a href="%s" class="thickbox open-plugin-details-modal" aria-label="%s" data-title="%s">%s</a>',
                        esc_url($details_link),
                        /* translators: %s: plugin name and version */
                        esc_attr(sprintf(__('More information about %s', 'opalestate-pro'), $name)),
                        esc_attr($name),
                        __('More Details', 'opalestate-pro')
                    );

                    if (!empty($plugin['icons']['svg'])) {
                        $plugin_icon_url = $plugin['icons']['svg'];
                    } elseif (!empty($plugin['icons']['2x'])) {
                        $plugin_icon_url = $plugin['icons']['2x'];
                    } elseif (!empty($plugin['icons']['1x'])) {
                        $plugin_icon_url = $plugin['icons']['1x'];
                    } else {
                        $plugin_icon_url = $plugin['icons']['default'];
                    }

                    /**
                     * Filters the install action links for a plugin.
                     *
                     * @param string[] $action_links An array of plugin action links. Defaults are links to Details and Install Now.
                     * @param array $plugin The plugin currently being listed.
                     * @since 2.7.0
                     *
                     */
                    $action_links = apply_filters('plugin_install_action_links', $action_links, $plugin);

                    $last_updated_timestamp = strtotime($plugin['last_updated']);
                    ?>
                    <div class="plugin-card plugin-card-<?php echo sanitize_html_class($plugin['slug']); ?>">
                        <?php
                        if (!$compatible_php || !$compatible_wp) {
                            echo '<div class="notice inline notice-error notice-alt"><p>';
                            if (!$compatible_php && !$compatible_wp) {
                                _e('This plugin doesn&#8217;t work with your versions of WordPress and PHP.', 'opalestate-pro');
                                if (current_user_can('update_core') && current_user_can('update_php')) {
                                    printf(
                                    /* translators: 1: "Update WordPress" screen URL, 2: "Update PHP" page URL */
                                        ' ' . __('<a href="%1$s">Please update WordPress</a>, and then <a href="%2$s">learn more about updating PHP</a>.', 'opalestate-pro'),
                                        self_admin_url('update-core.php'),
                                        esc_url(wp_get_update_php_url())
                                    );
                                    wp_update_php_annotation('</p><p><em>', '</em>');
                                } elseif (current_user_can('update_core')) {
                                    printf(
                                    /* translators: %s: "Update WordPress" screen URL */
                                        ' ' . __('<a href="%s">Please update WordPress</a>.', 'opalestate-pro'),
                                        self_admin_url('update-core.php')
                                    );
                                } elseif (current_user_can('update_php')) {
                                    printf(
                                    /* translators: %s: "Update PHP" page URL */
                                        ' ' . __('<a href="%s">Learn more about updating PHP</a>.', 'opalestate-pro'),
                                        esc_url(wp_get_update_php_url())
                                    );
                                    wp_update_php_annotation('</p><p><em>', '</em>');
                                }
                            } elseif (!$compatible_wp) {
                                _e('This plugin doesn&#8217;t work with your version of WordPress.', 'opalestate-pro');
                                if (current_user_can('update_core')) {
                                    printf(
                                    /* translators: %s: "Update WordPress" screen URL */
                                        ' ' . __('<a href="%s">Please update WordPress</a>.', 'opalestate-pro'),
                                        self_admin_url('update-core.php')
                                    );
                                }
                            } elseif (!$compatible_php) {
                                _e('This plugin doesn&#8217;t work with your version of PHP.', 'opalestate-pro');
                                if (current_user_can('update_php')) {
                                    printf(
                                    /* translators: %s: "Update PHP" page URL */
                                        ' ' . __('<a href="%s">Learn more about updating PHP</a>.', 'opalestate-pro'),
                                        esc_url(wp_get_update_php_url())
                                    );
                                    wp_update_php_annotation('</p><p><em>', '</em>');
                                }
                            }
                            echo '</p></div>';
                        }
                        ?>
                        <div class="plugin-card-top">
                            <div class="name column-name">
                                <h3>
                                    <a href="<?php echo esc_url($details_link); ?>" class="thickbox open-plugin-details-modal">
                                        <?php echo $title; ?>
                                        <img src="<?php echo esc_attr($plugin_icon_url); ?>" class="plugin-icon" alt="">
                                    </a>
                                </h3>
                            </div>
                            <div class="action-links">
                                <?php
                                if ($action_links) {
                                    echo '<ul class="plugin-action-buttons"><li>' . implode('</li><li>', $action_links) . '</li></ul>';
                                }
                                ?>
                            </div>
                            <div class="desc column-description">
                                <p><?php echo $description; ?></p>
                                <p class="authors"><?php echo $author; ?></p>
                            </div>
                        </div>
                        <div class="plugin-card-bottom">
                            <div class="vers column-rating">
                                <?php
                                wp_star_rating(
                                    [
                                        'rating' => $plugin['rating'],
                                        'type'   => 'percent',
                                        'number' => $plugin['num_ratings'],
                                    ]
                                );
                                ?>
                                <span class="num-ratings" aria-hidden="true">(<?php echo number_format_i18n($plugin['num_ratings']); ?>)</span>
                            </div>
                            <div class="column-updated">
                                <strong><?php _e('Last Updated:', 'opalestate-pro'); ?></strong> <?php printf(__('%s ago', 'opalestate-pro'), human_time_diff($last_updated_timestamp)); ?>
                            </div>
                            <div class="column-downloaded">
                                <?php
                                if ($plugin['active_installs'] >= 1000000) {
                                    $active_installs_millions = floor($plugin['active_installs'] / 1000000);
                                    $active_installs_text     = sprintf(
                                        _nx('%s+ Million', '%s+ Million', $active_installs_millions, 'Active plugin installations', 'opalestate-pro'),
                                        number_format_i18n($active_installs_millions)
                                    );
                                } elseif (0 == $plugin['active_installs']) {
                                    $active_installs_text = _x('Less Than 10', 'Active plugin installations', 'opalestate-pro');
                                } else {
                                    $active_installs_text = number_format_i18n($plugin['active_installs']) . '+';
                                }
                                printf(__('%s Active Installations', 'opalestate-pro'), $active_installs_text);
                                ?>
                            </div>
                            <div class="column-compatibility">
                                <?php
                                if (!$tested_wp) {
                                    echo '<span class="compatibility-untested">' . __('Untested with your version of WordPress', 'opalestate-pro') . '</span>';
                                } elseif (!$compatible_wp) {
                                    echo '<span class="compatibility-incompatible">' . __('<strong>Incompatible</strong> with your version of WordPress', 'opalestate-pro') . '</span>';
                                } else {
                                    echo '<span class="compatibility-compatible">' . __('<strong>Compatible</strong> with your version of WordPress', 'opalestate-pro') . '</span>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php
    } else {
        echo '<div class="error"><p>' . esc_html__('There was an error retrieving the Opalestate Add-ons list from the server. Please try again later.', 'opalestate-pro') . '</div>';
    }
    ?>
</div>
