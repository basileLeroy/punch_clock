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


// LOGIC

function create_nav_button(string $text, string $path, array $params) {
    
    return [
        'text' => get_string($text, 'mod_punchclock'),
        'url' => (new moodle_url($path, $params))->out(false),
        'class' => 'btn btn-outline-primary mx-3'
    ];
}


function display_teacher_interface ($OUTPUT) {
    $id = required_param('id', PARAM_INT);
    $date = time();

    $buttons = [
        create_nav_button('sessions', '/mod/punchclock/sessions.php', ['id' => $id, 'date' => time()]),
        create_nav_button('absences', '/mod/punchclock/absences.php', ['id' => $id]),
        create_nav_button('exports', '/mod/punchclock/exports.php', ['id' => $id])
    ];

    $context = (object)[
        'buttons' => $buttons
    ];

    return $OUTPUT->render_from_template('mod_punchclock/manage', $context);

}

function display_student_interface ($OUTPUT) {

    $templatecontext = (object)[
        'content' => 'something'
    ];

    echo "<h3>Student View</h3>";

    return $OUTPUT->render_from_template('mod_punchclock/view', $templatecontext);
}

// RENDER VIEW

echo $OUTPUT->header();

if (has_capability('mod/punchclock:manage', $context)) {
    echo display_teacher_interface($OUTPUT);
} else {

    echo display_student_interface($OUTPUT);
}

echo $OUTPUT->footer();
