<?php

namespace Woo_social_login\Modules;

use Woo_social_login\Utility\DateParser;
use Woo_social_login\Utility\Common;

session_start();

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * This class is responsible for processing different types of requests.
 * It includes methods to process actions, templates, and forms.
 * Each method handles a specific type of request and returns an appropriate response.
 */
class ActionHandler
{

    private UserDataController $user_controller;

    public function __construct()
    {
        $this->user_controller = new UserDataController();

        $this->hooks();
    }

    public function hooks()
    {
        add_action('wp_ajax_process_action', [$this, 'process_action']);
        add_action('wp_ajax_nopriv_process_action', [$this, 'process_action']);

        add_action('wp_ajax_facebook_callback', [$this, 'facebook_callback']);
        add_action('wp_ajax_nopriv_facebook_callback', [$this, 'facebook_callback']);

        add_action('wp_ajax_google_callback', [$this, 'google_callback']);
        add_action('wp_ajax_nopriv_google_callback', [$this, 'google_callback']);

        add_shortcode('register_form', [$this, 'ajax_register_shortcode']);
        add_filter('woocommerce_locate_template', [$this, 'override_myaccount_template'], 10, 3);
        add_filter('woocommerce_locate_template', [$this, 'override_password_template'], 10, 3);
    }

    public static function ajax_register_shortcode()
    {

        $locale = get_locale();

        $months = DateParser::get_month_names_by_locale($locale);

        $args = array(
            'months' => $months,
            'show_social_newtworks' => true,
            'show_birthdate' => true,
            'show_gender' => true,
            'show_cellphone' => true,
            'terms_conditions_url' => "https://site-test-01.local/politica-de-privacidade",
        );

        $template = wc_get_template_html(
            "templates/form-handler.php",
            $args,
            WOO_SOCIAL_LOGIN_PATH,
            WOO_SOCIAL_LOGIN_PATH
        ); // Default path


        return $template;
    }

    public function override_myaccount_template($template, $template_name, $template_path)
    {

        if ($template_name === 'myaccount/form-login.php') {

            $locale = get_locale();

            $months = DateParser::get_month_names_by_locale($locale);

            $args = array(
                'months' => $months,
                'show_social_newtworks' => true,
                'show_birthdate' => true,
                'show_gender' => true,
                'show_cellphone' => true,
                'terms_conditions_url' => "https://site-test-01.local/politica-de-privacidade",
            );

            update_option('wmf_values2', $args);
            $template = WOO_SOCIAL_LOGIN_PATH . 'templates/form-handler.php';

            return $template;
        }

        return $template;
    }

    public function override_password_template($template, $template_name, $template_path)
    {

        if ($template_name === 'myaccount/form-lost-password.php') {
            $template = WOO_SOCIAL_LOGIN_PATH . 'templates/lost-password-form.php';
        } else if ($template_name === 'myaccount/form-reset-password.php') {
            $template = WOO_SOCIAL_LOGIN_PATH . 'templates/reset-password-form.php';
        }

        return $template;
    }

    /**
     * Processes an AJAX action, handling template retrieval and form submission.
     *
     * This function processes an AJAX action, including nonce verification and parsing form data.
     * It determines whether to retrieve a template or process a form submission based on the provided data.
     *
     */
    public function process_action(): void
    {

        // Check the nonce
        if (!check_ajax_referer('wsl_secure_nonce', 'security', false)) {
            wp_send_json(
                array(
                    'success' => false,
                    'message' => __('Nonce verification failed.', 'woo-social-login'),
                )
            );
        }

        $form_type = sanitize_text_field($_POST['wsl_form']);

        if ($form_type !== 'google-login' && $form_type !== 'facebook-login') {
            $recaptcha = $_POST['g-recaptcha-response'];
            $secret_key = '6LdsE5spAAAAADeDj97u3zgsrNMfGUNO6Bsl9cS4';

            $url = 'https://www.google.com/recaptcha/api/siteverify?secret='
                . $secret_key . '&response=' . $recaptcha;

            $response = file_get_contents($url);

            $response = json_decode($response);

            if ($response->success === false || $response->score <= 0.5) {
                wp_send_json(array(
                    'success' => false,
                    'message' => __('Error in Google reCAPTCHA', 'woo-social-login'),
                ));
            }
        }

        /*// Parse form data
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        parse_str($_POST["form_data"], $_POST);
        } else {
        parse_str($_GET["form_data"], $_GET);
        }*/

        if (isset($_POST['form_action_id'])) {
            $message = self::process_template();
        } else if (isset($_POST['wsl_form'])) {
            $message = self::process_form();
        } else {
            $message = array(
                'success' => false,
                'message' => __('Form not defined.', 'woo-social-login'),
            );
        }

        // Send JSON response
        wp_send_json($message);
    }

