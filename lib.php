<?php

/**
 * The show page of the module.
 *
 * @package    mod_punchclock
 * @copyright  2025 onwards Basile Leroy {@link https://basileleroy.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Adds a new instance of the punchclock module.
 *
 * @param object $data Form data from mod_form.php
 * @param object $mform Moodle form object
 * @return int ID of the newly inserted record
 */
function punchclock_add_instance($data, $mform = null)
{
    global $DB;

    $record = new stdClass();
    $record->course_id = $data->course;
    $record->name = $data->name;  // Ensure name is saved
    $record->start_date = $data->start_date;
    $record->end_date = $data->end_date;
    $record->created_at = time();

    return $DB->insert_record('punchclock', $record);
}

/**
 * NOT WORKING YET
 * Deletes an instance of the punchclock module.
 *
 * @param int $id The ID of the activity instance being deleted.
 * @return bool True if successful, false otherwise.
 */
function punchclock_delete_instance($id)
{
    dd("Triggered");
    global $DB;

    if (!$record = $DB->get_record('punchclock', ['id' => $id])) {
        return false;
    }

    // Delete the record from the punchclock table
    $DB->delete_records('punchclock', ['id' => $id]);

    return true;
}
