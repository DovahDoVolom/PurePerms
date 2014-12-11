<?php

namespace _64FF00\PurePerms\commands;

use _64FF00\PurePerms\PurePerms;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;

use pocketmine\utils\TextFormat;

class UsrInfo extends Command implements PluginIdentifiableCommand
{
	public function __construct(PurePerms $plugin, $name, $description)
	{
		$this->plugin = $plugin;
		
		parent::__construct($name, $description);
		
		$this->setPermission("pperms.command.usrinfo");
	}
	
	public function execute(CommandSender $sender, $label, array $args)
	{
		if(!$this->testPermission($sender))
		{
			return false;
		}
		
		if(count($args) < 1 || count($args) > 2)
		{
			$sender->sendMessage(TextFormat::BLUE . "[PurePerms] Usage: /usrinfo <player> [world]");
			
			return true;
		}
		
		$player = $this->plugin->getPlayer($args[0]);
		
		$user = $this->plugin->getUser($player);
		
		$levelName = isset($args[1]) ?  $this->plugin->getServer()->getLevelByName($args[1])->getName() : null; 
		
		$status = $player instanceof Player ? TextFormat::GREEN . "ONLINE!" : TextFormat::RED . "OFFLINE...";
		
		$sender->sendMessage(TextFormat::BLUE . "[PurePerms] <--- User Information for " . $player->getName() . " --->");	
		$sender->sendMessage(TextFormat::BLUE . "[PurePerms] USERNAME: " . $player->getName());
		$sender->sendMessage(TextFormat::BLUE . "[PurePerms] STATUS: " . $status);
		
		$userGroup = $user->getGroup($levelName);
		
		$sender->sendMessage(TextFormat::BLUE . "[PurePerms] GROUP: " . $userGroup->getName());
		
		return true;
	}
	
	public function getPlugin()
	{
		return $this->plugin;
	}
}