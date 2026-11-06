<?php

return [
    'format' => [
        'date' => 'j M, Y',
        'time' => 'h:i a',
        'datetime' => 'j M, Y \a\t h:ia',
        'jscalendar' => [
            'date' => 'd/m/Y',
            'format_js' => 'dd/mm/yyyy',
            'format_moment' => 'DD/MM/YYYY',
            'format_php' => 'd/m/Y',
        ],
    ],
    'pagination' => [
        'perpage' => [
            'admin' => env('PAGINATION_PERPAGE_ADMIN', 100),
            'website' => env('PAGINATION_PERPAGE_WEBSITE', 25),
        ]
    ],
    'locale' => [
        'ar' => 'Arabic',
    ],
    'gacode' => env('GOOGLE_ANALYTICS_CODE', 'UA-83201567-1'),
    'app_start_date' => new DateTime(env('APP_START_DATE', 'now')),
    'use_email_queue' => env('USE_EMAIL_QUEUE', true),

    'voucher_types' => [
        'Payment' => 'Payment',
        'Receipt' => 'Receipt',
        'Journal' => 'Journal',
        'Contra' => 'Contra'
    ],
];
