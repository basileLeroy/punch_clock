<?php

/**
 * Plugin installation - Insert default Values to the tables
 *
 * @package    mod_punchclock
 * @copyright  2025 onwards Basile Leroy {@link https://basileleroy.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function xmldb_punchclock_install() {
    global $DB;

    // Define default attendance statuses
    $statuses = [
        (object)[ 'status' => 'Present',  'status_code' => 'P', 'trigger_time' => 0 ],
        (object)[ 'status' => 'Late',     'status_code' => 'L', 'trigger_time' => 900 ], // 900 seconds = 15 minutes
        (object)[ 'status' => 'Excused',  'status_code' => 'E', 'trigger_time' => null ],
        (object)[ 'status' => 'Absent',   'status_code' => 'A', 'trigger_time' => 2 * 60 * 60 ], // 2 hours in seconds
    ];

    // Insert each status into the database
    foreach ($statuses as $status) {
        $DB->insert_record('punchclock_statuses', $status, false);
    }
}