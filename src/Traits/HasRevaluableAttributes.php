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
     * Fetch attribute.
     *
     * @param string $method
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (!starts_with($method, 'get')) {
            return parent::__call($method, $args);
        }

        $attribute = substr($method, 3);

        if ($valuator = $this->getValuator($attribute)) {
            return $valuator;
        }

        return parent::__call($method, $args);
    }

    /**
     * Return valuator of attribute.
     *
     * @param  string $attribute
     *
     * @return Overtrue\LaravelRevaluation\Revaluable
     */
    public function getValuator($attribute)
    {
        $attribute = snake_case($attribute);

        if ($valuator = $this->getAttributeValuator($attribute)) {
            return new $valuator($this->{$attribute}, $attribute, $this);
        }

        return false;
    }

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
                $value = call_user_func([new $valuator($value), 'storeableValue'], $value);
            }
        }

        return parent::__set($attribute, $value);
    }

    /**
     * Return revaluable attributes.
     *
     * @example
     *
     * <pre>
     * // 1. Using default valuator:
     * protected $revaluable = [
     *     'foo', 'bar', 'baz'
     * ];
     *
     * // 2. Use the specified valuator:
     * protected $revaluable = [
     *     'foo' => '\Foo\Support\Valuator\Foo',
     *     'bar' => '\Foo\Support\Valuator\Bar',
     *     'baz',
     * ];
     * </pre>
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
