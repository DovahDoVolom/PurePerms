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
		
		if(count($args) < 2 || count($args) > 3 || !is_numeric($args[1]))
		{
			$sender->sendMessage(TextFormat::BLUE . "[PurePerms] Usage: /listuperms <player> <page> [world]");
			
			return true;
		}
		
		$player = $this->plugin->getPlayer($args[0]);
		
		$levelName = isset($args[2]) ?  $this->plugin->getServer()->getLevelByName($args[2]) : null;
		
		return true;
	}
	
	public function getPlugin()
	{
		return $this->plugin;
	}
}