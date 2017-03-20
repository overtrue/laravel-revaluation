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

    protected $revaluable = [
        'total', 'paid_in', 'postage',
    ];

    //...
}
```

## Usage

```php
$order = Order::find(1);

$order->total;                      // 34500
$order->getTotal()                  // Overtrue\LaravelRevaluation\Valuators\RmbCent
$order->getTotal()->inYuan();       // 345.00
$order->getTotal()->asCurrency();   // ￥345.00

// automatic setter.
$order->total = 123;
$order->save();

$order->total;                      // 12300
$order->getTotal()->asCurrency();   // ￥123.00
```

## License

MIT