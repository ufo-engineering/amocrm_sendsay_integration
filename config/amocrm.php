<?php

/*
 * This file is part of Laravel AmoCrm.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | amoCRM Auth
    |--------------------------------------------------------------------------
    |
    */

    'domain' => env('AMO_DOMAIN', 'example'),
    'login' => env('AMO_LOGIN', 'login@example.com'),
    'hash' => env('AMO_HASH', 'd56b699830e77ba53855679cb1d252da'),

    /*
    |--------------------------------------------------------------------------
    | B2B Family Auth
    |--------------------------------------------------------------------------
    |
    */

    'b2bfamily' => [

        'appkey' => env('B2B_APPKEY'),
        'secret' => env('B2B_SECRET'),
        'email' => env('B2B_EMAIL'),
        'password' => env('B2B_PASSWORD'),

    ]

];
