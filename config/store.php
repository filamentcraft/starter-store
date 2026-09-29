<?php

declare(strict_types=1);

return [

    'currency' => [
        'code' => env('STORE_CURRENCY', 'GBP'),
        'symbol' => env('STORE_CURRENCY_SYMBOL', '£'),
        'position' => env('STORE_CURRENCY_POSITION', 'before'),
    ],

    'order_prefix' => env('STORE_ORDER_PREFIX', 'KS'),

];
