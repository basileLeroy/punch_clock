<?php
/**
 * Sessions management page for PunchClock module.
 *
 * @package    mod_punchclock
 * @copyright  2025 onwards Basile Leroy {@link https://basileleroy.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_once($CFG->dirroot.'/mod/punchclock/classes/enums/filtercontrols.php');

use mod_punchclock\forms\edittables;
use mod_punchclock\utils\date_utils;
use mod_punchclock\enums\filter_controls;
use mod_punchclock\output\sessions_overview;
use mod_punchclock\output\time_range_selector;

// Get and validate parameters
$id = required_param('id', PARAM_INT);
$date = required_param('date', PARAM_INT);
$view = optional_param('view', filter_controls::WEEK, PARAM_INT);

// Get course module and context
$cm = get_coursemodule_from_id('punchclock', $id, 0, false, MUST_EXIST);
$course = get_course($cm->course);
$context = context_module::instance($cm->id);

// Require login and capabilities
require_login($course, true, $cm);
require_capability('mod/punchclock:manage', $context);

// Set up page
$PAGE->set_url('/mod/punchclock/sessions.php', ['id' => $id, 'date' => time()]);
$PAGE->set_title(get_string('modulename', 'mod_punchclock'));
$PAGE->set_heading(get_string('sessions', 'mod_punchclock'));
$PAGE->set_context($context);
$PAGE->requires->css('/mod/punchclock/style.css');
$PAGE->requires->js_call_amd('mod_punchclock/datepicker', 'init');
$PAGE->requires->js_call_amd('mod_punchclock/bulkselect', 'init');


// Initialize forms
$pageparams = ['id' => $id, 'view' => $view, 'date' => $date];
$tableform = new edittables(null, $pageparams);


$submitaction = optional_param('submit', '', PARAM_ALPHA);
$dates = optional_param_array('dates', [], PARAM_INT);



if ($submitaction === 'bulkedit') {
    redirect(new moodle_url('/mod/punchclock/bulkedit.php', ['id' => $id]));
} elseif ($submitaction === 'bulkdelete') {
    redirect(new moodle_url('/mod/punchclock/bulkdelete.php', ['id' => $id]));
}

// Get date range and sessions
$date_range = date_utils::get_date_range($view, $date);
$sessions = $DB->get_records_sql("
    SELECT date, COUNT(id) as session_count
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

// Prepare table output
$table = new sessions_overview(['sessions' => $sessions], $cm->instance);
$tableform->set_table_html($table->render());

// Prepare filter controls
$filtercontrols = [
    "buttons" => (new time_range_selector($id, $view))->get(),
];

// Output page
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('manage_sessions', 'mod_punchclock'));
echo $OUTPUT->render_from_template('mod_punchclock/components/filterbar', $filtercontrols);
$tableform->display();
echo $OUTPUT->footer();