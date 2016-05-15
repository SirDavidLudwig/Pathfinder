<?php

namespace Pathfinder\Http;


class Request
{
    protected $host;
    protected $connection;
    protected $userAgent;
    protected $remoteHost;
    protected $remotePort;
    protected $protocol;
    protected $method;
    protected $scheme;
    protected $uri;
    
    public static function capture()
    {
        $request = new Request();

        $request->setHost($_SERVER['HTTP_HOST'])
                ->setConnection($_SERVER['HTTP_CONNECTION'])
                ->setUserAgent($_SERVER['HTTP_USER_AGENT'])
                ->setRemoteHost($_SERVER['REMOTE_ADDR'])
                ->setRemotePort($_SERVER['REMOTE_PORT'])
                ->setProtocol($_SERVER['SERVER_PROTOCOL'])
                ->setScheme($_SERVER['REQUEST_SCHEME'])
                ->setUri($_SERVER['REQUEST_URI']);

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['_method']))

            $request->setMethod(strtoupper($_POST['_method']));

        else

            $request->setMethod($_SERVER['REQUEST_METHOD']);

        return $request;
    }

    public function __construct()
    {

    }

    public function host()
    {
        return $this->host;
    }

    public function connection()
    {
        return $this->connection;
    }

    public function userAgent()
    {
        return $this->userAgent;
    }

    public function remoteHost()
    {
        return $this->remoteHost;
    }

    public function remotePort()
    {
        return $this->remotePort;
    }

    public function protocol()
    {
        return $this->protocol;
    }

    public function scheme()
    {
        return $this->scheme;
    }

    public function method()
    {
        return $this->method;
    }

    public function uri()
    {
        return $this->uri;
    }

    public function setHost(string $host)
    {
        $this->host = $host;

        return $this;
    }

    public function setConnection(string $connection)
    {
        $this->connection = $connection;

        return $this;
    }

    public function setUserAgent(string $userAgent)
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    public function setRemoteHost(string $remoteHost)
    {
        $this->remoteHost = $remoteHost;

        return $this;
    }

    public function setRemotePort(string $remotePort)
    {
        $this->remotePort = $remotePort;

        return $this;
    }

    public function setProtocol(string $protocol)
    {
        $this->protocol = $protocol;

        return $this;
    }

    public function setScheme(string $scheme)
    {
        $this->scheme = $scheme;

        return $this;
    }

    public function setMethod(string $method)
    {
        $this->method = strtolower($method);

        return $this;
    }

    public function setUri(string $uri)
    {
        $this->uri = $uri;

        return $this;
    }

}
