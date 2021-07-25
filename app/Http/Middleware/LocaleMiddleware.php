<?php

namespace App\Http\Middleware;

use Closure;

class LocaleMiddleware
{

    protected $default = "ar";

    public function handle($request, Closure $next)
    {

        $locale = $this->default;

        $header_locale = request()->header("Api-Lang");

        if ($header_locale) {
            $locale = $header_locale;
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
