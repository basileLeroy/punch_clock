<?php

/**
 * Page to Bulk-delete Selected sessions.
 *
 * @package    mod_punchclock
 * @copyright  2025 onwards Basile Leroy {@link https://basileleroy.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_punchclock\forms\bulkeditform;

require_once('../../config.php');

$id = required_param('id', PARAM_INT);
$dates = optional_param_array('dates', array(), PARAM_INT);

$cm = get_coursemodule_from_id('punchclock', $id, 0, false, MUST_EXIST);
$course = get_course($cm->course);
$context = context_module::instance($cm->id);

require_login($course, true, $cm);
require_capability('mod/punchclock:manage', $context);

$PAGE->set_url('/mod/punchclock/bulkedit.php', ['id' => $id]);
$PAGE->set_title(get_string('modulename', 'mod_punchclock'));
$PAGE->set_heading('Edit Sessions');
$PAGE->set_context($context);

$actionurl = new moodle_url('/mod/punchclock/bulkedit.php', ['id' => $id]);
$mform = new bulkeditform($actionurl);
$mform->set_data(['id' => $id]);

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/mod/punchclock/view.php', ['id' => $id]));
} else if ($data = $mform->get_data()) {
    echo "Hello";
    print_object($data); // or use the data for updates
    die();
} else {
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('bulkedit', 'mod_punchclock'));
    $mform->display();
    echo $OUTPUT->footer();
}