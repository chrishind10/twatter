<?php

namespace App\Helper;

class Snowflake
{

    const epoch = 1288834974657;

    public static function generate()
    {
        $timestamp = time() - self::epoch;
        $output = (string) abs($timestamp << 22) | floor(random_int(0, 696969));
        return $output;
    }

}