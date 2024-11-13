<?php

namespace Woo_social_login\Admin;

if (!defined('ABSPATH')) {
    exit;
}

class Admin
{
    public $settings;
    public function __construct()
    {
        add_action('admin_menu', array($this, 'menu_pages'), 100);
        add_action('admin_init', array($this, 'menu_settings'));

        $this->settings = get_option('wsl-settings');
    }

    public function menu_pages()
    {

        add_submenu_page(
            'woocommerce',
            __('WooCommerce Desconto e Parcelas', 'woo-social-login'),
            __('Social Login', 'woo-social-login'),
            'manage_options',
            'wsl-page',
            array($this, 'render_page')
        );
    }
    public function render_page()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        include_once 'Tabs/html-panel.php';
    }

    public function menu_settings()
    {

        add_settings_field(
            'switch_login_facebook',
            'Enable/Disable',
            array($this, 'checkbox_field_callback'),
            'wsl-page',
            'section_general-switch_login_facebook',
            array(
                'id' => 'switch_login_facebook',
                'label_for' => 'switch_login_facebook',
                'desc' => __('Activate to log in with facebook.', 'woo-social-login')
            )
        );

        add_settings_field(
            'facebok_app_id',
            'App ID',
            array($this, 'input_field_callback'),
            'wsl-page',
            'section_general-facebook_app_id',
            array(
                'id' => 'facebook_app_id',
                'label_for' => 'facebook_app_id',
                'default' => 12,
                'placeholder' => '',
                'symbol' => '',
                'type' => 'text',
                'desc' => __('App ID', 'woo-social-login')
            )
        );

        add_settings_field(
            'facebok_app_secret',
            'App Secrect',
            array($this, 'input_field_callback'),
            'wsl-page',
            'section_general-facebok_app_secret',
            array(
                'id' => 'facebok_app_secret',
                'label_for' => 'facebok_app_secret',
                'default' => 12,
                'placeholder' => '',
                'symbol' => '',
                'type' => 'text',
                'desc' => __('App Secrect', 'woo-social-login')
            )
        );

        add_settings_field(
            'switch_login_google',
            'Enable/Disable',
            array($this, 'checkbox_field_callback'),
            'wsl-page',
            'section_general-switch_login_google',
            array(
                'id' => 'switch_login_google',
                'label_for' => 'switch_login_google',
                'desc' => __('Activate to log in with google.', 'woo-social-login')
            )
        );

        add_settings_field(
            'google_app_id',
            'App ID',
            array($this, 'input_field_callback'),
            'wsl-page',
            'section_general-google_app_id',
            array(
                'id' => 'google_app_id',
                'label_for' => 'google_app_id',
                'default' => 12,
                'placeholder' => '',
                'symbol' => '',
                'type' => 'text',
                'desc' => __('App ID', 'woo-social-login')
            )
        );

        add_settings_field(
            'google_app_secret',
            'App Secrect',
            array($this, 'input_field_callback'),
            'wsl-page',
            'section_general-google_app_secret',
            array(
                'id' => 'google_app_secret',
                'label_for' => 'google_app_secret',
                'default' => 12,
                'placeholder' => '',
                'symbol' => '',
                'type' => 'text',
                'desc' => __('App Secrect', 'woo-social-login')
            )
        );

        add_settings_field(
            'switch_login_recaptcha',
            'Enable/Disable',
            array($this, 'checkbox_field_callback'),
            'wsl-page',
            'section_general-switch_login_recaptcha',
            array(
                'id' => 'switch_login_recaptcha',
                'label_for' => 'switch_login_recaptcha',
                'desc' => __('Activate to log in with google.', 'woo-social-login')
            )
        );

        add_settings_field(
            'recaptcha_site_key',
            'App ID',
            array($this, 'input_field_callback'),
            'wsl-page',
            'section_general-recaptcha_site_key',
            array(
                'id' => 'recaptcha_site_key',
                'label_for' => 'recaptcha_site_key',
                'default' => 12,
                'placeholder' => '',
                'symbol' => '',
                'type' => 'text',
                'desc' => __('Site Key', 'woo-social-login')
            )
        );

        add_settings_field(
            'recaptcha_secret_key',
            'App Secrect',
            array($this, 'input_field_callback'),
            'wsl-page',
            'section_general-recaptcha_secret_key',
            array(
                'id' => 'recaptcha_secret_key',
                'label_for' => 'recaptcha_secret_key',
                'default' => 12,
                'placeholder' => '',
                'symbol' => '',
                'type' => 'text',
                'desc' => __('Secrect Key', 'woo-social-login')
            )
        );

        register_setting(
            "wsl-page",
            "wsl-settings",
            array($this, 'options_sanitize')
        );
    }

    public function checkbox_field_callback($args)
    {
        extract($args);

        $options = get_option('wcoommerce-discount-installments-settings');

        // Verifique se o campo está marcado
        $checked = isset($options['enable_icons']) === 'yes' ? 'checked' : '';

        echo '
		<div class="checkbox-group" style="display:flex; align-items:center; margin-bottom: 20px;">
			<div class="switch-toggle">
				<input type="checkbox" id="' . $id . '" class="switch-input"' . $checked . '>
				<label for="' . $label_for . '" class="switch-label"></label>
			</div>' .
            ($desc ? "<span style='margin-left: 10px;'>$desc</span>" : '') .
            '</div>';
    }

    public function input_field_callback($args)
    {
        extract($args);

        $value = isset($this->settings[$id]) ? $this->settings[$id] : $default;
        echo '
		<div class="checkbox-group" style="display:flex; align-items:center; margin-bottom: 20px;"> 
			<input class="input-group-text" type="' . $type . '" id="' . $id . '" name="wsl-settings[' . $id . ']" value="' . $value . '" />		
		</div>';
    }

    public function options_sanitize($input)
    {
        $newinput = array();
        foreach ($input as $key => $value) {
            $newinput[$key] = sanitize_text_field($value);
        }
        return $newinput;
    }
}