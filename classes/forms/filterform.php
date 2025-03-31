<?php
/**
 * Punch Clock filter form block.
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

class filterform extends moodleform
{
    public function __construct($actionurl = null, $customdata = null)
    {
        if (!$actionurl) {
            $actionurl = new moodle_url('/mod/punchclock/sessions.php', [
                'id' => $customdata['id'] ?? 0,
                'view' => $customdata['view'] ?? 0
            ]);
        }
        parent::__construct($actionurl, $customdata);
    }
    
    public function definition()
    {
        $mform = $this->_form;

        $mform->addGroup(
            [
                $mform->createElement('date_selector', 'date', '', ['style' => 'width: auto; display: inline-block;']),
                $mform->createElement('submit', 'submitbutton', get_string('filter', 'mod_punchclock'), ['class' => 'btn btn-primary']),
            ], 'calendarform', '', [' '], false
        );
    }
}