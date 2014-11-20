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
		
		if(count($args) < 1 || count($args) > 2)
		{
			$sender->sendMessage(TextFormat::BLUE . "[PurePerms] Usage: /setgperm <group> <permission> [world]");
			
			return true;
		}
		
		$group = $this->plugin->getGroup($args[0]);
		
		$permission = $args[1];
		
		$levelName = isset($args[2]) ?  $this->plugin->getServer()->getLevelByName($args[2]) : null;
		
		$group->setGroupPermission($permission, $levelName);
		
		$sender->sendMessage(TextFormat::BLUE . "[PurePerms] Added permission " . $permission . " to the group successfully.");
		
		return true;
	}
	
	public function getPlugin()
	{
		return $this->plugin;
	}
}