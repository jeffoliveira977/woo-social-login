<?php

namespace Woo_social_login\Modules;

use Exception;
use WC_Shortcode_My_Account;
use Woo_social_login\Modules\FacebookLogin;
use Woo_social_login\Modules\GoogleLogin;
use Woo_social_login\Utility\Common;
use Woo_social_login\Utility\DateParser;
use Woo_social_login\Utility\PinCode;
use WP_Error;

session_start();

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class UserDataController
 *
 * The responsibilities of this class include controlling user login, registration, password recovery, and email sending processes.
 */
class UserDataController
{

    public $settings;

    public function __construct()
    {
        $this->settings = get_option("wsl-settings");
    }

    /**
     * Logs in a user.
     *
     * This function handles the login process for a user. It sanitizes and validates the input data,
     * checks for errors, and logs in the user if everything is correct. If an error occurs,
     * it returns an error message.
     *
     * @return array An array containing the result of the login process.
     */
    public function login()
    {
        try {

            // Sanitize and assign login data
            $login = isset($_POST['wsl-log-user']) ? sanitize_user($_POST['wsl-log-user']) : '';
            $password = isset($_POST['wsl-log-password']) ? $_POST['wsl-log-password'] : '';

            $creds = array(
                'user_login' => trim(wp_unslash($login)),
                'user_password' => $password,
                'remember' => isset($_POST['wsl-rememberme']),
            );

            // Validate login data
            $validation_error = new WP_Error();
            $validation_error = apply_filters('woocommerce_login_errors', $validation_error, $creds['user_login'], $creds['user_password']);
            if ($validation_error->get_error_code()) {
                throw new Exception(__('Error: ', 'woo-social-login') . $validation_error->get_error_message());
            }

            // Check if username is set
            if (empty($creds['user_login'])) {
                throw new Exception(__('Error: ', 'woo-social-login') . __('Username is required. {.wsl-user input}', 'woo-social-login'));
            }

            // Check if password is set
            if (empty($creds['user_password'])) {
                throw new Exception(__('Error: ', 'woo-social-login') . __('A password is required. {.wsl-password input}', 'woo-social-login'));
            }

            // Add user to blog if necessary
            if (is_multisite()) {
                $user_data = get_user_by(is_email($creds['user_login']) ? 'email' : 'login', $creds['user_login']);
                if ($user_data && !is_user_member_of_blog($user_data->ID, get_current_blog_id())) {
                    add_user_to_blog(get_current_blog_id(), $user_data->ID, 'customer');
                }
            }

            // Sign on user
            $user = wp_signon(apply_filters('woocommerce_login_credentials', $creds), is_ssl());
            if (is_wp_error($user)) {
                $message_code = $user->get_error_code();

                if (in_array($message_code, array('invalid_username', 'invalid_email'))) {
                    throw new Exception(__('Incorrect Email or Username. {.wsl-user input}', 'woo-social-login'));
                }

                if (in_array($message_code, array('incorrect_password'))) {
                    throw new Exception(__('Incorrect Password. {.wsl-password input}', 'woo-social-login'));
                }

                throw new Exception($user->get_error_message());
            }

            // Check if user is admin
            if (!Common::is_admin_user($user->ID)) {

                // Check email verification
                $email_verification = get_user_meta($user->ID, 'wsl_email_verification_pin', true);
                if (!$email_verification) {

                    session_start();
                    $_SESSION['wsl_user_id'] = $user->ID;

                    wp_logout();

                    $template = Common::get_template("templates/otp-verification-form");

                    if (is_wp_error($template)) {
                        throw new Exception($template->get_error_message());
                    }

                    return array(
                        'success' => true,
                        'template' => $template,
                        'message' => __("<span>It appears that you have not yet verified your email address at <b>$user->user_email</b>. Please check your inbox for the verification email and follow the instructions to complete the verification process.</span>", 'woo-social-login'),
                    );
                }
            }

            // Set redirect URL
            if (!empty($_POST['redirect'])) {
                $redirect = wp_unslash($_POST['redirect']);
            } elseif (wc_get_raw_referer()) {
                $redirect = wc_get_raw_referer();
            } else {
                $redirect = wc_get_page_permalink('myaccount');
            }

            $redirect = apply_filters('woocommerce_login_redirect', wp_validate_redirect($redirect), $user);
            return array(
                'success' => true,
                'redirect' => $redirect,
                'message' => __('You have successfully logged in. Welcome back!', 'woo-social-login'),
            );

        } catch (Exception $e) {
            do_action('woocommerce_login_failed');

            return array(
                'success' => false,
                'message' => $e->getMessage(),
            );
        }
    }

