<?php

return [
	'mode'                  => 'utf-8',
	'format'                => 'A4',
	'author'                => env('APP_NAME', 'Larammerce'),
	'subject'               => '',
	'keywords'              => '',
	'creator'               => 'Larammerce',
	'display_mode'          => 'fullpage',
	'tempDir'               => storage_path('tmp'),
    'font_path' => resource_path('fonts/'),
    'font_data' => [
        'iransans' => [
            'R'  => 'IRANSansWeb.ttf',
            'B'  => 'IRANSansWeb_Bold.ttf',
            'I'  => 'IRANSansWeb.ttf',
            'BI' => 'IRANSansWeb_Medium.ttf',
            'useOTL' => 0xFF
        ]
    ]
];
