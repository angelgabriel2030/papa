<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DelayRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    private int $delayms = 100;
    public function handle(Request $request, Closure $next): Response
    {

        $key = 'game_request_lock';
        while(cache()->has($key)) {
            usleep(100000);
        }

        cache()->put($key, true, 2);

        usleep($this->delayms * 1000);
        $response = $next($request);
        cache()->forget($key);
        return $response;
    }
}