    public function get_facebook_login_auth_url()
    {
        if (!session_id()) {
            session_start();
        }

        $fbAppId = $this->settings["facebook_app_id"];
        $fbAppSecret = $this->settings["facebook_app_secret"];
        $callback_url = 'https://site-test-01.local/wp-admin/admin-ajax.php?action=facebook_callback';

        // Create an instance of the FacebookAPI class
        $facebookAPI = new FacebookLogin($fbAppId, $fbAppSecret, $callback_url);

        // Redirect to the Facebook login
        $redirect = $facebookAPI->getLoginUrl([
            'email',
            'user_birthday',
            'user_gender',
        ]);

        return array('redirect' => $redirect);
    }

    public function login_facebook(bool $get_auth_url = false)
    {
        Common::create_section();

        $fbAppId = $this->settings["facebook_app_id"];
        $fbAppSecret = $this->settings["facebook_app_secret"];
        $callback_url = 'https://site-test-01.local/wp-admin/admin-ajax.php?action=facebook_callback';

        $facebookAPI = new FacebookLogin($fbAppId, $fbAppSecret, $callback_url);

        if ($get_auth_url) {

            // Redirect to the Facebook login
            $redirect = $facebookAPI->getLoginUrl([
                'email',
                'user_birthday',
                'user_gender',
            ]);

            return array('redirect' => $redirect);
        }

        try {

            $accessToken = $facebookAPI->getAccessToken();

            if (!$accessToken) {
                throw new Exception("Error: Unable to retrieve access token from Facebook.");
            }

            $userProfile = $facebookAPI->getUserProfile();
            if (!$userProfile) {
                throw new Exception("Error: Unable to retrieve user profile from Facebook.");
            }

            $email = $userProfile->getEmail();
            $first_name = $userProfile->getFirstName();
            $last_name = $userProfile->getLastName();

            $user_data = array(
                'first_name' => $first_name,
                'last_name' => $last_name,
                'billing_first_name' => $first_name,
                'billing_last_name' => $last_name,
                'billing_birthdate' => $userProfile->getBirthday(),
                'billing_sex' => $userProfile->getGender(),
                'facebook_user_id' => $userProfile->getId(),
                'wsl_email_verification_pin' => true
            );

            $user_id = email_exists($email);
            if (!$user_id /*|| $userProfile->getId() !== get_user_meta($user_id, 'facebook_user_id', true)*/) {
                $user_id = $this->create_new_user($email, '', true, $user_data);
            }

            wc_set_customer_auth_cookie($user_id);

            return array(
                'success' => true,
                'redirect' => wc_get_page_permalink('myaccount'),
                'message' => __('You have successfully logged in. Welcome back!', 'woo-social-login'),
            );
        } catch (Exception $e) {
            do_action('woocommerce_login_failed');

            return array(
                'success' => false,
                'redirect' => wc_get_page_permalink('myaccount'),
                'message' => Common::remove_brackets_content($e->getMessage()),
            );
        }
    }

    public function get_google_login_auth_url()
    {
        //$client_id = "595075201008-llsl941pnh8sokcnljfooqn7nupnj8gd.apps.googleusercontent.com";
        // $client_secret = "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";
        $redirect_url = "https://localhost/wp-admin/admin-ajax.php?action=google_callback";

        $client_id = $this->settings["google_app_id"];
        $client_secret = $this->settings["google_app_secret"];

        $googleAPI = new GoogleLogin($client_id, $client_secret, $redirect_url);

        $redirect = $googleAPI->get_auth_url();

        return array('redirect' => $redirect);
    }

