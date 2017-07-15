<?php

namespace _64FF00\PurePerms\cmd;

use _64FF00\PurePerms\PurePerms;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\PluginIdentifiableCommand;

use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;

class SetGroup extends Command implements PluginIdentifiableCommand
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
    public function execute(CommandSender $sender, string $label, array $args) : bool
    {
        if(!$this->testPermission($sender))
        {
            return false;
        }
        
        if(count($args) < 2 || count($args) > 4)
        {
            $sender->sendMessage(TextFormat::GREEN . PurePerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.setgroup.usage"));
            
            return true;
        }
        
        $player = $this->plugin->getPlayer($args[0]);
        
        $group = $this->plugin->getGroup($args[1]);
        
        if($group === null)
        {
            $sender->sendMessage(TextFormat::RED . PurePerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.setgroup.messages.group_not_exist", $args[1]));
            
            return true;
        }

        $expTime = -1;

        if(isset($args[2]))
            $expTime = $this->plugin->date2Int($args[2]);
        
        $levelName = null;
        
        if(isset($args[3]))
        {
            $level = $this->plugin->getServer()->getLevelByName($args[3]);
            
            if($level === null)
            {
                $sender->sendMessage(TextFormat::RED . PurePerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.setgroup.messages.level_not_exist", $args[3]));
                
                return true;
            }
            
            $levelName = $level->getName();
        }

        $superAdminRanks = $this->plugin->getConfigValue("superadmin-ranks");

        foreach(array_values($superAdminRanks) as $value)
        {
            $tmpSuperAdminRanks[$value] = 1;
        }

        if(!($sender instanceof ConsoleCommandSender))
        {
            if(isset($tmpSuperAdminRanks[$group->getName()]))
            {
                $sender->sendMessage(TextFormat::RED . PurePerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.setgroup.messages.access_denied_01", $group->getName()));

                return true;
            }

            $userGroup = $this->plugin->getUserDataMgr()->getGroup($player, $levelName);

            if(isset($tmpSuperAdminRanks[$userGroup->getName()]))
            {
                $sender->sendMessage(TextFormat::RED . PurePerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.setgroup.messages.access_denied_02", $userGroup->getName()));

                return true;
            }
        }

        $this->plugin->getUserDataMgr()->setGroup($player, $group, $levelName, $expTime);
        
        $sender->sendMessage(TextFormat::GREEN . PurePerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.setgroup.messages.setgroup_successfully", $player->getName()));
        
        if($player instanceof Player)
        {
            if(!$this->plugin->getConfigValue("enable-multiworld-perms") || ($this->plugin->getConfigValue("enable-multiworld-perms") and $levelName === $player->getLevel()->getName()))
                $player->sendMessage(TextFormat::GREEN . PurePerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.setgroup.messages.on_player_group_change", strtolower($group->getName())));
        }

        return true;
    }
    
    public function getPlugin() : Plugin
    {
        return $this->plugin;
    }
}