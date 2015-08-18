<?php

namespace _64FF00\PurePerms\commands;

use _64FF00\PurePerms\PurePerms;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;

use pocketmine\Player;

use pocketmine\utils\TextFormat;

class SetGroup extends Command implements PluginIdentifiableCommand
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
     * @param $name
     * @param $description
     */
    public function __construct(PurePerms $plugin, $name, $description)
    {
        $this->plugin = $plugin;
        
        parent::__construct($name, $description);
        
        $this->setPermission("pperms.command.setgroup");
    }

    /**
     * @param CommandSender $sender
     * @param $label
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $sender, $label, array $args)
    {
        if(!$this->testPermission($sender))
        {
            return false;
        }
        
        if(count($args) < 2 || count($args) > 3)
        {
            $sender->sendMessage(TextFormat::BLUE . "[PurePerms] " . $this->plugin->getMessage("cmds.setgroup.usage"));
            
            return true;
        }
        
        $player = $this->plugin->getPlayer($args[0]);
        
        $group = $this->plugin->getGroup($args[1]);
        
        if($group == null) 
        {
            $sender->sendMessage(TextFormat::RED . "[PurePerms] " . $this->plugin->getMessage("cmds.setgroup.messages.group_not_exist", $args[1]));
            
            return true;
        }
        
        $levelName = null;
        
        if(isset($args[2]))
        {
            $level = $this->plugin->getServer()->getLevelByName($args[2]);
            
            if($level == null)
            {
                $sender->sendMessage(TextFormat::RED . "[PurePerms] " . $this->plugin->getMessage("cmds.setgroup.messages.level_not_exist", $args[2]));
                
                return true;
            }
            
            $levelName = $level->getName();
        }

        $this->plugin->getUser($player)->setGroup($group, $levelName);
        
        $sender->sendMessage(TextFormat::BLUE . "[PurePerms] " . $this->plugin->getMessage("cmds.setgroup.messages.setgroup_successfully", $player->getName()));
        
        if($player instanceof Player) $player->sendMessage(TextFormat::BLUE . "[PurePerms] " . $this->plugin->getMessage("cmds.setgroup.messages.on_player_group_change", strtolower($group->getName())));
        
        return true;
    }
    
    public function getPlugin()
    {
        return $this->plugin;
    }
}