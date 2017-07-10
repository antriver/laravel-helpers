<?php

namespace Tmd\LaravelSite\Providers;

use Config;
use DB;
use Illuminate\Support\ServiceProvider;
use PDO;
use Tmd\LaravelSite\Libraries\Debug\QueryLogger;
use Validator;

class LaravelSiteServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

        include_once dirname(__DIR__).'/Libraries/helpers.php';

        $this->registerQueryLogger();
        DB::connection()->getPdo()->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    private function registerQueryLogger()
    {
        if (Config::get('app.log_queries')) {
            new QueryLogger();
        }
    }
}
