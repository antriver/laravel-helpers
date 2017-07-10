<?php

namespace Tmd\LaravelSite\Http\Middleware;

use Cache;
use Closure;
use Exception;

class CloudFlareProxies
{
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
        $proxyIps = Cache::remember(
            'cloudFlareProxyIps',
            1440,
            function () {
                $url = 'https://www.cloudflare.com/ips-v4';
                try {
                    $ips = file_get_contents($url);
                    $ips = explode("\n", $ips);
                } catch (Exception $e) {
                    $ips = [];
                }

                return array_filter($ips);
            }
        );

        $request->setTrustedProxies($proxyIps);

        return $next($request);
    }
}