    public function google_login(bool $get_auth_url = false)
    {
        //$client_id = "595075201008-llsl941pnh8sokcnljfooqn7nupnj8gd.apps.googleusercontent.com";
        //$client_secret = "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";
        $redirect_url = "https://localhost/wp-admin/admin-ajax.php?action=google_callback";

        $client_id = $this->settings["google_app_id"];
        $client_secret = $this->settings["google_app_secret"];

        $google_api = new GoogleLogin($client_id, $client_secret, $redirect_url);

        if ($get_auth_url) {
            $redirect = $google_api->get_auth_url();

            return array('redirect' => $redirect);
        }

        try {

            $google_api->authenticate();

            $user_info = $google_api->get_user_info();

            if (!$user_info) {
                throw new Exception("Error: Unable to retrieve user profile from Google.");
            }

            $email = trim($user_info->email);
            $user_id = email_exists($email);

            if (!$user_id /*|| $google_id !== get_user_meta($user_id, 'google_user_id', true)*/) {

                $google_id = trim($user_info->id);
                $first_name = trim($user_info->given_name);
                $last_name = trim($user_info->family_name);
                $gender = trim($user_info->gender);

                $user_data = array(
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'billing_first_name' => $first_name,
                    'billing_last_name' => $last_name,
                    'billing_sex' => $gender,
                    'google_user_id' => $google_id,
                    'wsl_email_verification_pin' => true
                );

                $user_id = $this->create_new_user($email, '', true, $user_data);
            }

            wc_set_customer_auth_cookie($user_id);

            return array(
                'success' => true,
                'redirect' => wc_get_page_permalink('myaccount'),
                'message' => __('You have successfully logged in. Welcome back!', 'woo-social-login'),
            );
        } catch (Exception $e) {
            do_action('woocommerce_login_failed');

            return array(
                'success' => false,
                'redirect' => wc_get_page_permalink('myaccount'),
                'message' => Common::remove_brackets_content($e->getMessage()),
            );
        }
    }

    /**
     * This function creates a new user in WooCommerce.
     *
     * @param string $email The email address of the new user.
     * @param string $password (optional) The password for the new user. If not provided, a random password will be generated.
     * @param array $args An associative array containing additional user data.
     * @return int|WP_Error Returns the new user's ID on success, or a WP_Error object if the user could not be created.
     * @throws Exception Throws an exception if the email is invalid, the password is too short, or required data is missing from $args.
     */
    private function create_new_user(string $email, string $password = '', bool $ignore_email_check, array $args)
    {

        // Validate first name
        if (!isset($args["first_name"]) || empty($args["first_name"])) {
            throw new Exception(__('First name is required. {#wsl-first-name}', 'woo-social-login'));
        }

        // Validate last name
        if (!isset($args["last_name"]) || empty($args["last_name"])) {
            throw new Exception(__('Last name is required. {#wsl-last-name}', 'woo-social-login'));
        }

        if (!$ignore_email_check) {
            // Check if the email is valid
            if (empty($email) || !is_email($email) || email_exists($email)) {
                if (email_exists($email)) {
                    throw new Exception(__('A user with this email is already registered. {.wsl-email input}', 'woo-social-login'));
                } else {
                    throw new Exception(__('Please enter a valid email. {.wsl-email input}', 'woo-social-login'));
                }
            }
        }

        $username = wc_create_new_customer_username($email, $args);

        // Create a new customer
        $user_id = wc_create_new_customer($email, wc_clean($username), empty($password) ? wp_generate_password() : $password);

        if (is_wp_error($user_id)) {
            throw new Exception($user_id->get_error_message());
        }

        foreach ($args as $key => $value) {
            update_user_meta($user_id, $key, $value);
        }

        return $user_id;
    }

