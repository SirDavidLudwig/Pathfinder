<?php


$router->get('/Pathfinder/', function(){ echo "Home page"; });

$router->get("/Pathfinder/profile/{username}/edit/{option}",
	[
		'controller' => '\Pathfinder\Testing\HomeController',
		'name' => 'home'
	],
	['auth']
);