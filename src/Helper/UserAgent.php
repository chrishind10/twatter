<?php

namespace App\Helper;

use App\Enum\Platform;

class UserAgent
{

    const fakeChromeVersion = 124;
    const platformWindows = 'Windows NT 10.0; Win64; x64';
    const platformMac = 'Macintosh; Intel Mac OS X 10_15_7';
    const platformLinux = 'X11; Linux x86_64';
    const platformAndroid = 'Linux; Android 10; K';
    const chromeUA = 'Mozilla/5.0 ({platform}) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/{version}.0.0.0 Safari/537.36';
    const edgeUA = 'Mozilla/5.0 ({platform}) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/{version}.0.0.0 Safari/537.36 Edge/{version}.0.0.0';
    const chromeMobileUA = 'Mozilla/5.0 ({platform}) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/{version}.0.0.0 Mobile Safari/537.36';
    const edgeMobileUA = 'Mozilla/5.0 ({platform}) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/{version}.0.0.0 Mobile Safari/537.36 Edge/{version}.0.0.0';

    public static function getRandomVersion(): int
    {
        return self::fakeChromeVersion - random_int(0, 3);
    }

    public static function generate(): string
    {
        $platform = Platform::getRandom();
        $isEdge = (random_int(0, 10) > 5);
        $version = self::getRandomVersion();

        $androidUserAgent = $isEdge ? self::edgeMobileUA : self::chromeMobileUA;
        $userAgent = $isEdge ? self::edgeUA : self::chromeUA;
        $userAgent = strtr($userAgent, [
            '{platform}' => $platform->name
        ]);

        return match ($platform) {
            Platform::Mac => strtr($userAgent, ['{platform}' => self::platformMac, '{version}' => $version]),
            Platform::Linux => strtr($userAgent, ['{platform}' => self::platformLinux, '{version}' => $version]),
            Platform::Android => strtr($androidUserAgent, ['{platform}' => self::platformAndroid, '{version}' => $version]),
            default => strtr($userAgent, ['{platform}' => self::platformWindows, '{version}' => $version])
        };
    }

}