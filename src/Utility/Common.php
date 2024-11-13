<?php

namespace Woo_social_login\Utility;

use WP_Error;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class Common
{

    /**
     * Gets the email address of a user.
     *
     * This function retrieves the email address of a user given their user ID.
     *
     * @param int $user_id The ID of the user.
     * @return string The email address of the user.
     */
    public static function get_user_email(int $user_id): string
    {
        $user = get_userdata($user_id);
        return $user->user_email;
    }

    /**
     * This function retrieves a user by a specified meta key-value pair.
     *
     * @param string $key The meta key to search for.
     * @param mixed $value The meta value of the key to match.
     * @return int|bool Returns the first user object that matches the key-value pair if found, false otherwise.
     */
    public static function get_user_by_key(string $key, mixed $value): int|bool
    {

        $users = get_users(
            array(
                'meta_key' => $key,
                'meta_value' => $value,
            )
        );

        return !empty($users) ? $users[0] : false;
    }

    /**
     * Checks if a user is an administrator.
     *
     * This function takes a user ID as an argument and checks if the user with that ID is an administrator.
     * It returns true if the user is an administrator, and false otherwise.
     *
     * @param int $user_id The ID of the user to check.
     * @return bool True if the user is an administrator, false otherwise.
     */
    public static function is_admin_user(int $user_id): bool
    {

        // Get the user object by ID
        $user = get_user_by('id', $user_id);

        // Check if the user exists and has the 'administrator' role
        return $user && in_array('administrator', (array) $user->roles);
    }

    public static function get_template($template)
    {

        ob_start();

        $template_path = WOO_SOCIAL_LOGIN_PATH . $template . ".php";

        // Check if template file exists
        if (!file_exists($template_path)) {
            return new WP_Error('template_exist', __("Template '$template' not found in the templates folder.", 'woo-social-login'));
        }

        require $template_path;

        return ob_get_clean();
    }

    /**
     * This function removes certain special characters from a string.
     *
     * @param string $string The input string from which special characters will be removed.
     * @return string The modified string with all instances of '.', '-', and '/' removed.
     */
    public static function format_number(string $string): string
    {
        return preg_replace('/[.\-\/]/', '', $string);
    }

    /**
     * This function removes all content within brackets from a string and trims trailing white spaces.
     *
     * @param string $string The input string from which content within brackets and trailing white spaces will be removed.
     * @return string The resulting string after removal of content within brackets and trimming of trailing white spaces.
     */
    public static function remove_brackets_content($string)
    {
        $result = preg_replace('/\{.*?\}/', '', $string);
        return rtrim($result);
    }

    public static function create_section()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function print_message_popup(mixed $args, string $type): string
    {

        return wc_get_template_html(
            "templates/notification-$type.php",
            $args,
            WOO_SOCIAL_LOGIN_PATH,
            WOO_SOCIAL_LOGIN_PATH
        ); // Default path
    }

    /**
     * This function validates a Brazilian CPF number.
     *
     * @param string $cpf The CPF number to be validated.
     * @return bool Returns true if the CPF number is valid, false otherwise.
     */
    public static function validate_cpf(string $cpf): bool
    {

        // Remove any non-numeric characters
        $cpf = preg_replace('/\D/', '', $cpf);

        // Check if the length is correct (11 digits)
        if (strlen($cpf) != 11) {
            return false;
        }

        // Check if it's a sequence of the same number
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        // Calculate and validate check digits
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }

        return true;
    }
}