<?php

$routesJson = file_get_contents(__DIR__ . '/routes.json');
$routesJson = mb_convert_encoding($routesJson, 'UTF-8', 'UTF-16LE');
$routes = json_decode($routesJson, true);

$collection = [
    'info' => [
        'name' => 'Nour Academy API',
        'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json'
    ],
    'item' => []
];

$adminFolder = [
    'name' => 'Admin API',
    'item' => []
];

$websiteFolder = [
    'name' => 'Website API',
    'item' => []
];

$otherFolder = [
    'name' => 'Other',
    'item' => []
];

foreach ($routes as $route) {
    $uri = $route['uri'];
    if (strpos($uri, 'api/') !== 0) {
        continue; // Only API routes
    }
    
    $methods = explode('|', $route['method']);
    $method = in_array('POST', $methods) ? 'POST' : (in_array('PUT', $methods) ? 'PUT' : (in_array('DELETE', $methods) ? 'DELETE' : 'GET'));

    $item = [
        'name' => $route['name'] ?? $uri,
        'request' => [
            'method' => $method,
            'header' => [
                ['key' => 'Accept', 'value' => 'application/json']
            ],
            'url' => [
                'raw' => '{{base_url}}/' . $uri,
                'host' => ['{{base_url}}'],
                'path' => explode('/', $uri)
            ]
        ]
    ];

    if ($method === 'POST' || $method === 'PUT') {
        $item['request']['body'] = [
            'mode' => 'raw',
            'raw' => "{}",
            'options' => ['raw' => ['language' => 'json']]
        ];
    }

    if (in_array('Illuminate\Auth\Middleware\Authenticate:sanctum', $route['middleware'])) {
        $item['request']['auth'] = [
            'type' => 'bearer',
            'bearer' => [
                ['key' => 'token', 'value' => '{{token}}', 'type' => 'string']
            ]
        ];
    }

    if (strpos($uri, 'api/admin') === 0) {
        $adminFolder['item'][] = $item;
    } elseif (strpos($uri, 'api/website') === 0) {
        $websiteFolder['item'][] = $item;
    } else {
        $otherFolder['item'][] = $item;
    }
}

if (!empty($adminFolder['item'])) $collection['item'][] = $adminFolder;
if (!empty($websiteFolder['item'])) $collection['item'][] = $websiteFolder;
if (!empty($otherFolder['item'])) $collection['item'][] = $otherFolder;

file_put_contents(__DIR__ . '/Nour_Academy.postman_collection.json', json_encode($collection, JSON_PRETTY_PRINT));
echo "Postman collection generated successfully.\n";
