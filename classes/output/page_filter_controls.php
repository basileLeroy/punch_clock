<?php

namespace mod_punchclock\output;

defined('MOODLE_INTERNAL') || die();

use moodle_url;

use mod_punchclock\enums\filter_controls;
use mod_punchclock\forms\filterform;

/**
 * Class page_filter_controls
 * Generates filter buttons for session views.
 */
class page_filter_controls {
    private $id;
    private $view;
    private $buttons;
    private $form;

    /**
     * Constructor
     *
     * @param int $id Course module ID
     * @param int $view Active view parameter
     */
    public function __construct(int $id, int $view) {
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

        $mform = new filterform(null, ['id' => $id, 'view' => $view]);

        $this->form = $mform->render();
    }

    public function getViewMode()
    {
        echo $this->view;
    }

    /**
     * Returns the full data to be rendered in the $OUTPUT
     */
    public function render() {

        return [
            "buttons" => $this->buttons,
            "calendar" => $this->form,
        ];
    }
}
