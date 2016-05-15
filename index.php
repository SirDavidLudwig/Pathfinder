<?php


// Import the auto-loader
require __DIR__ .'/vendor/autoload.php';


// Create the kernel, which will create and manage the routes
$kernel = new Pathfinder\Core\HttpKernel([
	'middleware' => __DIR__ . '/middleware.php',
    'routes' => __DIR__ . '/routes.php'
]);


// Capture the current request
$request = Pathfinder\Http\Request::capture();


// Send the request to the kernel and generate a response
$response = $kernel->handle($request);


// Send the response to the page
$response->send();


?>


<form action="" method="post">
    <button type="submit">Submit</button>
    <select name="_method" id="">
        <option value="POST">POST</option>
        <option value="PUT">PUT</option>
        <option value="PATCH">PATCH</option>
        <option value="DELETE">DELETE</option>
    </select>
</form>
