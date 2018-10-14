<?php

namespace Tmd\LaravelHelpers\Http\Middleware;

use Closure;
use Illuminate\Routing\Router;

/**
 * Custom version of SubstituteBindings so we only bind things we explicitly told it to.
 *
 * @package Illuminate\Routing\Middleware
 */
class SubstituteBindings
{
    /**
     * The router instance.
     *
     * @var Router
     */
    protected $router;

    /**
     * Create a new bindings substitutor.
     *
     * @param  Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $route = $request->route();

        $this->router->substituteBindings($route);

        //$this->router->substituteImplicitBindings($route);

        return $next($request);
    }
}
