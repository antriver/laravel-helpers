<?php

namespace Tmd\LaravelSite\Providers;

use Config;
use Laravel\Passport\Bridge\ClientRepository;
use Laravel\Passport\Bridge\RefreshTokenRepository;
use Laravel\Passport\Bridge\ScopeRepository;
use Laravel\Passport\Passport;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\PasswordGrant;
use Route;
use Tmd\LaravelSite\Http\Middleware\CamelCaseRequestToSnakeCase;
use Tmd\LaravelSite\Libraries\Laravel\Passport\PassportAccessTokenRepository;
use Tmd\LaravelSite\Libraries\Laravel\Passport\PassportRouteRegistrar;
use Tmd\LaravelSite\Libraries\Laravel\Passport\PassportUserRepository;

class PassportServiceProvider extends \Laravel\Passport\PassportServiceProvider
{
    public function __construct(\Illuminate\Contracts\Foundation\Application $app)
    {
        parent::__construct($app);

        $this->registerPassportRoutes();
    }

    /**
     * Make the authorization service instance.
     *
     * @return AuthorizationServer
     */
    public function makeAuthorizationServer()
    {
        // The AuthorizationServer passes this access token repository instance to grants added to it.
        return new AuthorizationServer(
            $this->app->make(ClientRepository::class),
            $this->app->make(PassportAccessTokenRepository::class),
            $this->app->make(ScopeRepository::class),
            'file://'.Passport::keyPath('oauth-private.key'),
            'file://'.Passport::keyPath('oauth-public.key')
        );
    }

    /**
     * Create and configure a Password grant instance.
     *
     * @return PasswordGrant
     */
    protected function makePasswordGrant()
    {
        // Using a custom user repository to authenticate with usernames instead of emails
        $grant = new PasswordGrant(
            $this->app->make(PassportUserRepository::class),
            $this->app->make(RefreshTokenRepository::class)
        );

        $grant->setRefreshTokenTTL(Passport::refreshTokensExpireIn());

        return $grant;
    }

    /**
     * @api            {post} /oauth/token Create An Access Token
     * @apiDescription Only applicable to first-party clients.
     * @apiGroup       Authentication
     *
     * @apiParam {string=password} grantType
     * @apiParam {number} clientId
     * @apiParam {string} clientSecret
     * @apiParam {string} username
     * @apiParam {string} password
     * @apiParam {string=*} scope
     *
     * @apiSuccessExample {json} Success
     * {
     * "tokenType": "Bearer",
     * "expiresIn": 3155673600,
     * "accessToken": "abdefghijklmno...",
     * "refreshToken": "zyxwvutsrqponm..."
     * }
     */
    private function registerPassportRoutes()
    {
        $callback = function ($router) {
            $router->all();
        };

        $options = [
            'domain' => Config::get('api.domain'),
            'prefix' => 'oauth',
            'middleware' => [
                'api',
                CamelCaseRequestToSnakeCase::class,
            ],
            'namespace' => '\Laravel\Passport\Http\Controllers',
        ];

        Route::group(
            $options,
            function ($router) use ($callback) {
                $callback(new PassportRouteRegistrar($router));
            }
        );
    }
}
