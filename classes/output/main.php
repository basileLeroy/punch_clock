<?php 

namespace mod_punchclock\output;

use renderable;
use renderer_base;
use templatable;
use stdClass;

class main implements renderable, templatable {
    public function export_for_template(renderer_base $output) {
        global $USER, $COURSE, $PAGE;

        $cm = get_coursemodule_from_instance('punchclock', $PAGE->cm->instance, $COURSE->id);
        $context = \context_module::instance($cm->id);

        $data = new stdClass();
        $data->greetingMessage = get_string('greeting', 'mod_punchclock');
        $data->currentDate = userdate(time(), '%A, %d %B %Y');
        $data->currentHours = userdate(time(), '%H:%M');
        $data->deadline = strtotime(date('Y-m-d') . ' 09:00:00') * 1000; // JS-friendly timestamp
        $data->userid = $USER->id;
        $data->courseid = $COURSE->id;
        $data->punchclockid = $PAGE->cm->instance;

        return $data;
    }
}