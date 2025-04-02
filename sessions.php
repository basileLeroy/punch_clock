<?php

/**
 * Exports view page.
 *
 * @package    mod_punchclock
 * @copyright  2025 onwards Basile Leroy {@link https://basileleroy.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_once($CFG->dirroot . '/mod/punchclock/classes/forms/filterform.php');
require_once($CFG->dirroot . '/mod/punchclock/classes/enums/filtercontrols.php');

use mod_punchclock\forms\filterform;
use mod_punchclock\utils\date_utils;
use mod_punchclock\enums\filter_controls;
use mod_punchclock\output\sessions_overview;
use mod_punchclock\output\time_range_selector;

$id         = required_param('id', PARAM_INT);
$view       = optional_param('view', filter_controls::WEEK, PARAM_INT);
$cm         = get_coursemodule_from_id('punchclock', $id, 0, false, MUST_EXIST);
$course     = get_course($cm->course);
$context    = context_module::instance($cm->id);

require_login($course, true, $cm);
require_capability('mod/punchclock:manage', $context);

$PAGE->set_url('/mod/punchclock/sessions.php', ['id' => $id]);
$PAGE->set_title(get_string('modulename', 'mod_punchclock'));
$PAGE->set_heading('Sessions');
$PAGE->set_context($context);

$filtercontrols = [];

$buttons = new time_range_selector($id, $view);
$mform = new filterform(null, ['id' => $id, 'view' => $view, 'date' => $date ?? null]);

$filtercontrols = [
    "calendar" => $mform->render(),
    "buttons" => $buttons->get(),
];

if ($mform->is_submitted() && $mform->is_validated()) {
    $data = $mform->get_data();
    if ($data) {
        $date = $data->date ?? null;
        redirect(new moodle_url('/mod/punchclock/sessions.php', [
            'id' => $id,
            'view' => $view,
            'date' => $date
        ]));
    }
}

$date = optional_param('date', null, PARAM_TEXT);

$date_range = date_utils::get_date_range($view, $date ?? null);

$sessions = $DB->get_records_sql("
    SELECT 
        date,
        COUNT(id) as session_count
    FROM {punchclock_sessions}
    WHERE punchclock_id = :punchclock_id
    AND date BETWEEN :startdate AND :enddate
    GROUP BY date
    ORDER BY date ASC
", [
    'punchclock_id' => $cm->instance,
    'startdate' => $date_range["start"],
    'enddate' => $date_range["end"]
]);

$table = new sessions_overview(['sessions' => $sessions], $cm->instance);

echo $OUTPUT->header();
echo "<h3>Handle your sessions</h3>";
echo $OUTPUT->render_from_template('mod_punchclock/components/filterbar', $filtercontrols);
echo $table->render();
echo $OUTPUT->footer();