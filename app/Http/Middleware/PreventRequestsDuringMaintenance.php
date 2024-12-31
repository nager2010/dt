<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;

class PreventRequestsDuringMaintenance extends Middleware
{
    /**
     * The URIs that should be reachable while maintenance mode is enabled.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Check if the response content is a string and properly encoded
        if (is_string($response->getContent()) && !mb_check_encoding($response->getContent(), 'UTF-8')) {
            $response->setContent(utf8_encode($response->getContent()));
        }

        // Set the content type with charset
        $response->header('Content-Type', 'text/html; charset=UTF-8');

        return $response;
    }
}
