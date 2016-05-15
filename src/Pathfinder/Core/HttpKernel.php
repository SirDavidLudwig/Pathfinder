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
        $this->mOptions = $options;

        $this->mRouter = new Router();

        $this->_loadMiddleware();
        $this->_loadRoutes();
    }

    private function _loadMiddleware()
    {
        $this->mMiddleware = require $this->mOptions['middleware'];
    }

    private function _loadRoutes()
    {
        $router = $this->mRouter;

        require $this->mOptions['routes'];
    }

    public function handle(Request $request)
    {
        $closure = $this->resolveRoute($request);

        if ($closure)

            if (($result = $closure($request)) instanceof Response)

                return $result;

            else

            return new Response($result);

        else

            return new Response("404 Error: Page not found");
    }

    public function resolveRoute(Request $request)
    {
        $result = $this->mRouter->route($request->method(), $request->uri());

        if ($result)
        {
            $route = $result[0];
            $slugs  = $result[1];

            $controller = $this->resolveController($route, $slugs);
            $middleware = $this->resolveMiddleware($route, $controller);

            return $middleware;
        }

        return Null;
    }

    public function resolveController(Route $route, array $slugs)
    {
        $controller = $route->controller();

        $params = [];

        if (gettype($controller) == 'string')
        {
            if (strpos($controller, '@') > 0)
            {
                $parts = explode('@', $controller);

                $class  = $parts[0];
                $method = $parts[1];
            }
            else
            {
                $class  = $controller;
                $method = $route->method() == 'get' ? 'index' : $route->method();
            }

            $class    = new $class;
            $params[] = $class;
            
            $reflection = new \ReflectionMethod($class, $method);
        }
        else
        
            $reflection = new \ReflectionFunction($controller);
        

        $params[] = $this->resolveControllerParams($reflection, $slugs);

        return [$reflection, $params];
    }

    public function resolveControllerParams(\ReflectionFunctionAbstract $reflection, array $slugs)
    {
        $params = $reflection->getParameters();
        $count  = count($params);
        
        if (count($params) == 0 || count($slugs) == 0) return [];

        $slugsMatching = [];
        $slugsFloating = [];
        $slugCount     = count($slugs);

        for ($j = 0; $j < $slugCount && count($params) > 0 ; $j++)
        {
            $found = False;
            foreach ($params as $key => $param)
            {
                if ($param->getName() == $slugs[$j]['key'])
                {
                    $slugsMatching[$slugs[$j]['key']] = $slugs[$j]['value'];
                    unset($params[$key]);
                    $found = True;
                    break;
                }
            }

            if (!$found)

                $slugsFloating[] = $slugs[$j]['value'];
        }

        $i = 0;
        $values = [];

        foreach ($reflection->getParameters() as $param)
        {
            $name = $param->getName();

            if (isset($slugsMatching[$name]))
            {
                $values[] = $slugsMatching[$name];
                unset($slugsMatching[$name]);
            }
            else
                $values[] = @$slugsFloating[$i++] ?: Null;
        }

        return $values;
    }

    public function resolveMiddleware(Route $route, array $controller)
    {
        $middleware = $route->middleware();
        $classes = $this->mMiddleware;

        $c = function(Request $request) use ($controller) {
                return $controller[0]->invokeArgs(...$controller[1]);
             };

        for ($i = count($middleware) - 1; $i >= 0; $i--)
        {
            $c = function(Request $request) use ($i, $c, $middleware, $classes) {
                    $m = new $classes[$middleware[$i]];
                    return $m->handle($request, $c);
                 };
        }

        return $c;
    }
}
