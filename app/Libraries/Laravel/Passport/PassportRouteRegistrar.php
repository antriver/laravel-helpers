<?php

namespace Tmd\LaravelSite\Libraries\Laravel\Passport;

use Illuminate\Routing\Router;

class PassportRouteRegistrar extends \Laravel\Passport\RouteRegistrar
{
    /**
     * Register the routes needed for managing personal access tokens.
     *
     * @return void
     */
    public function forPersonalAccessTokens()
    {
        $this->router->group(
            ['middleware' => ['auth:api,web']],
            function (Router $router) {
                $router->get(
                    '/oauth/scopes',
                    [
                        'uses' => 'ScopeController@all',
                    ]
                );

                $router->get(
                    '/oauth/personal-access-tokens',
                    [
                        'uses' => 'PersonalAccessTokenController@forUser',
                    ]
                );

                $router->post(
                    '/oauth/personal-access-tokens',
                    [
                        'uses' => 'PersonalAccessTokenController@store',
                    ]
                );

                $router->delete(
                    '/oauth/personal-access-tokens/{token_id}',
                    [
                        'uses' => 'PersonalAccessTokenController@destroy',
                    ]
                );
            }
        );
    }
}
