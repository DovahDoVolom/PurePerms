<?php

namespace _64FF00\PurePerms\commands;

use _64FF00\PurePerms\PurePerms;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;

use pocketmine\utils\TextFormat;

class UsrInfo extends Command implements PluginIdentifiableCommand
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
        
        $this->setPermission("pperms.command.usrinfo");
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
        
        if(count($args) < 1 || count($args) > 2)
        {
            $sender->sendMessage(TextFormat::BLUE . "[PurePerms] " . $this->plugin->getMessage("cmds.usrinfo.usage"));
            
            return true;
        }
        
        $player = $this->plugin->getPlayer($args[0]);
        
        $user = $this->plugin->getUser($player);
        
        $levelName = null;
        
        if(isset($args[1]))
        {
            $level = $this->plugin->getServer()->getLevelByName($args[1]);
            
            if($level == null)
            {
                $sender->sendMessage(TextFormat::RED . "[PurePerms] " . $this->plugin->getMessage("cmds.usrinfo.messages.level_not_exist", $args[1]));
                
                return true;
            }
            
            $levelName = $level->getName();
        }
        
        $status = $player instanceof Player ? TextFormat::GREEN . $this->plugin->getMessage("cmds.usrinfo.messages.status_online") : TextFormat::RED . $this->plugin->getMessage("cmds.usrinfo.messages.status_offline");
        
        $sender->sendMessage(TextFormat::BLUE . "[PurePerms] " . $this->plugin->getMessage("cmds.usrinfo.messages.usrinfo_header", $player->getName()));    
        $sender->sendMessage(TextFormat::BLUE . "[PurePerms] " . $this->plugin->getMessage("cmds.usrinfo.messages.usrinfo_username", $player->getName()));
        $sender->sendMessage(TextFormat::BLUE . "[PurePerms] " . $this->plugin->getMessage("cmds.usrinfo.messages.usrinfo_status", $status));
        
        $userGroup = $user->getGroup($levelName);
        
        $sender->sendMessage(TextFormat::BLUE . "[PurePerms] " . $this->plugin->getMessage("cmds.usrinfo.messages.usrinfo_group", $userGroup->getName()));
        
        return true;
    }
    
    public function getPlugin()
    {
        return $this->plugin;
    }
}