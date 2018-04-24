<?php

namespace Tmd\LaravelSite\Libraries\Debug;

use Carbon\Carbon;
use Event;
use Illuminate\Cache\Events\CacheHit;
use Illuminate\Cache\Events\CacheMissed;
use Illuminate\Cache\Events\KeyForgotten;
use Illuminate\Cache\Events\KeyWritten;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Database\Events\QueryExecuted;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\BufferHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Tmd\LaravelSite\Libraries\Debug\Events\LocalCacheHit;
use Tmd\LaravelSite\Libraries\Debug\Events\LocalCacheMissed;
use Tmd\LaravelSite\Libraries\Debug\Events\LocalKeyWritten;

class QueryLogger
{
    public function __construct()
    {
        global $queryLogger;
        $queryLogger = new Logger('Queries');

        $rotatingFileHandler = new RotatingFileHandler(
            storage_path().'/logs/query.log',
            5,
            \Monolog\Logger::DEBUG,
            true,
            0777,
            true
        );

        // We write to a BufferHandler first as multiple requests at the same time will cause the logs
        // to be mixed together.
        $bufferHandler = new BufferHandler(
            $rotatingFileHandler
        );

        $lineFormatter = new LineFormatter("%message% %context% %extra%\n", null, true, true);
        $rotatingFileHandler->setFormatter($lineFormatter);

        $queryLogger->pushHandler($bufferHandler);

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
            CommandStarting::class,
            function (CommandStarting $event) use ($queryLogger) {
                $queryLogger->info(
                    "\n\n=======\n{$event->command}"
                    ."\n".(new Carbon())->toDateTimeString()
                    ."\n========="
                );
            }
        );

        Event::listen(
            CacheMissed::class,
            function (CacheMissed $event) use ($queryLogger) {
                if ($event->key === 'illuminate:queue:restart') {
                    return false;
                }

                return $queryLogger->info("cache.missed\t\t\t{$event->key}");
            }
        );

        Event::listen(
            CacheHit::class,
            function (CacheHit $event) use ($queryLogger) {
                if ($event->key === 'illuminate:queue:restart') {
                    return false;
                }

                $queryLogger->info("cache.hit\t\t\t{$event->key}");
            }
        );

        Event::listen(
            KeyWritten::class,
            function (KeyWritten $event) use ($queryLogger) {
                $queryLogger->info("cache.write\t\t\t{$event->key}");
            }
        );

        Event::listen(
            KeyForgotten::class,
            function (KeyForgotten $event) use ($queryLogger) {
                $queryLogger->info("cache.forget\t\t\t{$event->key}");
            }
        );

        Event::listen(
            QueryExecuted::class,
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
            LocalCacheMissed::class,
            function (LocalCacheMissed $event) use ($queryLogger) {
                $queryLogger->info("array-cache.missed\t{$event->key}");
            }
        );

        Event::listen(
            LocalCacheHit::class,
            function (LocalCacheHit $event) use ($queryLogger) {
                $queryLogger->info("array-cache.hit\t\t{$event->key}");
            }
        );

        Event::listen(
            LocalKeyWritten::class,
            function (LocalKeyWritten $event) use ($queryLogger) {
                $queryLogger->info("array-cache.write\t{$event->key}");
            }
        );

    }
}
