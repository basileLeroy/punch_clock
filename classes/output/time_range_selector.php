<?php
/**
 * Punch Clock time range selector options.
 *
 * @package    mod_punchclock
 * @copyright  2025 onwards Basile Leroy {@link https://basileleroy.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_punchclock\output;

defined('MOODLE_INTERNAL') || die();

use moodle_url;
use mod_punchclock\enums\filter_controls;

class time_range_selector {
    private $view;
    private $id;
    private $buttons;
    
    /**
     * Constructor
     *
     * @param int $id Course module ID
     * @param int $view Active view parameter
     */
    public function __construct(int $id, int $view)
    {
        global $PAGE;
        $this->id = $id;
        $this->view = $view;

        foreach (filter_controls::all() as $view => $text) {
            $is_active = ($view == $this->view);
            $this->buttons[] = [
                'text' => $text,
                'url' => format_string(new moodle_url($PAGE->url, ['id' => $this->id, 'view' => $view])),
                'class' => $is_active ? 'mx-1' : 'btn btn-outline-primary mx-1',
                'active' => $is_active
            ];
        }
    }

    /**
     * Returns the full list of time range options
     */
    public function get() {

        return $this->buttons;
    }
}