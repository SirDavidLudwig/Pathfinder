<?php

namespace Pathfinder\Http;


class Response
{    
	private $mContent;

	public function __construct($content = Null)
	{
		$this->mContent = $content;
	}

    public function send()
    {
    	echo $this->mContent;
    }
}
