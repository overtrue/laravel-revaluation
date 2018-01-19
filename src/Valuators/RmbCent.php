<?php

/*
 * This file is part of the overtrue/laravel-revaluation.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Overtrue\LaravelRevaluation\Valuators;

/**
 * Class RmbCent.
 */
class RmbCent extends Valuator
{
    public function toDefaultFormat()
    {
        return $this->inYuan();
    }

    public function inYuan()
    {
        return round($this->value / 100, config('revaluation.options.rmb.pricision'));
    }

    public function asCurrency($format = null)
    {
        return money_format($format ?? config('revaluation.options.rmb.currency_format'), abs($this->inYuan()));
    }

    public static function toStorableValue($value)
    {
        return $value === null ? null : $value * 100;
    }
}
