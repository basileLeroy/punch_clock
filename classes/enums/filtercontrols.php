<?php

/**
 * Punch Clock log class.
 *
 * @package    mod_punchclock
 * @copyright  2025 onwards Basile Leroy {@link https://basileleroy.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_punchclock\enums;


defined('MOODLE_INTERNAL') || die();

/**
 * Enum class for view filters.
 */
class filter_controls {
    public const ALL = 0;
    public const MONTH = 1;
    public const WEEK = 2;
    public const DAY = 3;

    /**
     * Get all view options.
     *
     * @return array
     */
    public static function all(): array {
        return [
            self::ALL => 'All',
            self::MONTH => 'Month',
            self::WEEK => 'Week',
            self::DAY => 'Day',
        ];
    }
}