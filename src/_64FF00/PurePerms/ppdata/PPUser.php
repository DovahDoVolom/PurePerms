<?php

namespace _64FF00\PurePerms\ppdata;

use _64FF00\PurePerms\PurePerms;

use pocketmine\IPlayer;

class PPUser
{	
	public function __construct(PurePerms $plugin, IPlayer $player)
	{
		$this->player = $player;
		$this->plugin = $plugin;
	}
	
	public function getData()
	{
		return $this->plugin->getProvider()->getUserData($this, true);
	}
	
	public function getGroup($levelName)
	{
		return $this->getWorldData($levelName)["group"];
	}
	
	public function getPermissions($levelName = null)
	{
		$isMultiWorldPermsEnabled = $this->plugin->getPPConfig()->getValue("enable-multiworld-perms");
		
		if($levelName = null and !$isMultiWorldPermsEnabled)
		{
			return $this->getNode("permissions");
		}
		
		return $this->getWorldData($levelName)["permissions"];
	}
	
	public function getPlayer()
	{
		return $this->player;
	}
	
	public function getWorldData($levelName = null)
	{
		if($levelName != null)
		{
			$levelName = $this->plugin->getServer()->getDefaultLevel()->getName();
		}
			
		if(!isset($this->getData()["worlds"][$levelName]))
		{
			$tempUserData = $this->getData();
			
			$tempUserData["worlds"][$levelName] = array(
				"group" => $this->plugin->getDefaultGroup()->getName(),
				"permissions" => array(
				)
			);
				
			$this->setData($tempUserData);
		}
			
		return $this->getData()["worlds"][$levelName];
	}
	
	public function setGroup(PPGroup $group, $levelName)
	{
	}
}