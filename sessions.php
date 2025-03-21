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
require_once($CFG->dirroot . '/mod/punchclock/classes/output/page_filter_controls.php');

use \mod_punchclock\output\page_filter_controls;
use \mod_punchclock\enums\filter_controls;

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

$filtercontrols = new page_filter_controls($id, $view);

echo $OUTPUT->header();
echo "<h3>Handle your sessions</h3>";
echo $OUTPUT->render_from_template('mod_punchclock/components/filterbar', $filtercontrols->render());
echo $OUTPUT->footer();