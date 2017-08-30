<?php

/*
 * This file is part of the overtrue/laravel-revaluation.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

return [
    'default_valuator' => Overtrue\LaravelRevaluation\Valuators\Valuator::class,

    'options' => [
        'rmb' => [
            'pricision' => 2,
            'currency_format' => '￥%i',
        ],
    ],
];
