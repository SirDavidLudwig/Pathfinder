<?php

namespace Pathfinder\Routing;


class Router
{
    /**
     * List of routes for each of the Http/REST apis
     * @var array
     */
    protected $routes = [
        'get'    => [],
        'post'   => [],
        'put'    => [],
        'patch'  => [],
        'delete' => [],
    ];

    /**
     * List of named routes where the key is a name pointing to a route
     * @var array
     */
    protected $namedRoutes = [];
    
    public function __construct()
    {

    }

    /**
     * Add a route to the route list
     * @param Route $route route to add
     * @return void
     */
    public function add(Route $route)
    {
        $this->routes[$route->method()][] = $route;

        if (gettype($route->name()) == 'string' && strlen(trim($route->name())) > 0)
        
            $this->namedRoutes[trim($route->name())] = $route;
    }

    /**
     * Create a route that accepts all request methods
     * @param  mixed $args Arguments to be placed into the route's constructor
     * @return void
     */
    public function all(...$args)
    {
        $this->mix("get|post|put|patch|delete", ...$args);
    }

    /**
     * Create a route that accepts the GET Http/REST method
     * @param  mixed $args Arguments to be placed into the route's constructor
     * @return void
     */
    public function get(...$args)
    {
        $this->add(new Route('get', ...$args));
    }

    /**
     * Create a route that accepts the POST Http/REST method
     * @param  mixed $args Arguments to be placed into the route's constructor
     * @return void
     */
    public function post(...$args)
    {
        $this->add(new Route('post', ...$args));
    }

    /**
     * Create a route that accepts the PUT Http/REST method
     * @param  mixed $args Arguments to be placed into the route's constructor
     * @return void
     */
    public function put(...$args)
    {
        $this->add(new Route('put', ...$args));
    }

    /**
     * Create a route that accepts the PATCH Http/REST method
     * @param  mixed $args Arguments to be placed into the route's constructor
     * @return void
     */
    public function patch(...$args)
    {
        $this->add(new Route('patch', ...$args));
    }

    /**
     * Create a route that accepts the DELETE Http/REST method
     * @param  mixed $args Arguments to be placed into the route's constructor
     * @return void
     */
    public function delete(...$args)
    {
        $this->add(new Route('delete', ...$args));
    }

    /**
     * Create a route that uses the given Http/REST methods
     * @param  string $method The Http/REST request method
     * @param  string $args   Arguments to be placed into the route's constructor
     * @return void
     */
    public function mix($methods, ...$args)
    {
        foreach (preg_split('/\s*\|\s*/', $methods) as $method)

            $this->add(new Route($method, ...$args));
    }

    /**
     * Locate and retrieve a route by the given request method and uri
     * @param  string $method The Http/REST request method
     * @param  string $uri    The Uri to route to
     * @return void
     */
    public function route(string $method, string $uri)
    {
        foreach ($this->routes[$method] as $route)
        
            if ($result = $route->checkUri($uri))
            
                return [$route, $result];
            
        return False;
    }

    /**
     * Locate and retrieve a route by it's name
     * @param  string $name The route's name
     * @return Route
     */
    public function locate(string $name)
    {
        return @$this->namedRoutes[$name] ?: Null;
    }
}
