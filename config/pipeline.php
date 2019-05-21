<?php

return [
	'events_per_job' => env('EVENTS_PER_JOB', 1000),
    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_resets',
            'expire' => 60,
        ],
    ],

];
