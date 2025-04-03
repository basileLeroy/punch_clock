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
     * @return array ['start' => int, 'end' => int] (Unix timestamps).
     */
    public static function get_date_range(int $view, ?string $date = null): array {
        $timestamp = $date ? strtotime(date('Y-m-d H:i:s', $date)) : time();

        switch ($view) {
            case filter_controls::DAY:
                $start = strtotime("midnight", $timestamp);
                $end = strtotime("tomorrow", $start) - 1;
                break;

            case filter_controls::WEEK:
                $dayOfWeek = date('N', $timestamp);
                $start = strtotime("-" . ($dayOfWeek - 1) . " days", $timestamp);
                $start = strtotime("midnight", $start);

                $end = strtotime("+" . (7 - $dayOfWeek) . " days", $timestamp);
                $end = strtotime("tomorrow", $end) - 1;
                break;

            case filter_controls::MONTH:
                $start = strtotime(date('Y-m-01 00:00:00', $timestamp));
                $end = strtotime(date('Y-m-t 23:59:59', $timestamp));
                break;

            case filter_controls::ALL:
            default:
                $start = 0;
                $end = strtotime('+10 years') - 1;
                break;
        }

        return compact('start', 'end');
    }    
}
