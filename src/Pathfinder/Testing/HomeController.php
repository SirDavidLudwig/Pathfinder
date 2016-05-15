<?php

namespace Pathfinder\Testing;

use Pathfinder\Http\Controller;

class HomeController extends Controller
{
	public function index($username)
	{
		return "Hello $username";
	}
}