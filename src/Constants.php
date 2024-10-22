<?php

namespace App;

class Constants
{

    const TWITTER_ROOT = 'https://twitter.com';
    const TWITTER_API_ROOT = 'https://api.twitter.com';
    const GUEST_BEARER_TOKEN = 'Bearer AAAAAAAAAAAAAAAAAAAAANRILgAAAAAAnNwIzUejRCOuH5E6I8xnZz4puTs%3D1Zv7ttfk8LF81IUq16cHjhLTvJu4FA33AGWWjCpTnA';
    const BASE_HEADERS = [
        'DNT' => '1',
        'x-twitter-client-language' => 'en',
        'sec-ch-ua-mobile' => '?0',
        'content-type' => 'application/x-www-form-urlencoded',
        'x-twitter-active-user' => 'yes',
        'sec-ch-ua-platform' => '"Windows"',
        'Accept' => '*/*',
        'Origin' => 'https://twitter.com',
        'Sec-Fetch-Site' => 'same-site',
        'Sec-Fetch-Mode' => 'cors',
        'Sec-Fetch-Dest' => 'empty',
        'Referer' => 'https://twitter.com/',
        'Accept-Encoding' => 'gzip, deflate',
        'Accept-Language' => 'en'
    ];

}