<?php

namespace App\Controllers;

use App\Helper\Twitter;
use Flight;

class TweetController
{

    public static function get(string $user, string $post)
    {
        $tweet = Twitter::fetchTweet($post);
        Flight::json($tweet);
    }

}