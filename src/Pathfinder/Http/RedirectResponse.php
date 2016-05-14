<?php

namespace Pathfinder\Http;


class RedirectResponse extends Response
{
    private $_url;

    public function __construct($url)
    {
        $this->_url = $url;
    }

    public function send()
    {
        header("Location: $this->url");
    }
}
