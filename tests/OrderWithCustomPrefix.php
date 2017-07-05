<?php


namespace Overtrue\LaravelRevaluation\Tests;


/**
 * Class OrderWithCustomPrefix
 *
 * @author overtrue <i@overtrue.me>
 */
class OrderWithCustomPrefix extends Order
{
    protected $revaluatedAttributePrefix = 'display';
}