<?php

namespace _64FF00\PurePerms\commands;

use _64FF00\PurePerms\PurePerms;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;

use pocketmine\utils\TextFormat;

class GroupList extends Command implements PluginIdentifiableCommand
{
	public function __construct(PurePerms $plugin, $name, $description)
	{
		$this->plugin = $plugin;
		
		parent::__construct($name, $description);
		
		$this->setPermission("pperms.command.grouplist");
	}
	
	public function execute(CommandSender $sender, $label, array $args)
	{
		if(!$this->testPermission($sender))
		{
			return false;
		}
		
		foreach($this->plugin->getGroups() as $group)
		{
			$result .= $group->getName() . ", ";
		}
		
		$sender->sendMessage(TextFormat::BLUE . "[PurePerms] All registered groups: " . substr($result, 0, -2));
		
		return true;
	}
	
	public function getPlugin()
	{
		return $this->plugin;
	}
}