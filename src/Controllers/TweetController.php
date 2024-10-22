<?php

namespace App\Controllers;

use App\Helper\Twitter;
use Flight;

class TweetController
{

    public static function get(string $user, string $post)
    {
        $tweet = Twitter::fetchTweet($user, $post, 2);
        Flight::json($tweet);
    }

}