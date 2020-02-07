<?php

/*
 * This file is part of the overtrue/laravel-revaluation.
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
     * Revaluated attributes append to array.
     *
     * @var bool
     */
    //protected $appendRevaluatedAttributesToArray = true;

    /**
     * @var bool
     */
    //protected $replaceRawAttributesToArray = false;

    /**
     * Prefix of revaluated attribute getter.
     *
     * <pre>
     *      $model->revaluated_price;
     * </pre>
     *
     * @var string
     */
    //protected $revaluatedAttributePrefix = 'revaluated';

    /**
     * Return valuator instance of attribute.
     *
     * @param string $attribute
     *
     * @return \Overtrue\LaravelRevaluation\Revaluable
     */
    public function getRevaluatedAttribute($attribute)
    {
        $attribute = \Illuminate\Support\Str::snake($attribute);

        if ($valuator = $this->getAttributeValuator($attribute)) {
            return new $valuator(parent::getAttribute($attribute), $attribute, $this);
        }

        return false;
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
            if (is_int($key)) {
                $revaluable[$valuator] = config('revaluation.default_valuator');
            } else {
                $revaluable[$key] = $valuator;
            }
        }

        return $revaluable;
    }

    /**
     * @return string
     */
    public function getRevaluableAttributePrefix()
    {
        return rtrim($this->revaluatedAttributePrefix ?? 'revaluated', '_');
    }

    /**
     * @example
     * <pre>
     * $object->revaluated_price;
     * $object->raw_price;
     * </pre>
     *
     * @param string $attribute
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function getAttribute($attribute)
    {
        if ($this->hasGetMutator($attribute)) {
            return parent::getAttribute($attribute);
        }

        if (\Illuminate\Support\Str::startsWith($attribute, 'raw_')) {
            return $this->getRevaluatedAttribute(substr($attribute, strlen('raw_')))->getRaw();
        }

        $prefix = $this->getRevaluableAttributePrefix();
        if (\Illuminate\Support\Str::startsWith($attribute, $prefix)) {
            return $this->getRevaluatedAttribute(substr($attribute, strlen($prefix) + 1));
        }

        if ($valuator = $this->getRevaluatedAttribute($attribute)) {
            return $valuator->toDefaultFormat();
        }

        return parent::getAttribute($attribute);
    }

    /**
     * Set attribute.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return $this
     */
    public function setAttribute($attribute, $value)
    {
        if ($valuator = $this->getAttributeValuator($attribute)) {
            $value = forward_static_call([$valuator, 'toStorableValue'], $value);
        }

        return parent::setAttribute($attribute, $value);
    }

    /**
     * Run the increment or decrement method on the model.
     *
     * @param string $column
     * @param int    $amount
     * @param array  $extra
     * @param string $method
     *
     * @return int
     */
    protected function incrementOrDecrement($column, $amount, $extra, $method)
    {
        $query = $this->newQuery();

        if (!$this->exists) {
            return $query->{$method}($column, $amount, $extra);
        }

        $this->incrementOrDecrementAttributeValue($column, $amount, $extra, $method);

        // ***[ fix increment/decrement bug]***
        if ($valuator = $this->getAttributeValuator($column)) {
            $amount = forward_static_call([$valuator, 'toStorableValue'], $amount);
        }

        return $query->where(
            $this->getKeyName(), $this->getKey()
        )->{$method}($column, $amount, $extra);
    }

    /**
     * Override HasAttributes::attributesToArray.
     *
     * @return array
     */
    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();

        if ($this->shouldAppendRevaluatedAttributesToArray()) {
            foreach (array_keys($this->getRevaluableAttributes()) as $attribute) {
                if ($valuator = $this->getRevaluatedAttribute($attribute)) {
                    $attribute = $this->shouldReplaceRawAttributesToArray() ? $attribute : $this->getRevaluablePrefixedAttributeName($attribute);
                    $attributes[$attribute] = $valuator->toDefaultFormat();
                }
            }
        }

        return $attributes;
    }

    /**
     * @param string $attribute
     *
     * @return string
     */
    public function getRevaluablePrefixedAttributeName($attribute)
    {
        return $this->getRevaluableAttributePrefix().'_'.$attribute;
    }

    /**
     * Fetch attribute.
     *
     * @example
     * <pre>
     * $object->getRevaluatedPriceAttribute();
     * $object->getRevaluatedXXXAttribute();
     * </pre>
     *
     * @param string $method
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        $prefix = \Illuminate\Support\Str::studly($this->getRevaluableAttributePrefix());
        if (preg_match("/get{$prefix}(?<attribute>\\w+)Attribute/i", $method, $matches)) {
            return $this->getRevaluatedAttribute($matches['attribute']);
        }

        return parent::__call($method, $args);
    }

    /**
     * @return bool
     */
    protected function shouldAppendRevaluatedAttributesToArray()
    {
        return property_exists($this, 'appendRevaluatedAttributesToArray') ? $this->appendRevaluatedAttributesToArray : true;
    }

    /**
     * @return bool
     */
    protected function shouldReplaceRawAttributesToArray()
    {
        return property_exists($this, 'replaceRawAttributesToArray') ? $this->replaceRawAttributesToArray : true;
    }

    /**
     * Return revaluated value of attribute.
     *
     * @param string $attribute
     *
     * @return mixed
     */
    protected function getStorableValue($attribute)
    {
        if ($valuator = $this->getAttributeValuator($attribute)) {
            if (is_callable($valuator, 'toStorableValue')) {
                $value = forward_static_call([$valuator, 'toStorableValue'], $this->attributes[$attribute]);
            }
        }

        return $value;
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
        return \Illuminate\Support\Arr::get($this->getRevaluableAttributes(), $attribute);
    }
}
