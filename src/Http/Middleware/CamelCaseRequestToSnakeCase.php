<?php

namespace Tmd\LaravelSite\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Tmd\LaravelSite\Libraries\Traits\ConvertsCaseTrait;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to convert incoming request parameters form camelCase keys to snake_case keys.
 * And convert keys in the response from snake_case to camelCase.
 *
 * @package Tmd\LaravelSite\Http\Middleware
 */
class CamelCaseRequestToSnakeCase
{
    use ConvertsCaseTrait;

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
        // Convert request data from camelCase to snake_case (only top level keys!)
        $input = $request->all();
        $snakeInput = [];
        foreach ($input as $key => $value) {
            $snakeInput[snake_case($key)] = $value;
        }
        $request->replace($snakeInput);

        $response = $next($request);

        // Convert response data from snake_case to camelCase (only top level keys!)
        if ($response instanceof JsonResponse) {
            $response->setData($this->snakeToCamel($response->getData()));
        } elseif ($response instanceof Response) {
            $data = json_decode($response->getContent());
            $response->setContent(json_encode($this->snakeToCamel($data)));
        }

        return $response;
    }
}
