<?php


namespace Overtrue\LaravelRevaluation\Tests;


/**
 * Class OrderWithReplaceToArray
 *
 * @author overtrue <i@overtrue.me>
 */
class OrderWithReplaceToArray extends Order
{
    protected $appendRevaluatedAttributesToArray = true;
    protected $replaceRawAttributesToArray = true;
}