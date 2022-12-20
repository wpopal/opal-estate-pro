<?php

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Elementor icon box widget.
 *
 * Elementor widget that displays an icon, a headline and a text.
 *
 */
class Opalestate_Account_Button_Elementor_Widget extends Opalestate_Elementor_Widget_Base {

    /**
     * Get widget name.
     *
     * Retrieve icon box widget name.
     *
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'opalestate-account-button';
    }

    /**
     * Get widget title.
     *
     * Retrieve icon box widget title.
     *
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__('Block: Account Button', 'opalestate-pro');
    }

    /**
     * Get widget icon.
     *
     * Retrieve icon box widget icon.
     *
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return apply_filters('opalestate_' . $this->get_name(), 'eicon-lock-user');
    }

    /**
     * Get widget keywords.
     *
     * Retrieve the list of keywords the widget belongs to.
     *
     * @access public
     *
     * @return array Widget keywords.
     */
    public function get_keywords() {
        return ['opalestate-pro', 'account', 'button'];
    }

    private function get_available_menus() {
        $menus = wp_get_nav_menus();

        $options = [];

        foreach ($menus as $menu) {
            $options[$menu->slug] = $menu->name;
        }

        return $options;
    }

    /**
     * Register icon box widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @access protected
     */
    protected function register_controls() {
        $this->start_controls_section(
            'account_content',
            [
                'label' => esc_html__('Not logged in', 'opalestate-pro'),
            ]
        );

        $this->add_control(
            'icon',
            [
                'label'   => esc_html__('Choose Icon', 'opalestate-pro'),
                'type'    => Controls_Manager::ICON,
                'default' => 'fa fa-user',
            ]
        );

        $this->add_control(
            'enable_label',
            [
                'label' => esc_html__('Enable Label', 'opalestate-pro'),
                'type'  => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_control(
            'label_text',
            [
                'label'     => esc_html__('Label Text', 'opalestate-pro'),
                'type'      => Controls_Manager::TEXT,
                'default'   => esc_html__('Account', 'opalestate-pro'),
                'condition' => ['enable_label' => 'yes'],
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'account_logged_in',
            [
                'label' => esc_html__('Logged in', 'opalestate-pro'),
            ]
        );

        $this->add_control(
            'logged_in_enable_avatar',
            [
                'label'   => esc_html__('Enable Avatar', 'opalestate-pro'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'on',
            ]
        );

        $this->add_control(
            'logged_in_enable_notification',
            [
                'label' => esc_html__('Enable Notification', 'opalestate-pro'),
                'type'  => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_control(
            'logged_in_icon',
            [
                'label'   => esc_html__('Choose Icon', 'opalestate-pro'),
                'type'    => Controls_Manager::ICON,
                'default' => 'fa fa-user',
            ]
        );

        $this->add_control(
            'logged_in_enable_label',
            [
                'label' => esc_html__('Enable Label', 'opalestate-pro'),
                'type'  => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_control(
            'logged_in_label_text',
            [
                'label'     => esc_html__('Label Text', 'opalestate-pro'),
                'type'      => Controls_Manager::TEXT,
                'default'   => esc_html__('Account', 'opalestate-pro'),
                'condition' => ['logged_in_enable_label' => 'yes'],
            ]
        );

        $menus = $this->get_available_menus();

        if (!empty($menus)) {
            $this->add_control(
                'enable_custom_menu',
                [
                    'label' => esc_html__('Use Custom Dashboard Menu', 'opalestate-pro'),
                    'type'  => Controls_Manager::SWITCHER,
                ]
            );

            $this->add_control(
                'menu',
                [
                    'label'        => esc_html__('Menu', 'opalestate-pro'),
                    'type'         => Controls_Manager::SELECT,
                    'options'      => $menus,
                    'default'      => 'my-account',
                    'save_default' => true,
                    'separator'    => 'after',
                    'description'  => sprintf(esc_html__('Go to the <a href="%s" target="_blank">Menus screen</a> to manage your menus.', 'opalestate-pro'), admin_url('nav-menus.php')),
                    'condition'    => ['enable_custom_menu' => 'yes'],
                ]
            );
        }

        $this->end_controls_section();

        $this->start_controls_section(
            'section_general_style_content',
            [
                'label' => esc_html__('General', 'opalestate-pro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'toggle_align',
            [
                'label'     => esc_html__('Alignment', 'opalestate-pro'),
                'type'      => Controls_Manager::CHOOSE,
                'options'   => [
                    'left'   => [
                        'title' => esc_html__('Left', 'opalestate-pro'),
                        'icon'  => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'opalestate-pro'),
                        'icon'  => 'eicon-h-align-center',
                    ],
                    'right'  => [
                        'title' => esc_html__('Right', 'opalestate-pro'),
                        'icon'  => 'eicon-h-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-widget-container' => 'text-align: {{VALUE}}',
                ],

            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'section_label_style_content',
            [
                'label' => esc_html__('Label', 'opalestate-pro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label'     => esc_html__('Color', 'opalestate-pro'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .site-header-account .account-label' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'title_typography',
                'selector' => '{{WRAPPER}} .site-header-account .account-label',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_icon_style_content',
            [
                'label' => esc_html__('Icon', 'opalestate-pro'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->start_controls_tabs('tabs_icon_style');

        $this->start_controls_tab(
            'tab_icon_normal',
            [
                'label' => esc_html__('Normal', 'opalestate-pro'),
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label'     => esc_html__('Icon Color', 'opalestate-pro'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .site-header-account i' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'background_color',
            [
                'label'     => esc_html__('Background Color', 'opalestate-pro'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .site-header-account i' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_fontsize',
            [
                'label'     => esc_html__('Icon Font Size', 'opalestate-pro'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .site-header-account i' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'icon_border',
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .site-header-account i',
                'separator'   => 'before',

            ]
        );

        $this->add_control(
            'icon_border_radius',
            [
                'label'      => esc_html__('Border Radius', 'opalestate-pro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .site-header-account i' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'icon_padding',
            [
                'label'      => esc_html__('Padding', 'opalestate-pro'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .site-header-account i' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_icon_hover',
            [
                'label' => esc_html__('Hover', 'opalestate-pro'),
            ]
        );

        $this->add_control(
            'icon_color_hover',
            [
                'label'     => esc_html__('Icon Color', 'opalestate-pro'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .site-header-account i:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'background_color_hover',
            [
                'label'     => esc_html__('Background Color', 'opalestate-pro'),
                'type'      => Controls_Manager::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .site-header-account i:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'icon_border_hover',
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .site-header-account i:hover',
                'separator'   => 'before',

            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings();

        $this->show_poup_hover_form($settings);
    }

    protected function show_poup_hover_form($settings) {
        $settings = wp_parse_args($settings, [
            'enable_label' => false,
            'icon'         => '',
            'style'        => '',
        ]);

        $account_link = opalestate_my_account_page();

        $id = rand(2, 9) . rand(0, 9);

        ?>
        <?php if (!is_user_logged_in()) : ?>
            <div class="elementor-dropdown site-header-account">
                <div class="elementor-dropdown-header">
                    <?php
                    echo '<a href="' . esc_url($account_link) . '">
                    <i class="' . esc_attr($settings['icon']) . '"></i>
                    ' . ($settings['enable_label'] && $settings['label_text'] ? '<span class="account-label">' . esc_html($settings['label_text']) . '</span>' : '') . '
                  </a>';
                    ?>
                </div>
                <div class="elementor-dropdown-menu" id="elementor-account-<?php echo esc_attr($id); ?>">
                    <div class="account-wrap">
                        <div class="account-inner dashboard">
                            <?php $this->render_form_login(); ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php else : ?>
            <div class="elementor-dropdown site-header-account">
                <div class="elementor-dropdown-header">
                    <div class="opalestate-user-greeting opalestate-popup hover-align-right">
                        <div class="popup-head">
                            <?php if ($settings['logged_in_enable_avatar']) : ?>
                                <a href="#">
                                    <?php $user_id = get_current_user_id(); ?>
                                    <div class="opalestate-user-image">
                                        <img src="<?php echo OpalEstate_User::get_author_picture($user_id); ?>" alt="<?php esc_attr_e('Avatar image', 'opalestate-pro'); ?>"/>
                                    </div>
                                    <span class="notify active"></span>
                                </a>
                            <?php else : ?>
                                <?php
                                echo '<a href="#">
                            	<i class="' . esc_attr($settings['logged_in_icon']) . '"></i>
                            ' . ($settings['logged_in_enable_label'] && $settings['logged_in_label_text'] ? '<span class="account-label">' . esc_html($settings['logged_in_label_text']) . '</span>' : '') . '
                         	    </a>';
                                ?>
                            <?php endif; ?>
                        </div>
                        <div class="popup-body">
                            <div class="account-dashboard-content">
                                <?php $this->render_dashboard($settings); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif;
    }

    protected function render_form_login() {
        ?>

        <div class="login-form-head">
            <span class="login-form-title"><?php esc_attr_e('Sign in', 'opalestate-pro') ?></span>
            <span class="pull-right">
                <a class="register-link" href="<?php echo esc_url(opalestate_my_account_page()); ?>"
                   title="<?php esc_attr_e('Register', 'opalestate-pro'); ?>"><?php esc_attr_e('Create an Account', 'opalestate-pro'); ?></a>
            </span>
        </div>

        <form class="opalestate-login-form opalestate-member-form" action="<?php echo esc_url(wp_login_url()); ?>" method="POST">
            <?php do_action('opalestate_member_before_login_form'); ?>
            <p>
                <label><?php esc_attr_e('Username or email', 'opalestate-pro'); ?>
                    <span class="required">*</span></label>
                <input name="username" type="text" required placeholder="<?php esc_attr_e('Username', 'opalestate-pro'); ?>"
                       value="<?php echo isset($_POST['username']) ? esc_attr($_POST['username']) : ''; ?>">
            </p>
            <p>
                <label><?php esc_attr_e('Password', 'opalestate-pro'); ?> <span class="required">*</span></label>
                <input name="password" type="password" required placeholder="<?php esc_attr_e('Password', 'opalestate-pro'); ?>">
            </p>

            <?php do_action('opalestate_member_login_form'); ?>

            <p>
                <input class="opalestate-input checkbox" name="rememberme" type="checkbox" value="forever"/> <?php esc_html_e('Remember me', 'opalestate-pro'); ?>
            </p>

            <?php wp_nonce_field('opalestate-login', 'opalestate-login-popup-nonce'); ?>

            <?php if (isset($redirect) && $redirect) : ?>
                <input type="hidden" name="redirect" value="<?php echo esc_url($redirect); ?>">
            <?php endif; ?>
            <button type="submit" name="login" data-button-action class="btn btn-primary btn-block w-100 mt-1" value="<?php esc_html_e('Login', 'opalestate-pro'); ?>">
                <?php esc_html_e('Login', 'opalestate-pro'); ?>
            </button>

            <?php do_action('login_form'); ?>

            <?php do_action('opalestate_member_after_login_form'); ?>
        </form>

        <div class="login-form-bottom">
            <a href="<?php echo wp_lostpassword_url(get_permalink()); ?>" class="lostpass-link"
               title="<?php esc_attr_e('Lost your password?', 'opalestate-pro'); ?>"><?php esc_attr_e('Lost your password?', 'opalestate-pro'); ?></a>
        </div>
        <?php
    }

    protected function render_dashboard($settings) { ?>
        <?php if ($settings['enable_custom_menu'] == 'yes') : ?>
            <nav class="social-navigation" role="navigation" aria-label="<?php esc_attr_e('Dashboard', 'opalestate-pro'); ?>">
                <?php
                wp_nav_menu([
                    'theme_location' => $settings['menu'],
                    'menu_class'     => 'account-links-menu',
                    'depth'          => 1,
                ]);
                ?>
            </nav><!-- .social-navigation -->
        <?php else: ?>
            <div class="account-dashboard">
                <?php
                if (function_exists('opalestate_management_user_menu_tabs')) {
                    opalestate_management_user_menu_tabs();
                }
                ?>
            </div>
        <?php endif;
    }
}
