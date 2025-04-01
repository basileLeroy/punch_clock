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
    global $USER, $COURSE, $DB;

    $id = required_param('id', PARAM_INT);
    $cm = get_coursemodule_from_id('punchclock', $id);
    $context = context_module::instance($cm->id);

    require_login($COURSE, false, $cm);

    date_default_timezone_set('Europe/Brussels');

    $now = time();
    $today = strtotime('today 09:00:00');
    $deadline = ($now > $today) ? strtotime('tomorrow 09:00:00') : $today;

    $allowed_start = $deadline - (15 * 60); // 15 minutes avant
    $allowed_end = $deadline + (2 * 3600);  // 2 heures après

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
        'deadline' => $deadline * 1000, // format JS
        'allowed_start' => $allowed_start * 1000,
        'allowed_end' => $allowed_end * 1000,
        'userid' => $USER->id,
        'courseid' => $COURSE->id,
        'punchclockid' => $cm->instance
    ];

    $today = strtotime('today');
    $session = $DB->get_record('punchclock_sessions', [
        'user_id' => $USER->id,
        'course_id' => $COURSE->id,
        'punchclock_id' => $cm->instance,
        'date' => $today
    ]);

    $checkin_a = '--:--';
    $checkout_a = '--:--';
    $checkin_b = '--:--';
    $checkout_b = '--:--';

    if ($session) {
        if (!empty($session->checkin_a)) {
            $checkin_a = date('H:i', $session->checkin_a);
        }
        if (!empty($session->checkout_a)) {
            $checkout_a = date('H:i', $session->checkout_a);
        }
        if (!empty($session->checkin_b)) {
            $checkin_b = date('H:i', $session->checkin_b);
        }
        if (!empty($session->checkout_b)) {
            $checkout_b = date('H:i', $session->checkout_b);
        }
    }

    // Ajouter au contexte
    $templatecontext->checkin_a = $checkin_a;
    $templatecontext->checkout_a = $checkout_a;
    $templatecontext->checkin_b = $checkin_b;
    $templatecontext->checkout_b = $checkout_b;

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
