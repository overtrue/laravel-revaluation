<?php


namespace Overtrue\LaravelRevaluation\Tests;


/**
 * Class OrderWithToArrayFalse
 *
 * @author overtrue <i@overtrue.me>
 */
class OrderWithToArrayFalse extends Order
{
    protected $appendRevaluatedAttributesToArray = false;
}