<?php

namespace Pathfinder\Core;

use Pathfinder\Http\Request;
use Pathfinder\Http\Response;
use Pathfinder\Routing\Route;
use Pathfinder\Routing\Router;

class HttpKernel
{
    public function __construct(array $options)
    {
        $this->_options = $options;

        $this->_router = new Router();

        $this->_loadRoutes();
    }

    public function handle(Request $request)
    {
        echo $request->method();
        
        return new Response();
    }

    private function _loadRoutes()
    {
        $router = $this->_router;

        require $this->_options['routes'];
    }
}
