<?php

/*
 * This file is part of the overtrue/laravel-revaluationable.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Overtrue\LaravelRevaluation\Traits;

/**
 * Trait HasRevaluableAttributes.
 */
trait HasRevaluableAttributes
{
    /**
     * Attribute mutator.
     *
     * @param string $attribute
     * @param mixed  $value
     */
    public function __set($attribute, $value)
    {
        if ($valuator = $this->getAttributeValuator($attribute)) {
            if (is_callable($valuator, 'storeableValue')) {
                $value = call_user_func([new $valuator(), 'storeableValue'], $value);
            }
        }

        return parent::__set($attribute, $value);
    }

    /**
     * Fetch attribute.
     *
     * @param string $attribute
     *
     * @return mixed
     */
    public function __get($attribute)
    {
        $value = parent::__get($attribute);

        if ($valuator = $this->getAttributeValuator($attribute)) {
            return new $valuator($value, $attribute, $this);
        }

        return $value;
    }

    /**
     * Return revaluable attributes.
     *
     * @return array
     */
    public function getRevaluableAttributes()
    {
        if (!property_exists($this, 'revaluable') || !is_array($this->revaluable)) {
            return [];
        }

        $revaluable = [];

        foreach ($this->revaluable as $key => $valuator) {
            if (is_integer($key)) {
                $revaluable[$valuator] = config('revaluation.default_valuator');
            } else {
                $revaluable[$valuator] = $valuator;
            }
        }

        return $revaluable;
    }

    /**
     * Wether the given attribute is revaluable.
     *
     * @param string $attribute
     *
     * @return bool
     */
    protected function isRevaluableAttribute($attribute)
    {
        return in_array($attribute, $this->getRevaluableAttributes(), true);
    }

    /**
     * Get attribute valuator.
     *
     * @param string $attribute
     *
     * @return string
     */
    protected function getAttributeValuator($attribute)
    {
        return array_get($this->getRevaluableAttributes(), $attribute);
    }
}
