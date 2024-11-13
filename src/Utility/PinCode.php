<?php

namespace Woo_social_login\Utility;

use WP_Error;
use WC_Email;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class PinCode
{

    /**
     * Generates a random PIN code.
     *
     * This function generates a random PIN code with a specified number of digits.
     *
     * @param int $digits The number of digits in the PIN code (default is 6).
     * @return string The generated PIN code.
     */
    public static function generate_pin_code(int $digits = 6): string
    {

        // Generate random digits
        $pin = str_pad(mt_rand(0, pow(10, $digits) - 1), $digits, '0', STR_PAD_LEFT);
        return $pin;
    }

    /**
     * Gets the decoded PIN data for the user ID.
     *
     * This function gets the serialized PIN data for the user id from the user meta,
     * unserializes it, and checks if it is valid.
     *
     * @param int $user_id The user ID.
     * @return array|WP_Error The decoded PIN data for the user ID or a WP_Error object if an error occurred.
     */
    public static function get_decoded_pin_data(int $user_id): array|WP_Error
    {

        // Get serialized PIN data for the user id
        $serialized_data = get_user_meta($user_id, 'slx_pin_code', true);

        // Check if serialized data is valid
        if (!$serialized_data) {
            return new WP_Error('invalid_pin_data', __('Unable to find PIN data for this user.', 'secureloginx'));
        }

        // Unserialize the data
        $decoded_data = maybe_unserialize($serialized_data);

        // Check if decoded data is an array and contains the PIN
        if (!is_array($decoded_data) || !isset($decoded_data['pin'])) {
            return new WP_Error('invalid_pin_data', __('Invalid PIN data for this user.', 'secureloginx'));
        }

        return $decoded_data;
    }

    /**
     * Sends a PIN code to the user via email.
     *
     * This function generates a random PIN code and sends it to the user via email.
     * It also updates the user's meta data with the generated PIN code and its expiration time.
     *
     * @param int $user_id The ID of the user.
     * @return bool Whether the email was sent successfully.
     */
    public static function send_pin_email(int $user_id, int $expire_time = 600): bool
    {

        // Generate random PIN code
        $pin_code = self::generate_pin_code();

        // Create key data array
        $key_data = [
            'pin' => $pin_code,
            'expiration_start' => time(),
            'expiration_end' => time() + $expire_time,
        ];

        // Update user's meta data with key data
        update_user_meta($user_id, 'slx_pin_code', $key_data);

        // Get user's email address
        $user_email = Common::get_user_email($user_id);

        // Create email
        $mailer = WC()->mailer();
        $heading = 'Account Verification Code';
        $message = ("Here is your account verification code: $pin_code. Please enter this code on the website to complete the verification process.");
        $wrapped_message = $mailer->wrap_message($heading, $message);

        // Style email
        $wc_email = new WC_Email;
        $html_message = $wc_email->style_inline($wrapped_message);

        // Send email
        return $mailer->send($user_email, $heading, $html_message);
    }
}