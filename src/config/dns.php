<?php

return [
    'default' => env('DNS_DRIVER', 'cloudflare'),

    'providers' => [
        'cloudflare' => [
            'driver' => 'cloudflare',
            'email' => env('CLOUDFLARE_EMAIL'),
            'key' => env('CLOUDFLARE_KEY'),
            'default_zone' => env('CLOUDFLARE_ZONE'),
        ],
        'route53' => [
            'driver' => 'route53',
            'key' => env('ROUTE53_KEY'),
            'secret' => env('ROUTE53_SECRET'),
            'default_zone' => env('ROUTE53_ZONE'),
        ],
    ],
];