    /**
     * Registers a new user.
     *
     * This function handles the registration of a new user. It sanitizes and validates the input data,
     * checks for errors, and creates a new user account if everything is correct. If an error occurs,
     * it returns an error message.
     *
     * @return array An array containing the result of the registration process.
     */
    public function register()
    {

        // Sanitize and assign input data
        $first_name = isset($_POST['wsl-first-name']) ? sanitize_user($_POST['wsl-first-name']) : '';
        $last_name = isset($_POST['wsl-last-name']) ? sanitize_user($_POST['wsl-last-name']) : '';
        $email = isset($_POST['wsl-reg-email']) ? sanitize_email($_POST['wsl-reg-email']) : '';
        $password = isset($_POST['wsl-reg-password']) ? $_POST['wsl-reg-password'] : '';
        $confirm_password = isset($_POST['wsl-reg-confirm-password']) ? $_POST['wsl-reg-confirm-password'] : '';
        $gender = isset($_POST['wsl-gender']) ? $_POST['wsl-gender'] : '';
        $cellphone = isset($_POST['wsl-cellphone']) ? Common::format_number($_POST['wsl-cellphone']) : '';

        try {
            // Validate first name
            if (empty($first_name)) {
                throw new Exception(__('First name is required. {#wsl-first-name}', 'woo-social-login'));
            }

            // Validate last name
            if (empty($last_name)) {
                throw new Exception(__('Last name is required. {#wsl-last-name}', 'woo-social-login'));
            }

            // Validate email
            if (empty($email) || !is_email($email) || email_exists($email)) {
                if (email_exists($email)) {
                    throw new Exception(__('A user with this email is already registered. {.wsl-email input}', 'woo-social-login'));
                } else {
                    throw new Exception(__('Please enter a valid email. {.wsl-email input}', 'woo-social-login'));
                }
            }

            // Validate password
            $password_error = $this->validate_password($password, $confirm_password, false);
            if (is_wp_error($password_error)) {
                $message_code = $password_error->get_error_code();
                $selector = in_array($message_code, array('password_mismatch')) ? '#wsl-reg-confirm-password' : '#wsl-reg-password';
                throw new Exception($password_error->get_error_message() . "{$selector}");
            }

            // Validate birthdate
            $birthdate = self::validate_birthdate($_POST['wsl-birthdate-day'], $_POST['wsl-birthdate-month'], $_POST['wsl-birthdate-year']);
            if (is_wp_error($birthdate)) {
                throw new Exception($birthdate->get_error_message() . '{.wsl-birthdate select}');
            }

            // Validate gender
            if ($gender === __('Select your gender', 'woo-social-login')) {
                throw new Exception(__('Gender is required. {#wsl-gender}', 'woo-social-login'));
            }

            // Validate cellphone
            if (strlen($cellphone) > 10 && strlen($cellphone) < 14) {
                if (Common::get_user_by_key('billing_cellphone', $cellphone)) {
                    throw new Exception(__('A user with this cellphone number is already registered. {#wsl-cellphone}', 'woo-social-login'));
                }
            } else {
                throw new Exception(__('Invalid cellphone number. {#wsl-cellphone}', 'woo-social-login'));
            }

            $username = wc_create_new_customer_username($email, array('first_name' => $first_name, 'last_name' => $last_name));

            // Create new customer
            $new_customer = wc_create_new_customer($email, wc_clean($username), $password);
            if (is_wp_error($new_customer)) {
                throw new Exception($new_customer->get_error_message());
            }

            // Set customer auth cookie
            wc_set_customer_auth_cookie($new_customer);

            // Start session and set user ID
            session_start();
            $_SESSION['wsl_user_id'] = $new_customer;

            update_user_meta($new_customer, 'first_name', $first_name);
            update_user_meta($new_customer, 'last_name', $last_name);
            update_user_meta($new_customer, 'billing_first_name', $first_name);
            update_user_meta($new_customer, 'billing_last_name', $last_name);
            update_user_meta($new_customer, 'billing_birthdate', $birthdate);
            update_user_meta($new_customer, 'billing_sex', $gender);
            update_user_meta($new_customer, 'billing_cellphone', $cellphone);
            update_user_meta($new_customer, 'wsl_email_verification_pin', false);

            // Send PIN email
            if (!PinCode::send_pin_email($new_customer)) {
                throw new Exception("An error occurred while sending the email. Please try again later or contact support for assistance.");
            }

            wp_logout();

            $template = Common::get_template("templates/otp-verification-form");

            if (is_wp_error($template)) {
                throw new Exception($template->get_error_message());
            }

            $message = "<span>A verification code has been sent to your email address at <b>$email</b>. Please check your inbox and follow the instructions to complete the verification process.</span>";
            return array(
                'success' => true,
                'message' => $message,
                'template' => $template,
            );
        } catch (Exception $e) {
            return array(
                'success' => false,
                'message' => $e->getMessage(),
            );
        }
    }

