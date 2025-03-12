<?php

/**
 * API calls for the module.
 *
 * @package    mod_punchclock
 * @copyright  2025 onwards Basile Leroy {@link https://basileleroy.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/locallib.php');

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

    echo "<pre>";
    var_dump($data);
    echo "</pre>";
    die();

    $transaction = $DB->start_delegated_transaction(); // Start transaction

    try {
        // Insert into the main table
        $instance_id = insert_punchclock($DB, $data);

        // Insert related exceptions if they exist
        // if (!empty($data->exceptions)) {
        //     insert_punchclock_exceptions($data->exceptions, $instance_id);
        // }

        // // Insert other related data (future-proof)
        // if (!empty($data->extra_settings)) {
        //     insert_punchclock_settings($data->extra_settings, $instance_id);
        // }

        $transaction->allow_commit(); // Commit all changes
        return $instance_id; // Return the main record ID

    } catch (Exception $e) {
        $transaction->rollback($e); // Rollback on failure
        return false;
    }
}

/**
 * Deletes an instance of the punchclock module.
 *
 * @param int $id The ID of the activity instance being deleted.
 * @return bool True if successful, false otherwise.
 */
function punchclock_delete_instance($id) {
    global $DB;

    // Check if the instance exists
    if (!$record = $DB->get_record('punchclock', ['id' => $id])) {
        return false; // If the instance is not found, return false
    }

    // Delete associated records (if any) before removing the main instance
    $DB->delete_records('punchclock_entries', ['punchclockid' => $id]); // Example if you have related data

    // Delete the main instance record
    $DB->delete_records('punchclock', ['id' => $id]);

    return true;
}
