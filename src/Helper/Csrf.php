<?php

namespace App\Helper;

class Csrf
{

    // 3cdacde9f8a443eca1cf44a6a87515c6c6b4dabde165bbeac347bff9ae9815d1e463227e93f32299d5118b579ebe9c493cd5514f5bbc6765736b4c9d59573249fd2bd93de026afd56f6511220d050662
    public static function generate(): string
    {
        return bin2hex(random_bytes(80));
    }

}