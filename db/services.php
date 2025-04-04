<?php

$functions = [
    'mod_punchclock_log_punch' => [
        'classname'   => 'mod_punchclock\external\log_punch',
        'methodname'  => 'execute',
        'description' => 'Logs the punch time for a student',
        'type'        => 'write',
        'ajax'        => true,
        'capabilities' => ''
    ]
];