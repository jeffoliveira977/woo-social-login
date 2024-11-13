<?php

namespace Woo_social_login\Utility;

use DateTime;
use IntlDateFormatter;
use WP_Error;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class DateParser
{

    /**
     * Cache for month names.
     */
    private static array $month_names_cache;

    private static string $old_locale;

    /**
     * Check if a date is valid.
     *
     * @param int $day The day of the month.
     * @param string $month The month (e.g., "February").
     * @param int $year The year.
     *
     * @return bool Returns true if the date is valid, false otherwise.
     */
    public static function is_valid_date(int $day, string $month, int $year, string $locale): bool
    {
        $date = new IntlDateFormatter(
            $locale,
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            'UTC',
            IntlDateFormatter::GREGORIAN,
            'd MMMM yyyy'
        );

        return $date->parse("$day $month $year");
    }

    /**
     * Returns the names of the months in a specific locale.
     *
     * @param string $locale The locale to use, as a BCP 47 language tag.
     * @return array|WP_Error An associative array where the keys are month numbers (1 for January, 2 for February, etc.)
     *                        and the values are the names of the months in the specified locale. If the provided locale is not valid,
     *                        a WP_Error object is returned.
     */
    public static function get_month_names_by_locale(string $locale = 'en_US'): array|WP_Error
    {

        // Check if the locale is valid
        if (!locale_accept_from_http($locale)) {
            return new WP_Error('invalid_locale', "Invalid locale: $locale");
        }

        if (empty(self::$month_names_cache) || self::$old_locale !== $locale) {

            // Create an array with the numbers of the months
            $months = range(1, 12);

            // Create an IntlDateFormatter object to format the date
            $date_formatter = new IntlDateFormatter(
                $locale,
                IntlDateFormatter::NONE,
                IntlDateFormatter::NONE,
                'UTC',
                IntlDateFormatter::GREGORIAN,
                'MMMM'
            );

            $date = new DateTime("January");

            foreach ($months as $month) {

                // Format the date to get the name of the month
                self::$month_names_cache[$month] = $date_formatter->format($date);

                // Advance to the next month
                $date->modify("+1 month");
            }

            self::$old_locale = $locale;
        }

        return self::$month_names_cache;
    }

    /**
     * Returns the number of a month given its name and a locale.
     *
     * @param string $name The name of the month.
     * @param string $locale The locale to use, as a BCP 47 language tag.
     * @return int|WP_Error The number of the month (1 for January, 2 for February, etc.), or a WP_Error object if the provide locale is invalid
     *                      or if the month name is not found.
     */
    public static function get_month_number_by_name(string $name, string $locale = 'en_US'): int|WP_Error
    {

        $month_names = self::get_month_names_by_locale($locale);

        if (is_wp_error($month_names)) {
            return $month_names;
        }

        $month_number = array_search($name, $month_names);

        if (!$month_number) {
            return new WP_Error('invalid_month_name', "Invalid month name: $name");
        }

        return $month_number;
    }

    /**
     * Returns the name of a month given its number and a locale.
     *
     * @param int $month_id The number of the month (1 for January, 2 for February, etc.).
     * @param string $locale The locale to use, as a BCP 47 language tag.
     * @return string|WP_Error The name of the month in the specified locale, or a WP_Error object if the provide locale is invalid
     *                          or if the month number is not found
     *
     */
    public static function get_month_name_by_number(int $month_id, string $locale = 'en_US'): string|WP_Error
    {

        $month_names = self::get_month_names_by_locale($locale);

        if (is_wp_error($month_names)) {
            return $month_names;
        }

        if (!isset($month_names[$month_id])) {
            return new WP_Error('invalid_month_id', "Invalid month id: $month_id");
        }

        return $month_names[$month_id];
    }
}