    /**
     * Get a template for a form based on the form link ID.
     *
     * This function retrieves a template for a specific form based on the provided form link ID.
     *
     * @return array An associative array containing the success status and template or error message.
     */
    public function process_template()
    {

        $form_action_id = sanitize_text_field($_POST['form_action_id']);

        // Define form types
        $form_types = array(
            'slx-login' => 'login-form',
            'slx-register' => 'register-form',
            'slx-lost-password' => 'lost-password-form',
            'slx-back-to-login' => 'login-form',
        );

        // Check if form type is valid
        if (array_key_exists($form_action_id, $form_types)) {

            $locale = get_locale();

            $months = DateParser::get_month_names_by_locale($locale);

            $template = wc_get_template_html(
                "templates/$form_types[$form_action_id].php",
                //"templates/otp-verification-form.php",
                $months,
                WOO_SOCIAL_LOGIN_PATH,
                WOO_SOCIAL_LOGIN_PATH
            ); // Default path

            $message = array(
                'success' => true,
                'template' => $template,
            );
        } else {
            $message = array(
                'success' => false,
                'message' => __("Invalid selector id: $form_action_id", 'woo-social-login'),
            );
        }

        return $message;
    }

    public function facebook_callback()
    {

        $this->callback_handler(false);
    }

    public function google_callback()
    {

        $this->callback_handler();
    }

    public function callback_handler($is_google_callback = true)
    {
        Common::create_section();

        $data = $is_google_callback ?
            $this->user_controller->google_login() : $this->user_controller->login_facebook();

        $template_type = strpos($data["message"], "error") !== false ? 'error' : 'success';

        $template = '
            <div id="slx-popup-message" class="slx-popup-message ' . $template_type . '">
                <span>' . $data["message"] . '</span>
            </div>';

        // Send message from popup to main window after it has loaded.
        echo "
            <script>
                window.onload = function() {
                    if (window.opener) {
                        window.opener.postMessage('" . json_encode($template) . "', window.origin);
                    }
                };
            </script>";
    }


    /**
     * Processes a form submission.
     *
     * This function handles the submission of a form via AJAX by determining the form type and calling the
     * corresponding handler function for processing.
     *
     * @return array An associative array containing the success status and template or error message.
     */
    public function process_form()
    {

        // Sanitize form_type
        $form_type = sanitize_text_field($_POST['wsl_form']);

        $message = array();

        // Handle form types
        switch ($form_type) {
            case 'login':
                $message = $this->user_controller->login();
                break;
            case 'google-login':
                $message = $this->user_controller->google_login(true);
                break;
            case 'facebook-login':
                $message = $this->user_controller->login_facebook(true);
                break;
            case 'register':
                $message = $this->user_controller->register();
                break;
            case 'lost-password':
                $message = $this->user_controller->lost_password();
                break;
            case 'reset-password':
                $message = $this->user_controller->reset_password();
                break;
            case 'otp-verification':
                $message = $this->user_controller->otp_verification();
                break;
            case 'otp-resend':
                $message = $this->user_controller->otp_resend();
                break;
            default:
                // Invalid form type
                $message = array(
                    'success' => false,
                    'message' => __("Invalid form type: $form_type", 'woo-social-login'),
                );
        }

        return $message;
    }
}