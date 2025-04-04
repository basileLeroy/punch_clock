<?php
/**
 * Punch Clock form around sessions table to update selected sessions.
 *
 * @package    mod_punchclock
 * @copyright  2025 onwards Basile Leroy {@link https://basileleroy.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_punchclock\forms;

defined('MOODLE_INTERNAL') || die();
require_once("$CFG->libdir/formslib.php");

use moodle_url;
use moodleform;
use moodle_exception;


class edittables extends moodleform
{
    public function __construct($actionurl = null, $customdata = null)
    {
        if (!$actionurl) {
            if (empty($customdata['id'])) {
                throw new moodle_exception('missingid', 'mod_punchclock');
            }
            
            $actionurl = new moodle_url('/mod/punchclock/sessions.php', [
                'id' => $customdata['id']
            ]);
        }
        parent::__construct($actionurl, $customdata);
    }

    public function definition() {
        $mform = $this->_form;

        $mform->setAttributes(["class" => "bulkselectsessions"]);

        $mform->addElement('static', 'sessions_table', '', '');  // Placeholder for table
    }

    public function set_table_html($html) {
        $this->_form->getElement('sessions_table')->setValue($html);
    }

    // Optional: you can add validation if needed
    public function validation($data, $files) {
        return [];
    }
}