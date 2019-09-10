<div class="opalestate-my-account-form tabl-simple-style"> 
    <div class="opalestate-tab hr-mode">
        <div class="nav opalestate-tab-head">
            <a href="#o-login-form" class="tab-item"><?php esc_html_e( 'Login', 'opalestate-pro' ); ?></a>
            <a href="#o-register-form" class="tab-item"><?php esc_html_e( 'Register', 'opalestate-pro' ); ?></a>
        </div>
        <div class="opalestate-tab-wrap">
            <div class="opalestate-tab-content" id="o-login-form">
                <?php
                    $atts = array(
                        'message'   => '',
                        'redirect'  => '',
                        'hide_title'    => false
                    );
                    echo opalestate_load_template_path( 'user/login-form', $atts );
                ?>
            </div>
            <div class="opalestate-tab-content" id="o-register-form">
                <?php
                    $atts = array(
                        'message'   => '',
                        'redirect'  => '',
                        'hide_title'    => false
                    );
                    echo opalestate_load_template_path( 'user/register-form', $atts );
                ?>
            </div>
        </div>
    </div>
</div>