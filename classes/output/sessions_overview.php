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

use mod_punchclock\output\table_actions;
use moodle_url;

/**
 * Class sessions_overview
 * Generates a table of session records.
 */
class sessions_overview
{
    private $sessions;
    private $cmid;

    /**
     * Constructor
     *
     * @param array $data An array of session data
     * @param int $cmid Course module ID
     */
    public function __construct(array $data, int $cmid)
    {
        $this->cmid = $cmid;
        $this->prepare_sessions($data["sessions"]);
    }

    /**
     * Updates the session list with new data.
     *
     * @param array $sessions New session data.
     */
    public function update(array $sessions)
    {
        $this->prepare_sessions($sessions);
    }

    /**
     * Prepares session data with action links
     *
     * @param array $sessions Raw session data
     */
    private function prepare_sessions(array $sessions)
    {
        $this->sessions = [];
        foreach ($sessions as $session) {
            $prepared = [
                'unix_date' => $session->date,
                'date' => userdate($session->date, '%A, %b %e, %Y'),
                'view_link' => new moodle_url('/mod/punchclock/view.php', [
                    'id' => $this->cmid,
                    'date' => $session->date
                ]),
                'update_link' => new moodle_url('/mod/punchclock/edit.php', [
                    'id' => $this->cmid,
                    'date' => $session->date
                ]),
                'delete_link' => new moodle_url('/mod/punchclock/delete.php', [
                    'id' => $this->cmid,
                    'date' => $session->date
                ])
            ];
            $this->sessions[] = $prepared;
        }
    }

    /**
     * Renders the table as HTML
     *
     * @return string HTML output of the table
     */
    public function render()
    {
        $tableactions = new table_actions();

        $html = '<table class="table table-striped table-hover">';
        $html .= $tableactions->render();
        $html .= '<thead class="text-center">';
        $html .= '<tr>';
        $html .= '<th class="text-center"><input type="checkbox" class="selectallrows" /></th>';
        $html .= '<th style="width: 33%;" class="text-center">Date</th>';
        $html .= '<th class="text-center">View</th>';
        $html .= '<th class="text-center">Update</th>';
        $html .= '<th class="text-center">Delete</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        // If no sessions exist, show a single-row message
        if (empty($this->sessions)) {
            $html .= '<tr><td colspan="4" class="text-center"><h4>No sessions were found</h4></td></tr>';
        } else {
            // Render session rows
            foreach ($this->sessions as $key => $row) {
                $html .= '<tr>';
                $html .= '<td class="text-center"><input class="selectrow" name="dates[d' . $key . ']" value="' . $row['unix_date'] . '" type="checkbox" /></td>';
                $html .= '<td class="text-center" style="width: 33%;">' . htmlspecialchars($row['date']) . '</td>';
                $html .= '<td class="text-center"><a href="' . htmlspecialchars($row['view_link']) . '" class="btn btn-link">See page</a></td>';
                $html .= '<td class="text-center"><a href="' . htmlspecialchars($row['update_link']) . '" class="btn btn-sm btn-primary">Edit</a></td>';
                $html .= '<td class="text-center"><a href="' . htmlspecialchars($row['delete_link']) . '" class="btn btn-sm btn-danger">Delete</a></td>';
                $html .= '</tr>';
            }
        }

        $html .= '</tbody>';
        $html .= '</table>';

        return $html;
    }
}