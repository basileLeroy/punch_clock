<?php

/**
 * Setup form for teachers/admin.
 *
 * @package    mod_punchclock
 * @copyright  2025 onwards Basile Leroy {@link https://basileleroy.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot . '/course/moodleform_mod.php');

class mod_punchclock_mod_form extends moodleform_mod
{
    function definition()
    {
        global $COURSE, $DB;
        $mform = $this->_form;

        // Get course data
        $course = $DB->get_record('course', ['id' => $COURSE->id], 'startdate, enddate');

        // Default values from course start/end date
        $default_start_date = $course->startdate ?? time(); // Use course start date or current time
        $default_end_date = $course->enddate ?? strtotime('+1 month', $default_start_date); // Use course end date or +1 month default

        // Activity Name
        $mform->addElement('text', 'name', get_string('getactivityname', 'mod_punchclock'), ['size' => '64']);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->setDefault('name', get_string('activityname', 'mod_punchclock'));

        // Start Date Selector
        $mform->addElement('date_selector', 'start_date', get_string('getcoursestartdate', 'mod_punchclock'));
        $mform->setDefault('start_date', $default_start_date);

        // End Date Selector
        $mform->addElement('date_selector', 'end_date', get_string('getcourseenddate', 'mod_punchclock'));
        $mform->setDefault('end_date', $default_end_date);

        // Standard course module elements
        $this->standard_coursemodule_elements();

        // Submit buttons
        $this->add_action_buttons();
    }
}
