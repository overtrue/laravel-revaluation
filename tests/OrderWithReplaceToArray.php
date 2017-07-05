<?php

/*
 * This file is part of the overtrue/laravel-revaluation.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Overtrue\LaravelRevaluation\Tests;

/**
 * Class OrderWithReplaceToArray.
 *
 * @author overtrue <i@overtrue.me>
 */
class OrderWithReplaceToArray extends Order
{
    protected $appendRevaluatedAttributesToArray = true;
    protected $replaceRawAttributesToArray = true;
}
