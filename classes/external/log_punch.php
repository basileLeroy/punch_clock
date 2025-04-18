<?php

namespace mod_punchclock\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;

use external_function_parameters;
use external_value;
use external_single_structure;

require_once($CFG->libdir . '/externallib.php');
/**
 * Dummy external API for testing punchclock.
 */
class log_punch extends \external_api {

    public static function execute_parameters() {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course ID'),
            'punchclockid' => new external_value(PARAM_INT, 'Punchclock instance ID'),
            'type' => new external_value(PARAM_TEXT, 'Punch type: morning-start or afternoon-start'),
            'time' => new external_value(PARAM_TEXT, 'Displayed time string (not used yet)')
        ]);
    }

    public static function execute() {
        return ['status' => 'ok'];
    }

    public static function execute_returns() {
        return new external_single_structure([
            'status' => new external_value(PARAM_TEXT, 'Simple status')
        ]);
    }
}