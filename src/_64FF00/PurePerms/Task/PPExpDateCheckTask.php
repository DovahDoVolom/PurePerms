<?php

namespace _64FF00\PurePerms\Task;

use _64FF00\PurePerms\EventManager\PPRankExpiredEvent;
use _64FF00\PurePerms\PurePerms;

use pocketmine\scheduler\Task;

class PPExpDateCheckTask extends Task
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

	private PurePerms $plugin;

    /**
     * @param PurePerms $plugin
     */
    public function __construct(PurePerms $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onRun():void
    {
        foreach($this->plugin->getServer()->getOnlinePlayers() as $player)
        {
            if(time() === $this->plugin->getUserDataMgr()->getNode($player, "expTime"))
            {
                $levelName = $this->plugin->getConfigValue("enable-multiworld-perms") ? $player->getWorld()->getDisplayName() : null;
                $event = new PPRankExpiredEvent($this->plugin, $player, $levelName);
                $event->call();
            }
        }
    }
}
