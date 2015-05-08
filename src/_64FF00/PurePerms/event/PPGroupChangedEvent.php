<?php

namespace _64FF00\PurePerms\event;

use _64FF00\PurePerms\ppdata\PPGroup;
use _64FF00\PurePerms\PurePerms;

use pocketmine\event\Cancellable;
use pocketmine\event\plugin\PluginEvent;

use pocketmine\IPlayer;

class PPGroupChangedEvent extends PluginEvent
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

    public static $handlerList = null;

    /**
     * @param PurePerms $plugin
     * @param IPlayer $player
     * @param PPGroup $group
     * @param $levelName
     */
    public function __construct(PurePerms $plugin, IPlayer $player, PPGroup $group, $levelName)
    {
        parent::__construct($plugin);

        $this->group = $group;
        $this->levelName = $levelName;
        $this->player = $player;
    }

    /**
     * @return PPGroup
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @return mixed
     */
    public function getLevelName()
    {
        return $this->levelName;
    }

    /**
     * @return IPlayer
     */
    public function getPlayer()
    {
        return $this->player;
    }
}