<?php

/*
 * This file is part of the overtrue/laravel-revaluationable.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Overtrue\LaravelRevaluation;

use JsonSerializable;

/**
 * Interface Revaluable.
 */
interface Revaluable extends JsonSerializable
{
    /**
     * Translate to the default format.
     *
     * @return mixed
     */
    public function toDefaultFormat();

    /**
     * Get raw value
     *
     * @return mixed
     */
    public function getRaw();

    /**
     * Return storable value.
     *
     * @param  mixed $value
     *
     * @return mixed
     */
    public static function toStorableValue($value);
}
