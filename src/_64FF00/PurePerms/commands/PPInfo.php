<?php

namespace _64FF00\PurePerms\commands;

use _64FF00\PurePerms\PurePerms;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\PluginIdentifiableCommand;

use pocketmine\utils\TextFormat;

class PPInfo extends Command implements PluginIdentifiableCommand
{
	public function __construct(PurePerms $plugin, $name, $description)
	{
		$this->plugin = $plugin;
		
		parent::__construct($name, $description);
		
		$this->setPermission("pperms.command.ppinfo");
	}
	
	public function execute(CommandSender $sender, $label, array $args)
	{
		if(!$this->testPermission($sender))
		{
            return false;
        }
		
		$author = $this->plugin->getDescription()->getAuthors()[0];
		$version = $this->plugin->getDescription()->getVersion();
		
		if($sender instanceof ConsoleCommandSender)
		{
			$sender->sendMessage(TextFormat::BLUE . "[PurePerms] " . $this->plugin->getMessage("cmds.ppinfo.messages.ppinfo_console", $version, $author));
		}
		else
		{
			$sender->sendMessage(TextFormat::BLUE . "[PurePerms] " . $this->plugin->getMessage("cmds.ppinfo.messages.ppinfo_player", $version, $author));
		}	
		
		return true;
	}
	
	public function getPlugin()
	{
		return $this->plugin;
	}
}