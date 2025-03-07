<?php

/**
 * A Moodle plugin to keep track of attendance sessions in a flexible manner.
 *
 * @package    mod_punchclock
 * @copyright  2025 onwards Basile Leroy {@link https://basileleroy.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2025030700;
$plugin->requires  = 2024100100;
$plugin->component = 'mod_punchclock';
$plugin->maturity  = MATURITY_ALPHA; // Or MATURITY_STABLE when ready.
$plugin->release   = 'v1.0-alpha'; // Human-readable version.