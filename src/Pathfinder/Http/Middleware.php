<?php

namespace Pathfinder\Http;

use Pathfinder\Http\Request;
use Closure;

class Middleware
{
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }
}
