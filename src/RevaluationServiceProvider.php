<?php

/*
 * This file is part of the overtrue/laravel-revaluationable.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Overtrue\LaravelRevaluation;

use Illuminate\Support\ServiceProvider;

class RevaluationServiceProvider extends ServiceProvider
{
    /**
     * Application bootstrap event.
     */
    public function boot()
    {
        $this->publishes([__DIR__.'/config.php' => config_path('/revaluation.php')]);
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        setlocale(LC_MONETARY, config('app.locale'));
    }
}
