<?php

namespace Woo_social_login\Bootstrap;

use Woo_social_login\Admin\Admin;
use Woo_social_login\Modules\ActionHandler;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class App
{

    /**
     * Plugin version.
     *
     * @var string
     */
    const VERSION = '1.0';

    /**
     * Instance of this class.
     *
     * @var object
     */
    protected static $instance = null;

    private ActionHandler $action_handler;

    public function __construct()
    {

        add_action('plugins_loaded', array($this, 'init'));
    }

    public function init()
    {

        // Load plugin text domain.
        add_action('init', array($this, 'load_translations'));

        if (class_exists('WooCommerce')) {

            add_action('wp_head', [$this, 'register_styles']);
            add_action('wp_enqueue_scripts', [$this, 'register_scripts']);

            new Admin();
            new ActionHandler();
        } else {
            add_action('admin_notices', array($this, 'woocommerce_fallback_notice'));
        }
    }
    /**
     * Return an instance of this class.
     *
     * @return object A single instance of this class.
     */
    public static function get_instance()
    {

        // If the single instance hasn't been set, set it now.
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function register_styles()
    {

        $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '.min' : '';

        wp_register_style('woo-social-login-style', self::get_assets_url() . '/build/css/style' . $suffix . '.css');
        wp_enqueue_style('woo-social-login-style');
    }

    public function register_scripts()
    {

        $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '.min' : '';

        wp_register_script('jquery-mask', self::get_assets_url() . '/build/js/jquery.mask/jquery.mask' . $suffix . '.js', array('jquery'), '1.14.10', true);
        wp_enqueue_script('woo-social-login-js', self::get_assets_url() . '/build/js/script' . $suffix . '.js', array('jquery', 'jquery-mask'), '1.0', true);

        $messages = array(
            __("At least 8 characters long.", "woo-social-login"),
            __("At least 1 uppercase letter.", "woo-social-login"),
            __("At least 1 lowercase letter.", "woo-social-login"),
            __("At least 1 number.", "woo-social-login"),
            __("Passwords do not match.", "woo-social-login"),
            __("A password is required.", "woo-social-login"),
        );

        wp_localize_script('woo-social-login-js', "wsl_localize_data", array(
            'adminurl' => admin_url() . 'admin-ajax.php',
            'nonce' => wp_create_nonce('wsl_secure_nonce'),
            'messages' => $messages,
        ));
    }

    /**
     * Get assets url.
     *
     * @return string
     */
    public static function get_assets_url()
    {
        return WOO_SOCIAL_LOGIN_URL . '/assets';
    }

    /**
     * Load the plugin text domain for translation.
     */
    public function load_translations()
    {
        load_plugin_textdomain('woo-social-login', false, dirname(WOO_SOCIAL_LOGIN_PLUGIN_BASENAME) . '/languages');
    }

    /**
     * WooCommerce fallback notice.
     */
    public function woocommerce_fallback_notice()
    {
        echo '<div class="error"><p>' . wp_kses(
            sprintf(
                /* translators: %s: woocommerce link */
                __('Woo Member Flow depends on %s to work!', 'woo-social-login'),
                '<a href="http://wordpress.org/plugins/woocommerce/">' . __('WooCommerce', 'woo-social-login') . '</a>'
            ),
            array(
                'a' => array(
                    'href' => array(),
                ),
            )
        ) . '</p></div>';
    }
}