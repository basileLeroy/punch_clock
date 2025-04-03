<?php

namespace mod_punchclock\external;

use external_function_parameters;
use external_value;
use external_single_structure;
use external_api;
use stdClass;

/**
 * External API to log a punch clock entry.
 */
class log_punch extends external_api {

    /**
     * Define parameters for the web service.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course ID'),
            'punchclockid' => new external_value(PARAM_INT, 'Punchclock instance ID'),
            'type' => new external_value(PARAM_ALPHA, 'Punch type: morning or afternoon'),
            'time' => new external_value(PARAM_TEXT, 'Time as string (HH:MM:SS)')
        ]);
    }

    /**
     * Log the punch in the database.
     *
     * @param int $courseid
     * @param int $punchclockid
     * @param string $type
     * @param string $time
     * @return array
     */
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
        $record->session_id = 0; // Tu pourras l'utiliser plus tard si besoin
        $record->level = $type; // 'morning' ou 'afternoon'
        $record->timestamp = time();
        $record->message = "User {$USER->id} punched at {$time} for {$type}";

        $DB->insert_record('punchclock_logs', $record);

        return ['status' => 'ok'];
    }

    /**
     * Define the structure of the return value.
     *
     * @return external_single_structure
     */
    public static function execute_returns() {
        return new external_single_structure([
            'status' => new external_value(PARAM_TEXT, 'Result status')
        ]);
    }
}
