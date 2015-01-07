<?php

namespace _64FF00\PurePerms\commands;

use _64FF00\PurePerms\PurePerms;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;

use pocketmine\utils\TextFormat;

class SetUPerm extends Command implements PluginIdentifiableCommand
{
	public function __construct(PurePerms $plugin, $name, $description)
	{
		$this->plugin = $plugin;
		
		parent::__construct($name, $description);
		
		$this->setPermission("pperms.command.setuperm");
	}
	
	public function execute(CommandSender $sender, $label, array $args)
	{
		if(!$this->testPermission($sender))
		{
			return false;
		}
		
		if(count($args) < 2 || count($args) > 3)
		{
			$sender->sendMessage(TextFormat::BLUE . "[PurePerms] " . $this->plugin->getMessage("cmds.setuperm.usage"));
			
			return true;
		}
		
		$player = $this->plugin->getPlayer($args[0]);
		
		$permission = $args[1];
		
		if(!$this->plugin->isValidPerm($permission))
		{
			$sender->sendMessage(TextFormat::RED . "[PurePerms] " . $this->plugin->getMessage("cmds.setuperm.messages.perm_not_exist", $args[1]));
			
			return true;
		}
		
		$levelName = null;
		
		if(isset($args[2]))
		{
			$level = $this->plugin->getServer()->getLevelByName($args[2]);
			
			if($level == null)
			{
				$sender->sendMessage(TextFormat::RED . "[PurePerms] " . $this->plugin->getMessage("cmds.setuperm.messages.level_not_exist", $args[2]));
				
				return true;
			}
			
			$levelName = $level->getName();
		}
		
		$this->plugin->getUser($player)->setUserPermission($permission, $levelName);
		
		$sender->sendMessage(TextFormat::BLUE . "[PurePerms] " . $this->plugin->getMessage("cmds.setuperm.messages.uperm_added_successfully", $permission, $player->getName()));
		
		return true;
	}
	
	public function getPlugin()
	{
		return $this->plugin;
	}
}