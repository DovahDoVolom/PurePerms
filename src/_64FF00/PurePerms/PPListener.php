<?php

namespace _64FF00\PurePerms;

use _64FF00\PurePerms\EventManager\PPRankChangedEvent;
use _64FF00\PurePerms\EventManager\PPRankExpiredEvent;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class PPListener implements Listener
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

    private $plugin;

    /**
     * @param PurePerms $plugin
     */
    public function __construct(PurePerms $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @param PPGroupChangedEvent $event
     * @priority LOWEST
     */
    public function onGroupChanged(PPRankChangedEvent $event)
    {
        $player = $event->getPlayer();
        $this->plugin->updatePermissions($player);
    }

    /**
     * @param EntityTeleportEvent $event
     * @priority MONITOR
     */
    public function onLevelChange(EntityTeleportEvent $event)
    {
        if($event->isCancelled()) return;
        $player = $event->getEntity();
        if($player instanceof Player) {
            $this->plugin->updatePermissions($player, $event->getTo()->getWorld()->getDisplayName());
        }
    }

    public function onPlayerCommand(PlayerCommandPreprocessEvent $event)
    {
        $message = $event->getMessage();
        $player = $event->getPlayer();

        if(substr($message, 0, 1) === "/")
        {
            $command = substr($message, 1);
            $args = explode(" ", $command);

            $disableOp = $this->plugin->getConfigValue("disable-op");

            if($disableOp and $args[0] === "op")
            {
                $event->cancel();

                $player->sendMessage(new Translatable(TextFormat::RED . "%commands.generic.permission"));
            }
        }
    }

    /**
     * @param PlayerLoginEvent $event
     * @priority LOWEST
     */
    public function onPlayerLogin(PlayerLoginEvent $event)
    {
        $player = $event->getPlayer();
        $this->plugin->registerPlayer($player);
    }

    /**
     * @param PlayerQuitEvent $event
     * @priority HIGHEST
     */
    public function onPlayerQuit(PlayerQuitEvent $event)
    {
        $player = $event->getPlayer();
        $this->plugin->unregisterPlayer($player);
    }

    /**
     * @param PPRankExpiredEvent $event
     * @priority LOWEST
     */
    public function onRankExpired(PPRankExpiredEvent $event)
    {
        $player = $event->getPlayer();
        $this->plugin->setGroup($player, $this->plugin->getDefaultGroup());
    }
}