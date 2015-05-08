<?php

namespace _64FF00\PurePerms;

use _64FF00\PurePerms\event\PPGroupChangeEvent;

use pocketmine\event\Listener;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\event\player\PlayerQuitEvent;

class PPListener implements Listener
{
    /* PurePerms by 64FF00 (xktiverz@gmail.com, @64ff00 for Twitter) */

    /*
          # #    #####  #       ####### #######   ###     ###   
          # #   #     # #    #  #       #        #   #   #   #  
        ####### #       #    #  #       #       #     # #     # 
          # #   ######  #    #  #####   #####   #     # #     # 
        ####### #     # ####### #       #       #     # #     # 
          # #   #     #      #  #       #        #   #   #   #  
          # #    #####       #  #       #         ###     ###                                        
                                                                                       
    */

    /**
     * @param PurePerms $plugin
     */
    public function __construct(PurePerms $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @param PPGroupChangeEvent $event
     */
    public function onGroupChange(PPGroupChangeEvent $event)
    {
        $player = $event->getPlayer();
        $levelName = $event->getLevelName();

        $this->plugin->updatePermissions($player, $levelName);
    }

    /**
     * @param EntityLevelChangeEvent $event
     */
    public function onLevelChange(EntityLevelChangeEvent $event)
    {
        $player = $event->getEntity();
        
        $isMultiWorldPermsEnabled = $this->plugin->getConfigValue("enable-multiworld-perms");
        
        $levelName = $isMultiWorldPermsEnabled ? $event->getTarget()->getName() : null;
               
        $this->plugin->updatePermissions($player, $levelName);
    }

    /**
     * @param PlayerJoinEvent $event
     */
    public function onPlayerJoin(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer();
        
        $isMultiWorldPermsEnabled = $this->plugin->getConfigValue("enable-multiworld-perms");
        
        $levelName = $isMultiWorldPermsEnabled ? $player->getLevel()->getName() : null;
        
        $this->plugin->updatePermissions($player, $levelName);
    }

    /**
     * @param PlayerKickEvent $event
     */
    public function onPlayerKick(PlayerKickEvent $event)
    {
        $player = $event->getPlayer();
        
        $this->plugin->removeAttachment($player);
    }

    /**
     * @param PlayerQuitEvent $event
     */
    public function onPlayerQuit(PlayerQuitEvent $event)
    {
        $player = $event->getPlayer();
        
        $this->plugin->removeAttachment($player);
    }
}