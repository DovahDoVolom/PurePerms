<?php

namespace _64FF00\PurePerms\commands;

use _64FF00\PurePerms\PurePerms;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\PluginIdentifiableCommand;

use pocketmine\utils\TextFormat;

class ListUPerms extends Command implements PluginIdentifiableCommand
{
    public function __construct(PurePerms $plugin, $name, $description)
    {
        $this->plugin = $plugin;
        
        parent::__construct($name, $description);
        
        $this->setPermission("pperms.command.listuperms");
    }
    
    public function execute(CommandSender $sender, $label, array $args)
    {
        if(!$this->testPermission($sender))
        {
            return false;
        }
        
        if(count($args) < 1 || count($args) > 3)
        {
            $sender->sendMessage(TextFormat::BLUE . "[PurePerms] " . $this->plugin->getMessage("cmds.listuperms.usage"));
            
            return true;
        }
        
        $player = $this->plugin->getPlayer($args[0]);
        
        $levelName = null;
        
        if(isset($args[2]))
        {
            $level = $this->plugin->getServer()->getLevelByName($args[2]);
            
            if($level == null)
            {
                $sender->sendMessage(TextFormat::RED . "[PurePerms] " . $this->plugin->getMessage("cmds.setgperm.messages.level_not_exist", $args[2]));
                
                return true;
            }
            
            $levelName = $level->getName();
        }
        
        $permissions = $this->plugin->getUser($player)->getUserPermissions($levelName);
        
        if(empty($permissions))
        {
            $sender->sendMessage(TextFormat::BLUE . "[PurePerms] " . $this->plugin->getMessage("cmds.listuperms.messages.no_user_perms", $player->getName()));
            
            return true;
        }
        
        $pageHeight = $sender instanceof ConsoleCommandSender ? 24 : 6;
                
        $chunkedPermissions = array_chunk($permissions, $pageHeight); 
        
        $maxPageNumber = count($chunkedPermissions);
        
        if(!isset($args[1]) || !is_numeric($args[1]) || $args[1] <= 0) 
        {
            $pageNumber = 1;
        }
        else if($args[1] > $maxPageNumber)
        {
            $pageNumber = $maxPageNumber;   
        }
        else 
        {
            $pageNumber = $args[1];
        }
        
        $sender->sendMessage(TextFormat::BLUE . "[PurePerms] " . $this->plugin->getMessage("cmds.listuperms.messages.user_perms_list", $player->getName(), $pageNumber, $maxPageNumber));
        
        foreach($chunkedPermissions[$pageNumber - 1] as $permission)
        {
            $sender->sendMessage(TextFormat::BLUE . "[PurePerms] - " . $permission);
        }
        
        return true;
    }
    
    public function getPlugin()
    {
        return $this->plugin;
    }
}