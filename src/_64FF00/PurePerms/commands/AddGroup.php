<?php

namespace _64FF00\PurePerms\commands;

use _64FF00\PurePerms\PurePerms;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;

use pocketmine\utils\TextFormat;

class AddGroup extends Command implements PluginIdentifiableCommand
{
	public function __construct(PurePerms $plugin, $name, $description)
	{
		$this->plugin = $plugin;
		
		parent::__construct($name, $description);
		
		$this->setPermission("pperms.command.addgroup");
	}
	
	public function execute(CommandSender $sender, $label, array $args)
	{
	    if(!$this->testPermission($sender))
		{
			return false;
		}
		
		if(!isset($args[0]) || count($args) > 1)
		{
			$sender->sendMessage(TextFormat::BLUE . "[PurePerms] Usage: /addgroup <group>");
			
			return true;
		}
		
		if($this->plugin->addGroup($args[0]))
		{
			$sender->sendMessage(TextFormat::BLUE . "[PurePerms] Added " . $args[0] . " to the group list successfully.");
		}
		else
		{
			$sender->sendMessage(TextFormat::RED . "[PurePerms] Group " . $args[0] . " already exists.");
		}
		
		return true;
	}
	
	public function getPlugin()
	{
		return $this->plugin;
	}
}