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

class filterform extends moodleform
{
    public function definition()
    {
        global $PAGE;

        $data = $this->_customdata ?? [];
        $mform = $this->_form;

        $id = $data["id"] ?? null;
        $id = $data["view"] ?? null;

        $mform->addGroup(
            [
                $mform->createElement('date_selector', 'date', '', ['style' => 'width: auto; display: inline-block;']),
                $mform->createElement('submit', 'submitbutton', get_string('filter', 'mod_punchclock'), ['class' => 'btn btn-primary ms-2']),
            ], 'calendarform', '', [' '], false
        );
    }
}