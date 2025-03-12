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

function insert_punchclock_exception ($data) {

}