<?php

return [
    [
        'method'    => 'get',
        'path'      => 'test',
        'class'     => '\Alto\Slimbxapi\Controllers\TestController',
        'function'  => 'hello'
    ],
    [
        'method'    => 'get',
        'path'      => 'test/authuser',
        'class'     => '\Alto\Slimbxapi\Controllers\TestController',
        'function'  => 'getAuthUser',
        'middlewares'   => [
            'initApiAuthMiddleware'
        ],
    ],
];
