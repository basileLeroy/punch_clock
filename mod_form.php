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
        global $CFG, $COURSE, $DB, $PAGE;

        $PAGE->requires->js_call_amd('mod_punchclock/punchclock_setup', 'init');

        $mform = $this->_form;

        // Get course data
        $course = $DB->get_record('course', ['id' => $COURSE->id], 'startdate, enddate');

        // Default values from course start/end date
        $default_start_date = $course->startdate ?? time(); // Use course start date or current time
        $default_end_date = $course->enddate ?? strtotime('+1 month', $default_start_date); // Use course end date or +1 month default

        $mform->addElement('header', 'general', get_string('generalsection', 'mod_punchclock'));

        // Activity Name
        $mform->addElement('text', 'name', get_string('getactivityname', 'mod_punchclock'), ['size' => '64']);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->setDefault('name', get_string('activityname', 'mod_punchclock'));

        // Start Date Selector
        $mform->addElement('date_selector', 'start_date', get_string('getcoursestartdate', 'mod_punchclock'));
        $mform->addRule('start_date', null, 'required', null, 'client');
        $mform->setDefault('start_date', $default_start_date);

        // End Date Selector
        $mform->addElement('date_selector', 'end_date', get_string('getcourseenddate', 'mod_punchclock'));
        $mform->addRule('end_date', null, 'required', null, 'client');
        $mform->setDefault('end_date', $default_end_date);

        $sessiondays = array();
        if ($CFG->calendar_startwday === '0') { // Week start from sunday.
            $sessiondays[] =& $mform->createElement('checkbox', 'Sun', '', get_string('sunday', 'calendar'));
        }
        $sessiondays[] =& $mform->createElement('checkbox', 'Mon', '', get_string('monday', 'calendar'));
        $sessiondays[] =& $mform->createElement('checkbox', 'Tue', '', get_string('tuesday', 'calendar'));
        $sessiondays[] =& $mform->createElement('checkbox', 'Wed', '', get_string('wednesday', 'calendar'));
        $sessiondays[] =& $mform->createElement('checkbox', 'Thu', '', get_string('thursday', 'calendar'));
        $sessiondays[] =& $mform->createElement('checkbox', 'Fri', '', get_string('friday', 'calendar'));
        $sessiondays[] =& $mform->createElement('checkbox', 'Sat', '', get_string('saturday', 'calendar'));
        if ($CFG->calendar_startwday !== '0') { // Week start from sunday.
            $sessiondays[] =& $mform->createElement('checkbox', 'Sun', '', get_string('sunday', 'calendar'));
        }
        $mform->addGroup($sessiondays, 'sessiondays', get_string('repeaton', 'mod_punchclock'), array('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'), true);
        $mform->addRule('sessiondays', null, 'required', null, 'client');

        $mform->addElement('header', 'sessionconfig', get_string('configsection', 'mod_punchclock'));
        $mform->setExpanded('sessionconfig');

        $mform->addElement('duration', 'opensessionearly', get_string('opensessionearly', 'mod_punchclock'));

        $options = array(
            '1' => '1',
            '2' => '2'
        );
        
        $dropdown = $mform->createElement('select', 'addsessionblocks', '', $options);
        $staticText = $mform->createElement('static', 'addsessionblocks_label', '', ' block(s)');
        
        $mform->addGroup(array($dropdown, $staticText), 'dropdown_group', get_string('addsessionblocks', 'mod_punchclock'), ' ', false);


        $hours = array_combine(range(0, 23), range(0, 23));
        $minutes = array_combine(range(0, 59), range(0, 59));


        // BLOCK 1 TIME SELECTION
        $startHour1 = $mform->createElement('select', 'starthour1', '', $hours);
        $startMinute1 = $mform->createElement('select', 'startminute1', '', $minutes);
        $startLabel1 = $mform->createElement('static', 'startlabel1', '', ' From: ');

        $endHour1 = $mform->createElement('select', 'endhour1', '', $hours);
        $endMinute1 = $mform->createElement('select', 'endminute1', '', $minutes);
        $endLabel1 = $mform->createElement('static', 'endlabel1', '', ' - To: ');

        $mform->addGroup(
            array($startLabel1, $startHour1, $startMinute1, $endLabel1, $endHour1, $endMinute1),
            'block1time',
            get_string('block1time', 'mod_punchclock'),
            ' ',
            false
        );

        // $mform->addElement('text', 'configure_time', get_string('configuretimeblocks', 'mod_punchclock'));
        // $mform->setType('configure_time', PARAM_RAW);

        // Standard course module elements
        $this->standard_coursemodule_elements();

        // Submit buttons
        $this->add_action_buttons();
    }
}
