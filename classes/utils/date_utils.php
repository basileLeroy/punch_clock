<?php
/**
 * Date helper class.
 *
 * @package    mod_punchclock
 * @copyright  2025 onwards Basile Leroy {@link https://basileleroy.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_punchclock\utils;

defined('MOODLE_INTERNAL') || die();

use mod_punchclock\enums\filter_controls;


class date_utils {
    /**
     * Get date range for the selected view mode.
     *
     * @param int $view One of filter_controls::DAY/WEEK/MONTH/ALL.
     * @param string|null $date Reference date (Y-m-d). Default: today.
     * @return array ['start' => 'Y-m-d', 'end' => 'Y-m-d'].
     */
    public static function get_date_range(int $view, ?string $date = null): array {
        $timestamp = $date ? strtotime($date) : time();

        switch ($view) {
            case filter_controls::DAY:
                return [
                    'start' => date('Y-m-d', $timestamp),
                    'end' => date('Y-m-d', $timestamp),
                ];

            case filter_controls::WEEK:
                $start = date('Y-m-d', strtotime('monday this week', $timestamp));
                $end = date('Y-m-d', strtotime('sunday this week', $timestamp));
                return compact('start', 'end');

            case filter_controls::MONTH:
                $start = date('Y-m-01', $timestamp); // First day of month.
                $end = date('Y-m-t', $timestamp);   // Last day of month.
                return compact('start', 'end');

            case filter_controls::ALL:
            default:
                return [
                    'start' => '1970-01-01', // Arbitrary early date.
                    'end' => '2038-01-19',   // Arbitrary far future date.
                ];
        }
    }
}