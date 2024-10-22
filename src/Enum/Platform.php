<?php

namespace App\Enum;

enum Platform
{

    case Windows;
    case Mac;
    case Linux;
    case Android;

    public static function getRandom(): self
    {
        $platforms = self::cases();
        return $platforms[random_int(0, count($platforms) - 1)];
    }

}