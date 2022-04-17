<?php

return [

    'categories' => [
        'buy_get',
        'discount',
        'bundle',
    ],

    'attrs' => [
        'buy_get' => [
            'int' => ['buy', 'get', 'discount', 'buy_discount'],
            'str' => ['discount_type'],
        ],
        'discount' => [
            'int' => ['discount'],
            'str' => [],
        ],
        'bundle' => [
            'int' => ['buy', 'price'],
            'str' => [],
        ],
    ],
];
