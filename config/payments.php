<?php

return [
    'driver' => env('PAYMENTS_DRIVER', 'simulated'),
    'simulated' => [
        'brands' => [
            'visa' => [
                '4242424242424242',
                '4000000000000002',
                '4000000000009995',
                '4000000000000069',
                '4000000000000127',
            ],
            'mastercard' => [
                '5555555555554444',
            ],
        ],
    ],
];
