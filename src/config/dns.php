<?php

return [
    'default' => env('DNS_DRIVER', 'cloudflare'),

    'drivers' => [
        'cloudflare' => [
            'driver' => 'cloudflare',
            'email' => env('CLOUDFLARE_EMAIL'),
            'key' => env('CLOUDFLARE_KEY'),
            'zone' => env('CLOUDFLARE_ZONE'),
        ],
        'route53' => [
            'driver' => 'route53',
            'key' => env('ROUTE53_KEY'),
            'secret' => env('ROUTE53_SECRET'),
            'zone' => env('ROUTE53_ZONE'),
        ],
    ],
];
