<?php

namespace mod_punchclock\external;

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");

use external_function_parameters;
use external_single_structure;
use external_value;
use external_api;
use stdClass;

/**
 * External function for logging punch clock data.
 */
class log_punch extends external_api {

    public static function execute_parameters() {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course ID'),
            'punchclockid' => new external_value(PARAM_INT, 'Punchclock instance ID'),
            'type' => new external_value(PARAM_ALPHA, 'Type: morning or afternoon'),
            'time' => new external_value(PARAM_TEXT, 'Time as string')
        ]);
    }

    public static function execute($courseid, $punchclockid, $type, $time) {
        global $USER, $DB;

        self::validate_parameters(self::execute_parameters(), [
            'courseid' => $courseid,
            'punchclockid' => $punchclockid,
            'type' => $type,
            'time' => $time
        ]);

        $record = new stdClass();
        $record->user_id = $USER->id;
        $record->course_id = $courseid;
        $record->punchclock_id = $punchclockid;
        $record->session_id = 0;
        $record->level = $type;
        $record->timestamp = time();
        $record->message = "User {$USER->id} punched at {$time} for {$type}";

        $DB->insert_record('punchclock_logs', $record);

        return ['status' => 'ok'];
    }

    public static function execute_returns() {
        return new external_single_structure([
            'status' => new external_value(PARAM_TEXT, 'Success status')
        ]);
    }
}
