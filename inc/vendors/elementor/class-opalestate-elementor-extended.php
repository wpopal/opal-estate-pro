<?php
/**
 * Main Opalestate_Elementor_Extended Class
 *
 * The main class that initiates and runs the plugin.
 */

final class Opalestate_Elementor_Extended {
    /**
     * Instance
     *
     * @access private
     * @static
     *
     * @var Opalestate_Elementor_Extended The single instance of the class.
     */
    private static $_instance = null;

    /**
     * Instance
     *
     * Ensures only one instance of the class is loaded or can be loaded.
     *
     * @access public
     * @static
     *
     * @return Opalestate_Elementor_Extended An instance of the class.
     */
    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Constructor
     *
     * @access public
     */
    public function __construct() {
        add_action('plugins_loaded', [$this, 'init']);
    }

    /**
     * Initialize the plugin
     *
     * Load the plugin only after Elementor (and other plugins) are loaded.
     * Checks for basic plugin requirements, if one check fail don't continue,
     * if all check have passed load the files required to run the plugin.
     *
     * Fired by `plugins_loaded` action hook.
     *
     * @access public
     */
    public function init() {
        // Check if Elementor installed and activated.
        if (!did_action('elementor/loaded')) {
            return;
        }

        add_action('elementor/elements/categories_registered', [$this, 'add_widget_categories']);

        // Add Plugin actions.
        add_action('elementor/widgets/register', [$this, 'register_widgets']);
    }

    /**
     * Add widget categories.
     *
     * @param object $elements_manager elements manager.
     */
    public function add_widget_categories($elements_manager) {
        $elements_manager->add_category(
            'opalestate-pro',
            [
                'title' => esc_html__('Opal Estate', 'opalestate-pro'),
                'icon'  => 'fa fa-plug',
            ]
        );
    }

    /**
     * Includes
     */
    public function includes() {
        require OPALESTATE_PLUGIN_DIR . 'inc/vendors/elementor/class-opalestate-elementor-widget-base.php';
    }

    /**
     * Init Widgets
     *
     * Include widgets files and register them
     *
     * @access public
     */
    public function register_widgets() {
        $this->includes();

        $widget_paths = glob(OPALESTATE_PLUGIN_DIR . 'inc/vendors/elementor/widgets/*.php');

        if ($widget_paths) {
            foreach ($widget_paths as $path) {
                require_once $path;
                $class_name = ucfirst(str_replace("-", "_", basename($path, '.php'))) . '_Elementor_Widget';

                if (class_exists($class_name)) {
                    \Elementor\Plugin::instance()->widgets_manager->register(new $class_name());
                }
            }
        }
    }
}

Opalestate_Elementor_Extended::instance();
