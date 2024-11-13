<?php

/**
 * Plugin Name: Woo Social Login
 * Description: This plugin allows users to login/signup using modern forms.
 * Version: 1.0
 * Author: Jeff Oliveira
 * Requires at least: 6.0
 * Tested up to: 6.1
 *
 * Text Domain: woo-social-login
 * Domain Path: /languages
 *
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

// Define plugin constants
define('WOO_SOCIAL_LOGIN_FILE', __FILE__);
define('WOO_SOCIAL_LOGIN_PATH', plugin_dir_path(__FILE__));
define('WOO_SOCIAL_LOGIN_PLUGIN_BASENAME', plugin_basename(__FILE__));
define("WOO_SOCIAL_LOGIN_URL", untrailingslashit(plugins_url('/', WOO_SOCIAL_LOGIN_FILE))); // plugin url

if (is_readable(__DIR__ . '/vendor/autoload.php')) {
	require_once __DIR__ . '/vendor/autoload.php';
}

function add_recaptcha_script()
{
	echo '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
	//echo '<script src="https://www.google.com/recaptcha/api.js?render=6LddVaQoAAAAAO4EC1zrvIPdfD5hYwqhrKgLCJtq"></script>';
}
add_action('wp_head', 'add_recaptcha_script');

Woo_social_login\Bootstrap\App::get_instance();