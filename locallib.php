<?php

/**
 * The local helper functions of the module.
 *
 * @package    mod_punchclock
 * @copyright  2025 onwards Basile Leroy {@link https://basileleroy.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

use mod_punchclock\enums\PunchclockLog;

function insert_punchclock($DB, $data)
{
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
        $record->block2_start = null;
    }

    if (!empty($data->endhour2) && !empty($data->endminute2)) {
        $record->block2_stop = sprintf('%02d:%02d:00', $data->endhour2, $data->endminute2);
    } else {
        $record->block2_stop = null;
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
function insert_punchclock_holidays($data, $instance_id)
{
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

function insert_punchclock_sessions($data, $instance_id) {
    global $DB, $USER, $COURSE;

    $holidays = $DB->get_records('punchclock_holidays', ['punchclock_id' => $instance_id]);
    $starttraining = $data->start_date;
    $endtraining = $data->end_date;
    $twentyfourhours = 86400;
    $allowed_days = array_keys(array_filter((array) $data->sessiondays));

    $courseid = $data->course;
    $context = context_course::instance($courseid);
    $roleid = 5; // Default Moodle role ID for 'student'

    $students = get_role_users($roleid, $context);

    $counter = 0;
    foreach ($students as $student) {
        for ($date = $starttraining; $date <= $endtraining; $date += $twentyfourhours) {
            $weekday = date('D', $date);

            foreach ($holidays as $holiday) {
                if ($date >= $holiday->start_date && $date <= $holiday->end_date) {
                    continue 2;
                }
            }

            if (!in_array($weekday, $allowed_days)) {
                continue;
            }

            $counter++;

            $record = new stdClass();
            $record->user_id = $student->id;
            $record->course_id = $courseid;
            $record->punchclock_id = $instance_id;
            $record->date = $date;
            $record->checkin_default_a = sprintf('%02d:%02d:00', $data->starthour[0] ?? 0, $data->startminute[0] ?? 0);
            $record->checkout_default_a = sprintf('%02d:%02d:00', $data->endhour[0] ?? 0, $data->endminute[0] ?? 0);
            $record->checkin_default_b = sprintf('%02d:%02d:00', $data->starthour[1] ?? 0, $data->startminute[1] ?? 0);
            $record->checkout_default_b = sprintf('%02d:%02d:00', $data->endhour[1] ?? 0, $data->endminute[1] ?? 0);
            $record->created_at = time();

            $DB->insert_record('punchclock_sessions', $record, false);
        }
    }

    insert_punchclock_log(
        PunchclockLog::COURSE,
        __FUNCTION__,
        "Create " . count($students) . " session(s) in $COURSE->fullname"
    );
}

function insert_punchclock_log($level, $action, $message, $session_id = null, $instance_id = null) {
    global $DB, $COURSE, $USER;

    $fullname = $USER->firstname . " " . $USER->lastname;

    $log = PunchclockLog::get_log_message($level, $fullname, $action, $message);

    $record = new stdClass();

    $record->user_id = $USER->id;
    $record->session_id = $session_id;
    $record->punchclock_id = $instance_id;
    $record->course_id = $COURSE->id;
    $record->level = $level;
    $record->timestamp = time();
    $record->message = $log;

    $DB->insert_record('punchclock_logs', $record, false);

}