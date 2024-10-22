<?php

use App\Controllers\TweetController;

require '../vendor/autoload.php';

// Then define a route and assign a function to handle the request.
Flight::route('/@user/status/@post', function (string $user, string $post) {
    TweetController::get($user, $post);
});

// Finally, start the framework.
Flight::start();