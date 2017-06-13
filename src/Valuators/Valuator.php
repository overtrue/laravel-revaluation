<?php

/*
 * This file is part of the overtrue/laravel-revaluationable.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Overtrue\LaravelRevaluation\Valuators;

use Overtrue\LaravelRevaluation\Revaluable;

/**
 * Class Valuator.
 */
class Valuator implements Revaluable
{
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function jsonSerialize()
    {
        return $this->getValue();
    }

    public function getValue()
    {
        return $this->value;
    }

    public function toDefaultFormat()
    {
        return $this->value;
    }

    public function __toString()
    {
        return $this->getValue();
    }

    public static function toStorableValue($value)
    {
        return $value;
    }
}
