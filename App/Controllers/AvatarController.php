<?php

namespace App\Controllers;

use App\Config;

class AvatarController {
    public static function getSource($player) {
        $source = str_replace('{player}', $player, Config::get('AVATAR_SOURCE', 'https://mc-heads.net/avatar/{player}/32'));
        return $source;
    }
}