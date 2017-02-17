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

Finally, use HasRevaluableAttributes in model. And specify which attributes in the `$revaluable` property can be revalued:

```php
...
use Overtrue\LaravelRevaluation\Traits\HasRevaluableAttributes;
...

class Order extends Model
{
    use HasRevaluableAttributes;

    protected $revaluable = [
        'total', 'paid_in', 'postage',
    ];
}
```

## Usage

TODO