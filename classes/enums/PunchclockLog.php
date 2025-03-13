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

class PunchclockLog 
{
    const string SYSTEM  = 'SYSTEM';
    const string COURSE  = 'COURSE';
    const string SESSION = 'SESSION';

    public static function get_all() {
        return [
            self::SYSTEM,
            self::COURSE,
            self::SESSION
        ];
    }

    public static function get_log_message($level, $user, $action, $message)
    {
        return "$level | $user - $action: $message.";
    }
}