    /**
     * Handles lost password requests.
     *
     * This function handles lost password requests from the user.
     * It retrieves the user's password using the provided login (username or email)
     * and sends a password reset email to the user if the login is valid.
     *
     * @return array An array containing the success status and message.
     */
    public function lost_password()
    {
        try {
            // Sanitize login
            $login = isset($_POST['wsl-lp-user']) ? sanitize_user(wp_unslash($_POST['wsl-lp-user'])) : '';

            if (empty($login)) {
                throw new Exception(__('Enter a username or email address.', 'woo-social-login'));
            }

            // Retrieve user's password
            $errors = $this->retrieve_password($login);

            // Check for errors
            if (is_wp_error($errors)) {
                throw new Exception($errors->get_error_message());
            }

            // Get lost password confirmation message
            $template_message = wc_get_template_html('myaccount/lost-password-confirmation.php');

            return array(
                'success' => true,
                'message' => $template_message,
            );
        } catch (Exception $e) {
            return array(
                'success' => false,
                'message' => $e->getMessage(),
            );
        }
    }

    /**
     * Resets the user's password.
     *
     * This function resets the user's password using the provided reset key and login.
     * It checks if the reset key is valid and if the new password is valid.
     * If the reset key and new password are valid, it resets the user's password.
     *
     * @return array An array containing the success status and message.
     */
    public function reset_password(): array
    {
        wc_clear_notices();

        // Check if reset key is valid
        $user = check_password_reset_key($_POST['reset_key'], $_POST['reset_login']);

        try {
            if (is_wp_error($user)) {
                throw new Exception(__('This key is invalid or has already been used. Please reset your password again if needed.', 'woo-social-login'));
            }

            // Check if new password is provided
            if (empty($_POST['wsl-rpw-password'])) {
                throw new Exception(__('Please enter your password.', 'woo-social-login'));
            }

            // Check if new password and confirm password match
            if ($_POST['wsl-rpw-password'] !== $_POST['wsl-rpw-confirm-password']) {
                throw new Exception(__('Passwords do not match.', 'woo-social-login'));
            }

            $errors = new WP_Error();

            // Validate password reset
            do_action('validate_password_reset', $errors, $user);

            if (is_wp_error($errors)) {
                throw new Exception($errors->get_error_message());
            }

            // Reset user's password
            WC_Shortcode_My_Account::reset_password($user, $_POST['wsl-rpw-password']);

            // Trigger action after resetting user's password
            do_action('woocommerce_customer_reset_password', $user);

            // Set redirect URL
            if (!empty($_POST['redirect'])) {
                $redirect = wp_unslash($_POST['redirect']);
            } elseif (wc_get_raw_referer()) {
                $redirect = wc_get_raw_referer();
            } else {
                $redirect = wc_get_page_permalink('myaccount');
            }

            $redirect = apply_filters('woocommerce_login_redirect', wp_validate_redirect($redirect), $user);
            return array(
                'success' => true,
                'redirect' => $redirect,
                'message' => __('Your password has been reset successfully.', 'woo-social-login'),
            );
        } catch (Exception $e) {
            return array(
                'success' => false,
                'message' => $e->getMessage(),
            );
        }
    }

