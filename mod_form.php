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
        global $PAGE;

        $PAGE->requires->js_call_amd('mod_punchclock/punchclock_setup', 'init');

        $mform = $this->_form;

        // General section for the plugin
        $mform = $this->create_general_fields($mform);

        // Configurations to set up the sessions
        $mform = $this->create_config_fields($mform);

        // Add exception dates to exclude from the sessions
        $mform = $this->create_exception_fields($mform);

        // Standard course module elements
        $this->standard_coursemodule_elements();

        // Submit buttons
        $this->add_action_buttons();
    }

    private function create_general_fields ($mform) {
        global $CFG, $COURSE, $DB;

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
            $sessiondays[] = &$mform->createElement('checkbox', 'Sun', '', get_string('sunday', 'calendar'));
        }
        $sessiondays[] = &$mform->createElement('checkbox', 'Mon', '', get_string('monday', 'calendar'));
        $sessiondays[] = &$mform->createElement('checkbox', 'Tue', '', get_string('tuesday', 'calendar'));
        $sessiondays[] = &$mform->createElement('checkbox', 'Wed', '', get_string('wednesday', 'calendar'));
        $sessiondays[] = &$mform->createElement('checkbox', 'Thu', '', get_string('thursday', 'calendar'));
        $sessiondays[] = &$mform->createElement('checkbox', 'Fri', '', get_string('friday', 'calendar'));
        $sessiondays[] = &$mform->createElement('checkbox', 'Sat', '', get_string('saturday', 'calendar'));
        if ($CFG->calendar_startwday !== '0') { // Week start from sunday.
            $sessiondays[] = &$mform->createElement('checkbox', 'Sun', '', get_string('sunday', 'calendar'));
        }
        $mform->addGroup($sessiondays, 'sessiondays', get_string('repeaton', 'mod_punchclock'), array('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'), true);
        $mform->addRule('sessiondays', null, 'required', null, 'client');

        return $mform;
    }

    private function create_config_fields ($mform) {
        $mform->addElement('header', 'sessionconfig', get_string('configsection', 'mod_punchclock'));
        $mform->setExpanded('sessionconfig');

        $mform->addElement('duration', 'opensessionearly', get_string('opensessionearly', 'mod_punchclock'));


        $hours = array_combine(range(0, 23), array_map(fn($h) => sprintf('%02d', $h), range(0, 23)));
        $minutes = array_combine(range(0, 59), array_map(fn($m) => sprintf('%02d', $m), range(0, 59)));

        // Define a single time block group
        $timeblockfields = [];

        $timeblock = [];
        $timeblock[] = $mform->createElement('static', 'startlabel', '', ' From: ');
        $timeblock[] = $mform->createElement('select', 'starthour', '', $hours);
        $timeblock[] = $mform->createElement('select', 'startminute', '', $minutes);

        $timeblock[] = $mform->createElement('static', 'endlabel', '', ' - To: ');
        $timeblock[] = $mform->createElement('select', 'endhour', '', $hours);
        $timeblock[] = $mform->createElement('select', 'endminute', '', $minutes);

        // Group time selectors into one row
        $timeblockfields[] = $mform->createElement('group', 'timeblock', get_string('timeblock', 'mod_punchclock'), $timeblock, ' ', false);

        // Define repeatable elements
        $repeatno = 1; // Initial number of time blocks
        $repeateloptions = []; // Extra options

        $this->repeat_elements(
            $timeblockfields,
            $repeatno,
            $repeateloptions,
            'timeblock_repeats',
            'timeblock_add_fields',
            1,
            get_string('addtimeblock', 'mod_punchclock'),
            true
        );

        $mform->addElement('html', '
            <div class="divider bulk-hidden d-flex justify-content-center align-items-center always-visible my-3">
                <hr>
                <div class="divider-content px-3">
                    <button type="button" id="add-timeblock-button" class="btn add-content d-flex justify-content-center align-items-center p-1 icon-no-margin" >
                        <div class="px-1">
                            <i class="icon fa fa-plus fa-fw " aria-hidden="true"></i>
                            <span class="activity-add-text pr-1">' . get_string('addtimeblock', 'mod_punchclock') . '</span>
                        </div>
                    </button>
                </div>
            </div>
        ');

        $mform->addElement('hidden', 'timeblock_count', 1); // Start with 1 block
        $mform->setType('timeblock_count', PARAM_INT);

        return $mform;
    }

    private function create_exception_fields ($mform) {
        $mform->addElement('header', 'exceptionsection', get_string('exceptions', 'mod_punchclock'));
        $mform->setExpanded('exceptionsection');
        
        $exceptionfields = [];
        
        $exceptionfields[] = $mform->createElement('text', 'description', get_string('description', 'mod_punchclock'));
        $mform->setType('description', PARAM_TEXT);
        $exceptionfields[] = $mform->createElement('date_selector', 'startdate', get_string('from', 'mod_punchclock'));
        $exceptionfields[] = $mform->createElement('date_selector', 'enddate', get_string('to', 'mod_punchclock'));

        $exceptionfields[] = $mform->createElement('html', '
            <div class="exception-divider my-4 mx-auto w-75">
                <hr class="my-3">
            </div>
        ');

        $repeatno = 1;

        $repeateloptions = array();
        $repeateloptions['description']['default'] = '';
        $repeateloptions['description']['type'] = PARAM_TEXT;
        $repeateloptions['startdate']['default'] = time();
        $repeateloptions['startdate']['type'] = PARAM_INT;
        $repeateloptions['enddate']['default'] = time() + 86400;
        $repeateloptions['enddate']['type'] = PARAM_INT;

        $this->repeat_elements(
            $exceptionfields, 
            $repeatno, 
            $repeateloptions, 
            'exception_repeats', 
            'exception_add_fields', 
            1, 
            get_string('addexception', 'mod_punchclock'),
            true
        );
        
        $mform->addElement('html', '
            <div class="divider bulk-hidden d-flex justify-content-center align-items-center always-visible my-3">
                <hr>
                <div class="divider-content px-3">
                    <button type="button" id="add-holiday-button" class="btn add-content d-flex justify-content-center align-items-center p-1 icon-no-margin" >
                        <div class="px-1">
                            <i class="icon fa fa-plus fa-fw " aria-hidden="true"></i>
                            <span class="activity-add-text pr-1">' . get_string('addexception', 'mod_punchclock') . '</span>
                        </div>
                    </button>
                </div>
            </div>
        ');

        return $mform;
    }
}
