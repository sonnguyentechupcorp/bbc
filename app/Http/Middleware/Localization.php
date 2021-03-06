<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        //Check header request and set language defaut
        $lang = ($request->hasHeader('lang')) ? $request->header('lang') : 'en';

        //Set laravel localization
        app()->setLocale($lang);

        return $next($request);
    }
}
