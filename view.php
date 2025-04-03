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
$PAGE->requires->css('/mod/punchclock/styles/styles.css');
$PAGE->requires->js_call_amd('mod_punchclock/punchclock', 'init');



// LOGIC

function display_teacher_interface ($OUTPUT) {
    $id = required_param('id', PARAM_INT);

    $buttons = [
        ['text' => 'Sessions', 'url' => new moodle_url('/mod/punchclock/sessions.php', ['id' => $id]), 'class' => 'btn btn-outline-primary mx-3'],
        ['text' => 'Absences', 'url' => new moodle_url('/mod/punchclock/absences.php', ['id' => $id]), 'class' => 'btn btn-outline-primary mx-3'],
        ['text' => 'Exports', 'url' => new moodle_url('/mod/punchclock/exports.php', ['id' => $id]), 'class' => 'btn btn-outline-primary mx-3'],
    ];

    $context = (object)[
        'buttons' => $buttons
    ];

    return $OUTPUT->render_from_template('mod_punchclock/manage', $context);

}

function display_student_interface($OUTPUT) {
    global $USER, $COURSE;

    date_default_timezone_set('Europe/Brussels');

    $now = time();
    $morning_deadline = strtotime('09:00:00');
    $afternoon_deadline = strtotime('13:30:00');


    $currentTime = date("H:i");
    if (strtotime($currentTime) < strtotime("12:30")) {
        $greeting = "Good Morning";
    } elseif (strtotime($currentTime) < strtotime("17:00")) {
        $greeting = "Good Afternoon";
    } else {
        $greeting = "Good Evening";
    }

    $templatecontext = (object)[
        'currentDate' => userdate($now, '%A, %d %B %Y'),
        'currentHours' => $currentTime,
        'greetingMessage' => $greeting,
        'morning_deadline' => $morning_deadline * 1000,
        'afternoon_deadline' => $afternoon_deadline * 1000,
        'userid' => $USER->id,
        'courseid' => $COURSE->id,
    ];

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
