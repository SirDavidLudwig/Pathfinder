<?php

namespace Pathfinder\Routing;


class Router
{
    protected $routes = [
        'get'    => [],
        'post'   => [],
        'put'    => [],
        'patch'  => [],
        'delete' => [],
    ];
    
    public function __construct()
    {

    }

    public function add(Route $route)
    {
        $this->routes[$route->method()] = $route;
    }

    public function get(...$args)
    {
        $this->add(new Route('get', ...$args));
    }

    public function post(...$args)
    {
        $this->add(new Route('post', ...$args));
    }

    public function put(...$args)
    {
        $this->add(new Route('put', ...$args));
    }

    public function patch(...$args)
    {
        $this->add(new Route('patch', ...$args));
    }

    public function delete(...$args)
    {
        $this->add(new Route('delete', ...$args));
    }

    public function mix($methods, ...$args)
    {
        foreach (preg_split('/\s*\|\s*/', $methods) as $method)

            $this->add(new Route($method, ...$args));
    }
}