    /**
     * Verifies the OTP entered by the user.
     *
     * This function verifies the OTP entered by the user during email verification.
     * It checks if the entered OTP matches the saved OTP for the user and if it is within the expiration range.
     * If the OTP is valid, it updates the user meta to indicate successful email verification.
     *
     * @return array An array containing the success status and message.
     */
    public function otp_verification(): array
    {
        session_start();

        // Check if OTP code and user ID are set
        if (!isset($_POST['wsl-otp-code']) || !isset($_SESSION['wsl_user_id'])) {
            return array(
                'success' => false,
                'message' => __('Error in OTP verification. Please check whether the OTP value or user ID is set.', 'woo-social-login')
            );
        }

        try {

            $entered_pin = $_POST['wsl-otp-code'];
            $customer = $_SESSION['wsl_user_id'];

            if ($customer <= 0 || $customer >= 9999) {
                throw new Exception(__('Invalid user ID.', 'woo-social-login'));
            }

            $pin_data = PinCode::get_decoded_pin_data($customer);

            // Handle error
            if (is_wp_error($pin_data)) {
                throw new Exception($pin_data->get_error_message());
            }

            $saved_pin = $pin_data['pin'];

            // Check if entered PIN matches saved PIN
            if ($entered_pin !== $saved_pin) {
                throw new Exception(__('The entered PIN is incorrect. Please check and try again.', 'woo-social-login'));
            }

            $now = time();

            // Check if current time is within the expiration range of the PIN
            if ($now < $pin_data['expiration_start'] || $now > $pin_data['expiration_end']) {
                throw new Exception(__('The PIN has expired. Please generate a new PIN and try again.', 'woo-social-login'));
            }

            // Update user meta to indicate successful email verification
            update_user_meta($customer, 'wsl_email_verification_pin', true);

            // Set customer auth cookie
            wc_set_customer_auth_cookie($customer);

            return array(
                'success' => true,
                'message' => __('Your account has been successfully verified!', 'woo-social-login'),
            );
        } catch (Exception $e) {
            error_log($e->getMessage());

            return array(
                'success' => false,
                'message' => $e->getMessage(),
            );
        }
    }

    /**
     * Resends the OTP to the user.
     *
     * This function resends the OTP to the user if the previous OTP has expired.
     * It checks if the user is valid and if the previous OTP has expired.
     * If the OTP has expired, it sends a new OTP to the user via email.
     *
     * @return array An array containing the success status and message.
     */
    public function otp_resend(): array
    {
        session_start();

        // Check if user ID is set
        if (!isset($_SESSION['wsl_user_id'])) {
            return array(
                'success' => false,
                'message' => 'Invalid user',
            );
        }

        try {
            $customer = $_SESSION['wsl_user_id'];

            // Check if customer is valid
            if (!$customer) {
                throw new Exception(__('Invalid user id.', 'woo-social-login'));
            }

            $pin_data = PinCode::get_decoded_pin_data($customer);

            // Handle error
            if (is_wp_error($pin_data)) {
                throw new Exception($pin_data->get_error_message());
            }

            $now = time();

            // Check if current time is within the expiration range of the PIN
            if ($now >= $pin_data['expiration_start'] && $now <= $pin_data['expiration_end']) {
                throw new Exception('Please allow a 10-minute interval before initiating a new PIN generation request.');
            } else {
                // Send new OTP to customer via email
                if (!PinCode::send_pin_email($customer)) {
                    throw new Exception('An error occurred while sending the email. Please try again later or contact support for assistance.');
                }
            }

            return array(
                'success' => true,
                'message' => __('A new PIN has been generated and sent to your email. Please check your inbox and follow the instructions to complete the verification process.', 'woo-social-login'),
            );
        } catch (Exception $e) {
            return array(
                'success' => false,
                'message' => $e->getMessage(),
            );
        }
    }

    /**
     * Retrieves the user's password.
     *
     * This function retrieves the user's password using the provided login (username or email).
     * It checks if the login is valid and if the user is allowed to reset their password.
     * If the login is valid and the user is allowed to reset their password, it sends a password reset email to the user.
     *
     * @return WP_Error|void A WP_Error object if an error occurred, or void if the password reset email was sent successfully.
     */
    public static function retrieve_password(int $login)
    {

        $errors = new WP_Error();

        // Check if login is a username
        $user_data = get_user_by('login', $login);

        // If no user found, check if login is email
        if (!$user_data && is_email($login) && apply_filters('woocommerce_get_username_from_email', true)) {
            $user_data = get_user_by('email', $login);
        }

        // Trigger lostpassword_post action
        do_action('lostpassword_post', $errors, $user_data);

        // Check for errors
        if ($errors->get_error_code()) {
            return $errors;
        }

        // Check if user data is valid
        if (!$user_data) {
            return new WP_Error('invalid_login', __('Invalid username or email.', 'woo-social-login'));
        }

        // Check if user is a member of the current blog (for multisite)
        if (is_multisite() && !is_user_member_of_blog($user_data->ID, get_current_blog_id())) {
            return new WP_Error('invalid_login', __('Invalid username or email.', 'woo-social-login'));
        }

        $user_login = $user_data->user_login;

        // Trigger retrieve_password action
        do_action('retrieve_password', $user_login);

        // Check if password reset is allowed for this user
        $allow = apply_filters('allow_password_reset', true, $user_data->ID);

        if (!$allow) {
            return new WP_Error('password_reset_not_allowed', __('Password reset is not allowed for this user', 'woo-social-login'));
        } elseif (is_wp_error($allow)) {
            return $allow;
        }

        // Get password reset key
        $key = get_password_reset_key($user_data);

        // Send password reset email
        WC()->mailer(); // Load email classes.
        do_action('woocommerce_reset_password_notification', $user_login, $key);
    }

