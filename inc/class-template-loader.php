<?php
/**
 * Opalestate_Template_Loader
 *
 * @package    opalestate
 * @author     Opal  Team <info@wpopal.com >
 * @copyright  Copyright (C) 2019 wpopal.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @website  http://www.wpopal.com
 * @support  http://www.wpopal.com/support/forum.html
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Opalestate_Template_Loader {

    /**
     * Initialize template loader
     *
     * @access public
     * @return void
     */
    public static function init() {
        add_filter('template_include', [__CLASS__, 'templates']);
    }

    /**
     * Default templates
     *
     * @access public
     * @param $template
     * @return string
     * @throws Exception
     */
    public static function templates($template) {
        $post_type         = get_post_type();
        $custom_post_types = ['opalestate_property', 'opalestate_agent', 'opalestate_agency'];

        if (in_array($post_type, $custom_post_types)) {


            if (is_tax('opalestate_agency')) {
                return self::locate('single-opalestate_agency');
            }

            if (is_archive()) {
                return self::locate('archive-' . $post_type);
            }

            if (is_single()) {
                return self::locate('single-' . $post_type);
            }
        }

        if (is_post_type_archive('opalestate_agency')) {
            return self::locate('archive-opalestate_agency');
        }

        if (is_post_type_archive('opalestate_agent')) {
            return self::locate('archive-opalestate_agent');
        }

        return $template;
    }

    /**
     * Gets template path
     *
     * @access public
     * @param $name
     * @param $plugin_dir
     * @return string
     * @throws Exception
     */
    public static function locate($name, $plugin_dir = OPALESTATE_PLUGIN_DIR, $warning = true) {
        $template = '';

        // Current theme base dir
        if (!empty($name)) {
            $template = locate_template("{$name}.php");
        }

        // Child theme
        if (!$template && !empty($name) && file_exists(get_stylesheet_directory() . "/opalestate/{$name}.php")) {
            $template = get_stylesheet_directory() . "/opalestate/{$name}.php";
        }

        // Original theme
        if (!$template && !empty($name) && file_exists(get_template_directory() . "/opalestate/{$name}.php")) {
            $template = get_template_directory() . "/opalestate/{$name}.php";
        }

        // Plugin
        if (!$template && !empty($name) && file_exists($plugin_dir . "/templates/{$name}.php")) {
            $template = $plugin_dir . "/templates/{$name}.php";
        }

        // Nothing found
        if (empty($template) && $warning) {
            throw new Exception("Template /templates/{$name}.php in plugin dir {$plugin_dir} not found.");
        }

        return $template;
    }


    /**
     * Loads template content
     *
     * @param string $name
     * @param array $args
     * @param string $plugin_dir
     * @return string
     * @throws Exception
     */
    public static function get_template_part($name, $args = [], $slug = null) {
        if (is_array($args) && count($args) > 0) {
            extract($args, EXTR_SKIP);
        }

        if ($slug) {
            $path = self::locate($name . '-' . $slug, OPALESTATE_PLUGIN_DIR, false);
            if (empty($path)) {
                $path = self::locate($name, OPALESTATE_PLUGIN_DIR);
            }
        } else {
            $path = self::locate($name, OPALESTATE_PLUGIN_DIR);
        }

        ob_start();
        include $path;
        $result = ob_get_contents();
        ob_end_clean();

        return $result;
    }
}

Opalestate_Template_Loader::init();
