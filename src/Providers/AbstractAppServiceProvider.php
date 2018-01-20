<?php

namespace Tmd\LaravelSite\Providers;

use Illuminate\Support\ServiceProvider;

abstract class AbstractAppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        include_once dirname(__DIR__).'/Libraries/helpers.php';
    }
}
