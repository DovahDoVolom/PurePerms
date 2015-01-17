<?php

namespace _64FF00\PurePerms\commands;

use _64FF00\PurePerms\PurePerms;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\PluginIdentifiableCommand;

use pocketmine\utils\TextFormat;

class FPerms extends Command implements PluginIdentifiableCommand
{
	public function __construct(PurePerms $plugin, $name, $description)
	{
		$this->plugin = $plugin;
		
		parent::__construct($name, $description);
		
		$this->setPermission("pperms.command.fperms");
	}
	
	public function execute(CommandSender $sender, $label, array $args)
	{
		if(!$this->testPermission($sender))
		{
			return false;
		}
		
		if(!isset($args[0]) || count($args) > 2)
		{
			$sender->sendMessage(TextFormat::BLUE . "[PurePerms] " . $this->plugin->getMessage("cmds.fperms.usage"));
			
			return true;
		}
		
		$plugin = $this->plugin->getServer()->getPluginManager()->getPlugin($args[0]);
		
		if($plugin == null)
		{
			$sender->sendMessage(TextFormat::RED . "[PurePerms] " . $this->plugin->getMessage("cmds.fperms.messages.plugin_not_exist", $args[0]));
			
			return true;
		}
		
		$permissions = $plugin->getDescription()->getPermissions();
		
		if(empty($permissions))
		{
			$sender->sendMessage(TextFormat::BLUE . "[PurePerms] " . $this->plugin->getMessage("cmds.fperms.messages.no_plugin_perms", $plugin->getName()));
			
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
		
		$sender->sendMessage(TextFormat::BLUE . "[PurePerms] " . $this->plugin->getMessage("cmds.fperms.messages.plugin_perms_list", $plugin->getName(), $pageNumber, $maxPageNumber));
		
		foreach($chunkedPermissions[$pageNumber - 1] as $permission)
		{
			$sender->sendMessage(TextFormat::BLUE . "[PurePerms] - " . $permission->getName());
		}
		
		return true;
	}
	
	public function getPlugin()
	{
		return $this->plugin;
	}
}