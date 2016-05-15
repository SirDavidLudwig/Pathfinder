<?php

namespace Pathfinder\Testing;

use Closure;
use Pathfinder\Http\Middleware;
use Pathfinder\Http\Request;

class AuthMiddleware extends Middleware
{
	public function handle(Request $request, Closure $next)
	{
		return $next($request);
	}
}