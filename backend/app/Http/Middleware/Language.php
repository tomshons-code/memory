<?php

namespace App\Http\Middleware;

use Closure;

class Language
{

	public function handle($request, Closure $next)
	{
		if (isset($request->language) && in_array($request->language, array('pl', 'en')))
        	app()->setLocale($request->language);
        return $next($request);
	}
}
