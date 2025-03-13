<?php

/**
 * The local helper functions of the module.
 *
 * @package    mod_punchclock
 * @copyright  2025 onwards Basile Leroy {@link https://basileleroy.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

function insert_punchclock($DB, $data) {
    $days = !empty($data->sessiondays) ? implode(',', array_keys($data->sessiondays)) : '';

    $record = new stdClass();
    $record->course_id = $data->course;
    $record->name = $data->name;
    $record->start_date = $data->start_date;
    $record->end_date = $data->end_date;
    $record->created_at = time();
    $record->days = $days;
    $record->early_access = isset($data->opensessionearly) ? $data->opensessionearly : 0;
    $record->number_of_blocks = isset($data->addsessionblocks) ? (int)$data->addsessionblocks : 0;

    $record->block1_start = sprintf('%02d:%02d:00', $data->starthour1 ?? 0, $data->startminute1 ?? 0);
    $record->block1_stop = sprintf('%02d:%02d:00', $data->endhour1 ?? 0, $data->endminute1 ?? 0);

    if (!empty($data->starthour2) && !empty($data->startminute2)) {
        $record->block2_start = sprintf('%02d:%02d:00', $data->starthour2, $data->startminute2);
    } else {
        $record->block2_start = '00:00:00';
    }

    if (!empty($data->endhour2) && !empty($data->endminute2)) {
        $record->block2_stop = sprintf('%02d:%02d:00', $data->endhour2, $data->endminute2);
    } else {
        $record->block2_stop = '00:00:00';
    }

    return $DB->insert_record('punchclock', $record, true);
}

/**
 * Inserts Holiday records into the database.
 *
 * This function processes holiday data and inserts it into the 'punchclock_holidays' table.
 * It ensures that:
 * - Descriptions are set to null if empty.
 * - Start dates that are in the past are set to null, along with their end dates.
 *
 * @param object $data An object containing holiday details, including:
 *                     - description (array of strings)
 *                     - startdate (array of timestamps)
 *                     - enddate (array of timestamps)
 * @param int $instance_id The ID of the punchclock instance to associate with course.
 * @return bool Returns true if the holidays were inserted successfully, false if no valid data was provided.
 */
function insert_punchclock_holidays($data, $instance_id) {
    global $DB;

    $today = strtotime('today');

    foreach ($data->description as $index => $description) {
        $description = trim($description);
        $reason = empty($description) ? null : $description;
    
        $startdate = isset($data->startdate[$index]) ? (int)$data->startdate[$index] : null;
        $enddate = isset($data->enddate[$index]) ? (int)$data->enddate[$index] : null;
        $today = strtotime('today'); // Midnight today
    
        // Skip inserting if startdate or enddate is before today
        if ($startdate < $today || $enddate < $today) {
            continue;
        }
    
        $record = new stdClass();
        $record->punchclock_id = $instance_id;
        $record->start_date = $startdate;
        $record->end_date = $enddate;
        $record->reason = $reason;
        $record->created_at = time();
    
        // Insert record into DB
        $DB->insert_record('punchclock_holidays', $record, false);
    }
}

