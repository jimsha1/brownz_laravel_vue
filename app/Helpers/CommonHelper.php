<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

if (! function_exists('whatsappGatewayRequest')) {


    function whatsappGatewayRequest(
        string $mobile,
        string $templateName,
        string $language = 'en',
        ?array $header = null,
        array $body = [],
        array $buttons = []
    ): array {
        $result = [
            'raw_response' => null,
            'api_response' => null,
            'message_status' => 'F',
            'message_id' => null,
            'error' => null,
        ];

        try {
            $components = [];

            // Body parameters
            if (! empty($body)) {
                $bodyParameters = array_map(
                    fn($value) => [
                        'type' => 'text',
                        'text' => (string) $value,
                    ],
                    $body
                );

                $components[] = [
                    'type' => 'body',
                    'parameters' => $bodyParameters,
                ];
            }

            // Header
            if (! empty($header)) {
                $headerType = $header['type'] ?? null;

                if ($headerType === 'image') {
                    $components[] = [
                        'type' => 'header',
                        'parameters' => [
                            [
                                'type' => 'image',
                                'image' => [
                                    'link' => $header['url'],
                                ],
                            ],
                        ],
                    ];
                } elseif ($headerType === 'video') {
                    $components[] = [
                        'type' => 'header',
                        'parameters' => [
                            [
                                'type' => 'video',
                                'video' => [
                                    'link' => $header['url'],
                                ],
                            ],
                        ],
                    ];
                } elseif ($headerType === 'document') {
                    $document = [
                        'link' => $header['url'],
                    ];

                    if (! empty($header['filename'])) {
                        $document['filename'] = $header['filename'];
                    }

                    $components[] = [
                        'type' => 'header',
                        'parameters' => [
                            [
                                'type' => 'document',
                                'document' => $document,
                            ],
                        ],
                    ];
                } elseif ($headerType === 'text') {
                    $components[] = [
                        'type' => 'header',
                        'parameters' => [
                            [
                                'type' => 'text',
                                'text' => (string) ($header['text'] ?? ''),
                            ],
                        ],
                    ];
                }
            }

            // Buttons
            if (! empty($buttons)) {
                foreach ($buttons as $button) {
                    $type = $button['type'] ?? null;
                    $index = $button['index'] ?? 0;
                    $value = $button['value'] ?? null;

                    if ($type === 'url' && $value !== null) {
                        $components[] = [
                            'type' => 'button',
                            'sub_type' => 'url',
                            'index' => (string) $index,
                            'parameters' => [
                                [
                                    'type' => 'text',
                                    'text' => (string) $value,
                                ],
                            ],
                        ];
                    }
                }
            }

            // WhatsApp API Payload
            $payload = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $mobile,
                'type' => 'template',
                'template' => [
                    'name' => $templateName,
                    'language' => ['code' => $language],
                    'components' => $components,
                ],
            ];

            // API URL
            $apiUrl = sprintf(
                'https://graph.facebook.com/%s/%s/messages',
                config('services.whatsapp.api_version', 'v23.0'),
                config('services.whatsapp.phone_number_id')
            );

            $httpResponse = Http::withToken(config('services.whatsapp.token'))
                ->timeout(15)
                ->post($apiUrl, $payload);

            $responseData = $httpResponse->json();
            $result['raw_response'] = $httpResponse->body();
            $result['api_response'] = $responseData;

            if ($httpResponse->successful() && ! empty($responseData['messages'][0]['id'])) {
                $result['message_status'] = 'S';
                $result['message_id'] = $responseData['messages'][0]['id'];
            } else {
                $result['message_status'] = 'F';
                Log::warning('WhatsApp Gateway Error Response', [
                    'mobile' => $mobile,
                    'response' => $responseData,
                ]);
            }
        } catch (Throwable $e) {
            Log::error('WhatsApp gateway request exception: ' . $e->getMessage(), [
                'mobile' => $mobile,
                'template' => $templateName,
            ]);
            $result['api_response'] = $e->getMessage();
        }

        return $result;
    }
}
