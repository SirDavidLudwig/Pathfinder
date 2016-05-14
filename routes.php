<?php


// Add a get route to the router
$router->get("/profile/{username}/edit", function() {
    return 'You\'re editing the page';
});
