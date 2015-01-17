<?php

namespace _64FF00\PurePerms\commands;

use _64FF00\PurePerms\PurePerms;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;

use pocketmine\utils\TextFormat;

class SetGPerm extends Command implements PluginIdentifiableCommand
{
	public function __construct(PurePerms $plugin, $name, $description)
	{
		$this->plugin = $plugin;
		
		parent::__construct($name, $description);
		
		$this->setPermission("pperms.command.setgperm");
	}
	
	public function execute(CommandSender $sender, $label, array $args)
	{
		if(!$this->testPermission($sender))
		{
			return false;
		}
		
		if(count($args) < 2 || count($args) > 3)
		{
			$sender->sendMessage(TextFormat::BLUE . "[PurePerms] " . $this->plugin->getMessage("cmds.setgperm.usage"));
			
			return true;
		}
		
		$group = $this->plugin->getGroup($args[0]);
		
		if($group == null)
		{
			$sender->sendMessage(TextFormat::RED . "[PurePerms] " . $this->plugin->getMessage("cmds.setgperm.messages.group_not_exist", $args[0]));
			
			return true;
		}
		
		$permission = $args[1];
		
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
		
		$group->setGroupPermission($permission, $levelName);
		
		$sender->sendMessage(TextFormat::BLUE . "[PurePerms] " . $this->plugin->getMessage("cmds.setgperm.messages.gperm_added_successfully", $permission));
		
		return true;
	}
	
	public function getPlugin()
	{
		return $this->plugin;
	}
}