    /**
     * Processes the user's password.
     *
     * This function processes the user's password and checks if it is valid.
     * It checks if the password and confirm password are provided and if they match.
     * If the use_password_strength parameter is true, it also checks if the password meets the strength requirements.
     *
     * @param string $password The user's password.
     * @param string $confirm_password The user's confirm password.
     * @param bool $set_strength Whether to sets the password strength.
     * @return WP_Error|void A WP_Error object if an error occurred, or void if the password is valid.
     */
    public function validate_password(string $password, string $confirm_password, bool $set_strength = false, int $min_password_length = 8)
    {

        // Check if password and confirm password are provided
        if (empty($password) || empty($confirm_password)) {
            return new WP_Error('empty_password', __('A password is required.', 'woo-social-login'));
        }

        // Check if password and confirm password match
        if ($password !== $confirm_password) {
            return new WP_Error('password_mismatch', __('Passwords do not match.', 'woo-social-login'));
        }

        if ($set_strength) {
            // Check if password is at least min_password_length characters long
            if (strlen($password) < $min_password_length) {
                return new WP_Error('password_length', __('Password should be at least ' . $min_password_length . ' characters long.', 'woo-social-login'));
            }

            // Check if password contains at least one uppercase letter
            if (!preg_match('/[A-Z]/', $password)) {
                return new WP_Error('password_uppercase_letter', __('Password should include at least one uppercase letter.', 'woo-social-login'));
            }

            // Check if password contains at least one lowercase letter
            if (!preg_match('/[a-z]/', $password)) {
                return new WP_Error('password_lowercase_letter', __('Password should include at least one lowercase letter.', 'woo-social-login'));
            }

            // Check if password contains at least one number
            if (!preg_match('/[0-9]/', $password)) {
                return new WP_Error('password_number', __('Password should include at least one number.', 'woo-social-login'));
            }

            /*
        // Check if password contains at least one special character
        if (!preg_match('/[!@#$%^&*()\-_=+{};:,<.>]/', $password)) {
        return new WP_Error('password_special_character', __('Password should include at least one special character.', 'woo-social-login'));
        }
         */
        }
    }

    /**
     * Validates the user's birthdate.
     *
     * This function validates the user's birthdate by checking if the provided day, month, and year form a valid date.
     *
     * @param int $day The day of the birthdate.
     * @param string $month The month name of the birthdate.
     * @param int $year The year of the birthdate.
     * @return string|WP_Error The validated birthdate in 'dd/mm/yyyy' format, or a WP_Error object if the birthdate is invalid.
     */
    public static function validate_birthdate(int $day, string $month, int $year): string|WP_Error
    {

        // Get the site locale
        $locale = get_locale();

        // Check if provided day, month and year form a valid date
        if (!DateParser::is_valid_date($day, $month, $year, $locale)) {
            return new WP_Error('invalid_birthdate', __('Birthdate is required and must be a valid date.', 'woo-social-login'));
        }

        $month_number = DateParser::get_month_number_by_name($month, $locale);

        if (is_wp_error($month_number)) {
            return $month_number;
        }

        // Format birthdate as 'dd/mm/yyyy'
        $birthdate_parts = array($day, $month_number, $year);
        $birthdate = implode('/', $birthdate_parts);
        return $birthdate;
    }
}