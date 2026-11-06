<?php

return [
	'whatsapp' => [
		'access_token' => env('WHATSAPP_ACCESS_TOKEN'),
		'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
		'graph_version' => env('WHATSAPP_GRAPH_VERSION', 'v23.0'),
		'invoice_template' => env('WHATSAPP_INVOICE_TEMPLATE'),
		'template_language' => env('WHATSAPP_TEMPLATE_LANGUAGE', 'en_US'),
	],
];
