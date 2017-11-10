<?php

namespace Tmd\LaravelSite\Providers;

use Auth;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Tmd\LaravelSite\Repositories\UserRepository;
use Tmd\LaravelPasswordUpdater\PasswordHasher;
use Tmd\LaravelSite\Libraries\Laravel\Auth\CachedEloquentUserProvider;
use Tmd\LaravelSite\Libraries\Laravel\Passport\PassportAccessTokenRepository;
use Tmd\LaravelSite\Libraries\Laravel\Passport\PassportTokenAuthGuard;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application authentication / authorization services.
     */
    public function boot()
    {
        Auth::provider(
            'cached-eloquent',
            function ($app) {
                return new CachedEloquentUserProvider(
                    $this->getUserRepository(),
                    app(PasswordHasher::class)
                );
            }
        );

        Auth::extend(
            'passport-token',
            function ($app, $name, array $config) {
                $guard = new PassportTokenAuthGuard(
                    app('auth')->createUserProvider($config['provider']),
                    app('request'),
                    app(PassportAccessTokenRepository::class)
                );

                /** @var \Illuminate\Container\Container $app */
                $app->refresh('request', $guard, 'setRequest');

                return $guard;
            }
        );

        $this->registerPolicies();
    }

    protected function getUserRepository()
    {
        return app(UserRepository::class);
    }
}
