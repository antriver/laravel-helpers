<?php

namespace Tmd\LaravelSite\Libraries\Debug;

use Carbon\Carbon;
use Event;
use Illuminate\Cache\Events\CacheHit;
use Illuminate\Cache\Events\CacheMissed;
use Illuminate\Cache\Events\KeyForgotten;
use Illuminate\Cache\Events\KeyWritten;
use Illuminate\Database\Events\QueryExecuted;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Tmd\LaravelSite\Libraries\Date\DateFormat;
use Tmd\LaravelSite\Libraries\Debug\Events\LocalCacheHit;
use Tmd\LaravelSite\Libraries\Debug\Events\LocalCacheMissed;
use Tmd\LaravelSite\Libraries\Debug\Events\LocalKeyWritten;

class QueryLogger
{
    public function __construct()
    {
        global $queryLogger;
        $queryLogger = new Logger('Queries');

        $fileHandler = new RotatingFileHandler(
            storage_path().'/logs/query.log',
            5,
            \Monolog\Logger::DEBUG,
            true,
            0777
        );

        $lineFormatter = new LineFormatter("%message% %context% %extra%\n", null, true, true);
        $fileHandler->setFormatter($lineFormatter);

        $queryLogger->pushHandler($fileHandler);

        //$queryLogger->pushHandler(new StreamHandler("php://output"));

        if (php_sapi_name() !== 'cli') {
            $queryLogger->info(
                "\n\n=======\n{$_SERVER['REQUEST_METHOD']}\n{$_SERVER['REQUEST_URI']}"
                //." \n".Request::server('HTTP_REFERER')
                ."\n".(new Carbon())->toDateTimeString()
                ."\n========="
            );
        }

        Event::listen(
            'Illuminate\Cache\Events\CacheMissed',
            function (CacheMissed $event) use ($queryLogger) {
                if ($event->key === 'illuminate:queue:restart') {
                    return false;
                }

                return $queryLogger->info("cache.missed\t\t\t{$event->key}");
            }
        );

        Event::listen(
            'Illuminate\Cache\Events\CacheHit',
            function (CacheHit $event) use ($queryLogger) {
                $queryLogger->info("cache.hit\t\t\t{$event->key}");
            }
        );

        Event::listen(
            'Illuminate\Cache\Events\KeyWritten',
            function (KeyWritten $event) use ($queryLogger) {
                $queryLogger->info("cache.write\t\t\t{$event->key}");
            }
        );

        Event::listen(
            'Illuminate\Cache\Events\KeyForgotten',
            function (KeyForgotten $event) use ($queryLogger) {
                $queryLogger->info("cache.forget\t\t\t{$event->key}");
            }
        );

        Event::listen(
            'Illuminate\Database\Events\QueryExecuted',
            function (QueryExecuted $event) use ($queryLogger) {

                $query = $event->sql;
                $bindings = $event->bindings;

                // Format binding data for sql insertion
                foreach ($bindings as $i => $binding) {
                    if ($binding instanceof \DateTime) {
                        $bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
                    } else {
                        if (is_string($binding)) {
                            $bindings[$i] = "'$binding'";
                        }
                    }
                }

                // Insert bindings into query
                $query = str_replace(['%', '?'], ['%%', '%s'], $query);
                $query = vsprintf($query, $bindings);

                $queryLogger->info("query\t\t{$query}", [$event->time]);
            }
        );


        Event::listen(
            'Tmd\LaravelSite\Libraries\Debug\Events\LocalCacheMissed',
            function (LocalCacheMissed $event) use ($queryLogger) {
                $queryLogger->info("array-cache.missed\t{$event->key}");
            }
        );

        Event::listen(
            'Tmd\LaravelSite\Libraries\Debug\Events\LocalCacheHit',
            function (LocalCacheHit $event) use ($queryLogger) {
                $queryLogger->info("array-cache.hit\t\t{$event->key}");
            }
        );

        Event::listen(
            'Tmd\LaravelSite\Libraries\Debug\Events\LocalKeyWritten',
            function (LocalKeyWritten $event) use ($queryLogger) {
                $queryLogger->info("array-cache.write\t{$event->key}");
            }
        );

    }
}
