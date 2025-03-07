<?php

/**
 * Activity view page.
 *
 * @package    mod_punchclock
 * @copyright  2025 onwards Basile Leroy {@link https://basileleroy.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once('lib.php');

$id = required_param('id', PARAM_INT);
$cm = get_coursemodule_from_id('punchclock', $id);
$context = context_module::instance($cm->id);
require_login($cm->course, true, $cm);

$PAGE->set_url('/mod/punchclock/view.php', ['id' => $id]);
$PAGE->set_title(get_string('modulename', 'mod_punchclock'));
$PAGE->set_heading(format_string($cm->name));

echo $OUTPUT->header();

if (has_capability('mod/punchclock:manage', $context)) {
    echo "<h3>Teacher View</h3>";
    echo "<p>This is the teacher-specific content.</p>";
} else {

    $templatecontext = (object)[
        'content' => 'something'
    ];

    echo "<h3>Student View</h3>";
    echo $OUTPUT->render_from_template('mod_punchclock/view', $templatecontext);
}

echo $OUTPUT->footer();
