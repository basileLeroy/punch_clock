<?php

/**
 * Punch Clock table action buttons.
 *
 * @package    mod_punchclock
 * @copyright  2025 onwards Basile Leroy {@link https://basileleroy.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_punchclock\output;

defined('MOODLE_INTERNAL') || die();

use mod_punchclock\enums\filter_controls;
use moodle_url;

/**
 * Class table_actions
 * Generates the default action buttons on the session tables.
 */
class table_actions
{
    /**
     * Prepares navigation elements with navigation links
     * @return string Navigation element.
     */
    private function navigation_block()
    {
        global $PAGE, $OUTPUT;

        // Get URL parameters, ensuring defaults
        $id = required_param('id', PARAM_INT);
        $view = optional_param('view', filter_controls::WEEK, PARAM_INT);
        $date = optional_param('date', time(), PARAM_INT);

        switch ($view) {
            case filter_controls::MONTH:
                $currentMonth = date('F Y', $date);
                $prevMonth = strtotime('-1 month', $date);
                $nextMonth = strtotime('+1 month', $date);

                $datepickerHtml = $OUTPUT->render_from_template('mod_punchclock/components/datepicker', [
                    'date' => $currentMonth
                ]);

                return '<div class="navigation text-end mb-3">'
                    . '<div class="btn-group">'
                    . '<a href="' . new moodle_url($PAGE->url, ['id' => $id, 'view' => $view, 'date' => $prevMonth]) . '" class="btn btn-secondary">◀️ Previous Month</a>'
                    . $datepickerHtml
                    . '<a href="' . new moodle_url($PAGE->url, ['id' => $id, 'view' => $view, 'date' => $nextMonth]) . '" class="btn btn-secondary">Next Month ▶️</a>'
                    . '</div>'
                    . '</div>';

            case filter_controls::WEEK:
                $startOfWeek = strtotime('Monday this week', $date);
                $endOfWeek = strtotime('Sunday this week', $date);
                $prevWeek = strtotime('-1 week', $date);
                $nextWeek = strtotime('+1 week', $date);

                $dateRange = date('M j', $startOfWeek) . ' - ' . date('M j', $endOfWeek);

                $datepickerHtml = $OUTPUT->render_from_template('mod_punchclock/components/datepicker', [
                    'date' => $dateRange
                ]);

                return '<div class="navigation text-end mb-3">'
                    . '<div class="btn-group">'
                    . '<a href="' . new moodle_url($PAGE->url, ['id' => $id, 'view' => $view, 'date' => $prevWeek]) . '" class="btn btn-secondary">◀️ Previous Week</a>'
                    . $datepickerHtml
                    . '<a href="' . new moodle_url($PAGE->url, ['id' => $id, 'view' => $view, 'date' => $nextWeek]) . '" class="btn btn-secondary">Next Week ▶️</a>'
                    . '</div>'
                    . '</div>';

            case filter_controls::DAY:
                $currentDate = date('M j, Y', $date);
                $prevDay = strtotime('-1 day', $date);
                $nextDay = strtotime('+1 day', $date);

                $datepickerHtml = $OUTPUT->render_from_template('mod_punchclock/components/datepicker', [
                    'date' => $currentDate
                ]);

                return '<div id="navigation" class="text-end mb-3">'
                    . '<div class="btn-group">'
                    . '<a href="' . new moodle_url($PAGE->url, ['id' => $id, 'view' => $view, 'date' => $prevDay]) . '" class="btn btn-secondary">◀️ Yesterday</a>'
                    . $datepickerHtml
                    . '<a href="' . new moodle_url($PAGE->url, ['id' => $id, 'view' => $view, 'date' => $nextDay]) . '" class="btn btn-secondary">Tomorrow ▶️</a>'
                    . '</div>'
                    . '</div>';

            case filter_controls::ALL:
            default:
                return '';
        }
    }

    private function bulkaction_block ()
    {
        return '<div class="bulkactions">'
            . '<button type="submit" name="submit" value="bulkedit" class="bulkeditbtn m-2 btn btn-outline-primary btn-sm">Edit selected (0)</button>'
            . '<button type="submit" name="submit" value="bulkdelete" class="bulkdeletebtn m-2 btn btn-danger btn-sm">Delete selected (0)</button>'
            . '</div>';
    }

    public function render()
    {
        $element = '<div id="tableactions" class="d-flex justify-content-between w-100" >';
        $element .= $this->bulkaction_block();
        $element .= $this->navigation_block();
        $element .= '</div>';

        return $element;
    }
}
