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
		return $this->plugin->getProvider()->getUserData($this);
	}
	
	public function getGroup($worldName)
	{
		return $this->getWorldData($worldName)["group"];
	}
	
	public function getPlayer()
	{
		return $this->player;
	}
	
	public function getPermissions($worldName = null)
	{
		$isMultiWorldPermsEnabled = $this->plugin->getConfig()->getValue("enable-multiworld-perms");
		
		if($worldName = null and $isMultiWorldPermsEnabled == false)
		{
			return $this->getNode("permissions");
		}
		
		return $this->getWorldData($worldName)["permissions"];
	}
	
	public function getPlayer()
	{
		return $this->player;
	}
	
	public function getWorldData($worldName = null)
	{
		if($worldName != null)
		{
			$worldName = $this->plugin->getServer()->getDefaultLevel()->getName();
		}
			
		if(!isset($this->getData()["worlds"][$worldName]))
		{
			$tempUserData = $this->getData();
			
			$tempUserData["worlds"][$worldName] = array(
				"group" => $this->plugin->getDefaultGroup()->getName(),
				"permissions" => array(
				)
			);
				
			$this->setData($tempUserData);
				
			unset($tempUserData);
		}
			
		return $this->getData()["worlds"][$worldName];
	}
	
	public function setGroup(PPGroup $group, $worldName)
	{
	}
}