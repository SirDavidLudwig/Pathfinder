<?php

namespace Pathfinder\Routing;

use Pathfinder\Core\RouteParser;


class Route
{
    const URI_REGEX = "~(?<=^|[^\\\\])(?:[\\\\]{2})*[{]([^}]*)[}]~s";

    private $mController;
    private $mMethod;
    private $mMiddleware;
    private $mName;
    private $mPattern;
    private $mUri;
    private $mUriParts;
    private $mUriSlugs;

    public function __construct(string $method, string $uri, $controller, $middleware = [])
    {
        $this->mMethod = $method;
        $this->mName   = Null;
        
        $this->setUri($uri);
        $this->setController($controller);
        $this->setMiddleware($middleware);
    }

    public function controller()
    {
        return $this->mController;
    }

    public function method()
    {
        return $this->mMethod;
    }

    public function middleware()
    {
        return $this->mMiddleware;
    }

    public function name()
    {
        return $this->mName;
    }

    public function uri()
    {
        return $this->mUri;
    }

    public function setController($controller)
    {
        if (gettype($controller) == 'array')
        {
            if ($controller['controller'])
            {
                $this->mController = $controller['controller'];

                if (isset($controller['name']))

                    $this->mName = $controller['name'];
            }
            else
                throw new Error("No controller found in the given array");
        }
        elseif ((is_object($controller) && ($controller instanceof \Closure)) ||
                 gettype($controller) == 'string')
        {
            $this->mController = $controller;
        }
        else
        
            throw new Error("Unknown object for controller");
    }

    public function setMiddleware(array $middleware)
    {
        $this->mMiddleware = $middleware;
    }

    public function setUri(string $uri)
    {
        $this->mUri = $uri;
        $this->mUriParts = [];
        $this->mUriSlugs = [];

        $pieces = preg_split(Route::URI_REGEX, $this->mUri, -1, PREG_SPLIT_DELIM_CAPTURE);

        $this->mPattern = '/' . preg_quote($pieces[$i = 0], '/');
        $this->mUriParts[] = $pieces[$i];

        for ($i++; $i < count($pieces); $i++)
        {
            $this->mUriSlugs[] = $pieces[$i++];
            $this->mPattern .= '(.*)' . preg_quote($pieces[$i], '/');
            $this->mUriParts[] = $pieces[$i];
        }

        $this->mPattern .= '/';
    }

    public function checkUri($uri)
    {
        $result = preg_match($this->mPattern, $uri, $matches);

        if ($result)
        {
            $cUri = $this->mUriParts[$i = 0];
            $slugs = [];

            for ($i++; $i < count($this->mUriParts); $i++)
            {
                $cUri   .= $matches[$i] . $this->mUriParts[$i];
                $slugs[] = array(
                    'key'   => $this->mUriSlugs[$i - 1],
                    'value' => $matches[$i]
                );
            }

            if ($cUri == $uri)
            
                return $slugs;
            
        }

        return False;
    }
}
