<?php

namespace Pathfinder\Testing;

use Something;
use Pathfinder\Http\Controller;
use Pathfinder\Http\Request;

class HomeController extends Controller
{
	public function index(Something $s, Request $request, $username)
	{
		return $s->test() . $request->uri();
	}
}