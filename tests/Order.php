<?php

namespace Overtrue\LaravelRevaluation\Tests;

use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelRevaluation\Traits\HasRevaluableAttributes;

/**
 * Class Order
 *
 * @author overtrue <i@overtrue.me>
 */
class Order extends Model
{
    use HasRevaluableAttributes;

    protected $table = 'orders';

    protected $fillable = [
        'title', 'total', 'postage', 'paid_in',
    ];

    protected $revaluable = [
        'total', 'postage', 'paid_in',
    ];
}