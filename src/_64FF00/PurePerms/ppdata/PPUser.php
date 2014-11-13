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
	
	// TODO
	public function getGroup($levelName)
	{
		$isMultiWorldPermsEnabled = $this->plugin->getPPConfig()->getValue("enable-multiworld-perms");
		
		if(!$isMultiWorldPermsEnabled)
		{
			$groupName = $this->getNode("group");
		}
		else
		{
			$groupName = $this->getWorldData($levelName)["group"];
		}
		
		if(isset($groupName))
		{
			return $this->plugin->getGroup($groupName);
		}
	}
	
	public function getGroupPermissions($levelName = null)
	{
		return $this->getGroup($levelName)->getPermissions($levelName);
	}
	
	public function getNode($node)
	{
		if(!isset($this->getData()[$node]))
		{
			$this->setNode($node, null);
		}
		
		return $this->getData()[$node];
	}
	
	public function getPermissions($levelName = null)
	{
		$permissions = array_merge($this->getGroupPermissions($levelName), $this->getUserPermissions($levelName));
		
		return $permissions;
	}
	
	public function getPlayer()
	{
		return $this->player;
	}
	
	public function getUserPermissions($levelName = null)
	{
		$isMultiWorldPermsEnabled = $this->plugin->getPPConfig()->getValue("enable-multiworld-perms");
		
		if($levelName == null and !$isMultiWorldPermsEnabled)
		{
			return $this->getNode("permissions");
		}
		
		return $this->getWorldData($levelName)["permissions"];
	}
	
	public function getWorldData($levelName)
	{
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
	
	public function setData(array $userData)
	{
		$this->plugin->getProvider()->setUserData($this, $userData);
	}
	
	public function setGroup(PPGroup $group, $levelName)
	{
	}
	
	public function setNode($node, $value)
	{
		$tempUserData = $this->getData();
					
		$tempUserData[$node] = $value;
			
		$this->setData($tempUserData);
	}
}