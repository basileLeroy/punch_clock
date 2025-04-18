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
$punchclock = $DB->get_record('punchclock', ['id' => $cm->instance]);
require_login($cm->course, true, $cm);

$PAGE->set_url('/mod/punchclock/view.php', ['id' => $id]);
$PAGE->set_context(context_module::instance($cm->id));
$PAGE->set_cm($cm);
$PAGE->set_title(get_string('modulename', 'mod_punchclock'));
$PAGE->set_heading(format_string($cm->name));
$PAGE->requires->css('/mod/punchclock/styles/styles.css');
$PAGE->requires->js_call_amd('mod_punchclock/punchclock', 'init');



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

function display_student_interface($OUTPUT, $cm) {
    global $USER, $COURSE;

    date_default_timezone_set('Europe/Brussels');

    $now = time();

    $currentTime = date("H:i");
    if (strtotime($currentTime) < strtotime("12:30")) {
        $greeting = "Good Morning";
    } elseif (strtotime($currentTime) < strtotime("17:00")) {
        $greeting = "Good Afternoon";
    } else {
        $greeting = "Good Evening";
    }

    $templatecontext = [
        'currentDate' => userdate($now, '%A, %d %B %Y'),
        'currentHours' => $currentTime,
        'greetingMessage' => $greeting,
        'courseid' => $COURSE->id,
        'punchclockid' => $cm->instance,
    ];

    return $OUTPUT->render_from_template('mod_punchclock/view', $templatecontext);
}


// RENDER VIEW

echo $OUTPUT->header();

if (has_capability('mod/punchclock:manage', $context)) {
    echo display_teacher_interface($OUTPUT);
} else {
    echo display_student_interface($OUTPUT, $cm);
}

echo $OUTPUT->footer();
