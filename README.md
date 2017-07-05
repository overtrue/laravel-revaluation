# Laravel 5 model revaluation helper.



## Installation

You can install the package using composer

```sh
$ composer require overtrue/laravel-revaluation -vvv
```

Then add the service provider to `config/app.php`

```php
Overtrue\LaravelRevaluation\RevaluationServiceProvider::class,
```

Publish the config file:

```sh
$ php artisan vendor:publish --provider='Overtrue\LaravelRevaluation\RevaluationServiceProvider'
```

Finally, use `Overtrue\LaravelRevaluation\Traits\HasRevaluableAttributes` in model. And specify which attributes in the `$revaluable` property can be revalued:

```php
<?php

use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelRevaluation\Traits\HasRevaluableAttributes;

class Order extends Model
{
    use HasRevaluableAttributes;
    
    // 1. Use the default valuator.
    protected $revaluable = [
        'total', 'paid_in', 'postage',
    ];
    
    // 2. Use the specified valuator:
    // protected $revaluable = [
    //    'foo' => '\Foo\Support\Valuator\Foo',
    //    'bar' => '\Foo\Support\Valuator\Bar',
    //    'baz',  // default valuator
    //];

    //...
}
```

## Usage


### Basic usage with default options.

```php
$order = Order::find(1);

$order->total;                      // 345 (Db: 34500)
$order->raw_total;                   // 3450

$order->getRevaluatedTotalAttribute() or $order->revaluated_total; // Overtrue\LaravelRevaluation\Valuators\RmbCent
$order->revaluated_total->inYuan();       // 345.00
$order->revaluated_total->asCurrency();   // ￥345.00

// automatic setter.
$order->total = 123;
$order->save();

$order->total;                      // 123
$order->raw_total;                  // 12300
$order->revaluated_total->asCurrency();   // ￥123.00

// to array
$order->toArray();
//[
//    'total' => 12300,
//    'revaluated_total' => 123.0,
//]
```

### Custom revaluated attribute prefix

```php
protected $revaluatedAttributePrefix = 'display';

$order->total;                      // 123.0;
$order->raw_total;                  // 12300
$order->display_total->asCurrency();   // ￥123.00

// to array
$order->toArray();
//[
//    'total' => 12300,
//    'display_total' => 123.0,
//]
```

### Disable auto append revaluated attributes to array

```php
protected $appendRevaluatedAttributesToArray = false;

$order->total;                      // 123.0;
$order->raw_total;                  // 12300
$order->display_total->asCurrency();   // ￥123.00

// to array
$order->toArray();
//[
//    'total' => 12300,
//]
```

### Using revaluated value replace raw attributes value

```php
protected $replaceRawAttributesToArray = true;

$order->total;                      // 123.0;
$order->raw_total;                  // 12300
$order->display_total->asCurrency();   // ￥123.00

// to array
$order->toArray();
//[
//    'total' => 123.0,
//]
```

More usage examples, Please refer to [unit testing](https://github.com/overtrue/laravel-revaluation/tree/master/tests) 

## License

MIT