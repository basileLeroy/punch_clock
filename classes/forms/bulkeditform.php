<?php

/**
 * Punch Clock form for bulk edits on selected sessions.
 *
 * @package    mod_punchclock
 * @copyright  2025 onwards Basile Leroy {@link https://basileleroy.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_punchclock\forms;

defined('MOODLE_INTERNAL') || die();
require_once("$CFG->libdir/formslib.php");

use moodleform;
use moodle_url;
use moodle_exception;

class bulkeditform extends moodleform
{
    public function definition()
    {
        $mform = $this->_form;

        $mform->setAttributes(["class" => "bulkeditform"]);

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('header', 'sessionconfig', get_string('configsection', 'mod_punchclock'));
        $mform->setExpanded('sessionconfig');

        $mform->addElement('static', 'bulkeditinfo', get_string('info', 'mod_punchclock'), get_string('bulkeditinfotext', 'mod_punchclock'));

        $mform->addElement('duration', 'opensessionearly', get_string('opensessionearly', 'mod_punchclock'));

        $hours = array_combine(range(0, 23), array_map(fn($h) => sprintf('%02d', $h), range(0, 23)));
        $minutes = array_combine(range(0, 59), array_map(fn($m) => sprintf('%02d', $m), range(0, 59)));

        // First timeblock
        $timeblock1 = [];
        $timeblock1[] = $mform->createElement('static', 'startlabel1', '', ' From: ');
        $timeblock1[] = $mform->createElement('select', 'starthour1', '', $hours);
        $timeblock1[] = $mform->createElement('select', 'startminute1', '', $minutes);
        $timeblock1[] = $mform->createElement('static', 'endlabel1', '', ' - To: ');
        $timeblock1[] = $mform->createElement('select', 'endhour1', '', $hours);
        $timeblock1[] = $mform->createElement('select', 'endminute1', '', $minutes);

        $mform->addElement('group', 'timeblock1', get_string('timeblock', 'mod_punchclock') . ' 1', $timeblock1, ' ', false);

        // Second timeblock
        $timeblock2 = [];
        $timeblock2[] = $mform->createElement('static', 'startlabel2', '', ' From: ');
        $timeblock2[] = $mform->createElement('select', 'starthour2', '', $hours);
        $timeblock2[] = $mform->createElement('select', 'startminute2', '', $minutes);
        $timeblock2[] = $mform->createElement('static', 'endlabel2', '', ' - To: ');
        $timeblock2[] = $mform->createElement('select', 'endhour2', '', $hours);
        $timeblock2[] = $mform->createElement('select', 'endminute2', '', $minutes);

        $mform->addElement('group', 'timeblock2', get_string('timeblock', 'mod_punchclock') . ' 2', $timeblock2, ' ', false);

        $this->add_action_buttons();
    }

    // Optional: you can add validation if needed
    public function validation($data, $files)
    {
        $errors = [];

        $index = 0;
        if (($data['starthour'][$index] === "0" && $data["startminute"][$index] === "0") ||
            ($data["endhour"][$index] === "0" && $data["endminutes"][$index] === "0")
        ) {
            $errors["timeblock[$index]"] = get_string('timecannotbenull', 'mod_punchclock');
        }

        return $errors;
    }
}
