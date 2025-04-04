<?php

/**
 * Page to Bulk-delete Selected sessions.
 *
 * @package    mod_punchclock
 * @copyright  2025 onwards Basile Leroy {@link https://basileleroy.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

$id = required_param('id', PARAM_INT);
$dates = optional_param_array('dates', array(), PARAM_INT);

$cm = get_coursemodule_from_id('punchclock', $id, 0, false, MUST_EXIST);
$course = get_course($cm->course);
$context = context_module::instance($cm->id);

require_login($course, true, $cm);
require_capability('mod/punchclock:manage', $context);

$PAGE->set_url('/mod/punchclock/bulkdelete.php', ['id' => $id]);
$PAGE->set_title(get_string('modulename', 'mod_punchclock'));
$PAGE->set_heading('Delete Sessions');
$PAGE->set_context($context);

echo $OUTPUT->header();
echo "<h3>Delete Sessions</h3>";
// Your session management logic here
echo $OUTPUT->footer();