<?php

namespace Pathfinder\Routing;


class Route
{
    private $_method;
    private $_uri;

    public function __construct(string $method, string $uri, $arg1, $arg2 = Null)
    {
        $this->_method = $method;
        $this->_uri    = $uri;
    }

    public function method()
    {
        return $this->_method;
    }

    public function uri()
    {
        return $this->_uri;
    }
}
