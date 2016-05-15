<?php

namespace Pathfinder\Core;

use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionFunctionAbstract;

use Pathfinder\Http\Request;
use Pathfinder\Http\Response;
use Pathfinder\Routing\Route;
use Pathfinder\Routing\Router;

class HttpKernel
{
    private $mOptions;
    private $mRouter;
    private $mTypehints;

    public function __construct(array $options)
    {
        $this->mOptions = $options;

        $this->mRouter = new Router();

        $this->_loadMiddleware();
        $this->_loadRoutes();
        $this->_loadTypehints();
    }

    private function _loadMiddleware()
    {
        if (isset($this->mOptions['middleware']))
            
            $this->mMiddleware = require $this->mOptions['middleware'];
    }

    private function _loadRoutes()
    {
        if (isset($this->mOptions['routes']))
        {
            $router = $this->mRouter;

            require $this->mOptions['routes'];
        }
    }

    private function _loadTypehints()
    {
        $this->addTypehint($this->mRouter);

        if (isset($this->mOptions['typehints']))
        
            foreach ($this->mOptions['typehints'] as $object)
            
                $this->addTypehint($object);
        
    }

    public function addTypehint($object)
    {
        $this->mTypehints[get_class($object)] = $object;
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

            $controller = $this->resolveController($route);
            $middleware = $this->resolveMiddleware($route, $controller, $slugs);

            return $middleware;
        }

        return Null;
    }

    public function resolveController(Route $route)
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
            
            $reflection = new ReflectionMethod($class, $method);
        }
        else
        
            $reflection = new ReflectionFunction($controller);
        

        return [$reflection, $params];
    }

    public function resolveMiddleware(Route $route, array $controller, array $slugs)
    {
        $middleware = $route->middleware();
        $classes    = $this->mMiddleware;
        $kernel     = $this;

        $c = function(Request $request) use ($kernel, $controller, $slugs) {
                $controller[1][] = $kernel->resolveParameters($controller[0], $slugs, [$request]);

                return $controller[0]->invokeArgs(...$controller[1]);
             };

        for ($i = count($middleware) - 1; $i >= 0; $i--)
        {
            $c = function(Request $request) use ($i, $c, $kernel, $middleware, $classes) {
                    $m = new $classes[$middleware[$i]];

                    $method = new ReflectionMethod($m, 'handle');
                    $params = $kernel->resolveParameters($method, [], [$request, $c]);

                    return $m->handle(...$params);
                 };
        }

        return $c;
    }

    public function resolveParameters(ReflectionFunctionAbstract $reflection, array $slugs = [], array $typehintObjects = [])
    {

        $params = $reflection->getParameters();
        $count  = count($params);
        
        if (count($params) == 0 && count($slugs) == 0) return [];

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

        $typehints = [];

        
        foreach ($typehintObjects as $typehint)

            $typehints[get_class($typehint)] = $typehint;


        foreach ($reflection->getParameters() as $param)
        {
            $class = $param->getClass();
            $name = $param->getName();

            if ($class && isset($typehints[$class->name]))
            {
                $values[] = $typehints[$class->name];
            }
            elseif ($class && isset($this->mTypehints[$class->name]))
            {
                $values[] = $this->mTypehints[$class->name];
            }
            elseif (isset($slugsMatching[$name]))
            {
                $values[] = $slugsMatching[$name];
                unset($slugsMatching[$name]);
            }
            else
                $values[] = @$slugsFloating[$i++] ?: Null;
        }

        return $values;
    }
}
