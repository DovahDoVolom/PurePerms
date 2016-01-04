<?php

namespace _64FF00\PurePerms\noeul;

use _64FF00\PurePerms\PurePerms;

use pocketmine\Player;

class NoeulAPI
{
    /*
        PurePerms by 64FF00 (Twitter: @64FF00)

          888  888    .d8888b.      d8888  8888888888 8888888888 .d8888b.   .d8888b.
          888  888   d88P  Y88b    d8P888  888        888       d88P  Y88b d88P  Y88b
        888888888888 888          d8P 888  888        888       888    888 888    888
          888  888   888d888b.   d8P  888  8888888    8888888   888    888 888    888
          888  888   888P "Y88b d88   888  888        888       888    888 888    888
        888888888888 888    888 8888888888 888        888       888    888 888    888
          888  888   Y88b  d88P       888  888        888       Y88b  d88P Y88b  d88P
          888  888    "Y8888P"        888  888        888        "Y8888P"   "Y8888P"
    */

    private $needAuth = [];

    public function __construct(PurePerms $plugin)
    {
        $this->plugin = $plugin;
    }

    // TODO
    public function auth(Player $player)
    {
        if($this->isAuthed($player))
            return true;
    }

    // TODO
    public function deAuth(Player $player)
    {
        $this->needAuth[spl_object_hash($player)] = true;
    }

    /**
     * @param $password
     * @return bool|string
     */
    public function hash($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function isAuthed(Player $player)
    {
        return !isset($this->needAuth[spl_object_hash($player)]);
    }

    /**
     * @return bool
     */
    public function isNoeulEnabled()
    {
        return $this->plugin->getConfigValue("enable-noeul-sixtyfour");
    }
}