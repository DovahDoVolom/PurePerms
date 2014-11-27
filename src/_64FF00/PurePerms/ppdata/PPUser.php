<?php

namespace _64FF00\PurePerms\ppdata;

use _64FF00\PurePerms\PurePerms;
use _64FF00\PurePerms\ppdata\PPGroup;

use pocketmine\IPlayer;

class PPUser implements PPDataInterface
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
	
	public function getGroup($levelName = null)
	{
		if($levelName == null)
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
	
	public function getName()
	{
		return $this->player->getName();
	}
	
	public function getNode($node)
	{
		if(!isset($this->getData()[$node]))
		{
			return null;
		}
		
		return $this->getData()[$node];
	}
	
	// TODO
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
		$permissions = $levelName != null ? $this->getWorldData($levelName)["permissions"] : $this->getNode("permissions");
		
		return $permissions;
	}
	
	public function getWorldData($levelName)
	{
		if($levelName == null) return null;
		
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
	
	public function removeNode($node)
	{
		$tempUserData = $this->getData();
		
		if(isset($tempUserData[$node]))
		{				
			unset($tempUserData[$node]);
			
			$this->setData($tempUserData);
		}
	}
	
	public function setData(array $data)
	{
		$this->plugin->getProvider()->setUserData($this, $data);
	}
	
	public function setGroup(PPGroup $group, $levelName)
	{
		if($levelName == null)
		{
			$this->setNode("group", $group->getName());
		}
		else
		{
			$worldData = $this->getWorldData($levelName);
			
			$worldData["group"] = $group->getName();
			
			$this->setWorldData($levelName, $worldData);
		}
		
		$this->plugin->updatePermissions($this->player, $levelName);
	}
	
	public function setNode($node, $value)
	{
		$tempUserData = $this->getData();
					
		$tempUserData[$node] = $value;
			
		$this->setData($tempUserData);
	}
	
	public function setUserPermission($permission, $levelName = null)
	{
		if($levelName == null)
		{
			$tempUserData = $this->getData();
					
			$tempUserData["permissions"][] = $permission;
			
			$this->setData($tempUserData);
		}
		else
		{
			$worldData = $this->getWorldData($levelName);
			
			$worldData["permissions"][] = $permission;
			
			$this->setWorldData($levelName, $worldData);
		}
		
		$this->plugin->updatePermissions($this->player, $levelName);
	}
	
	public function setWorldData($levelName, array $worldData)
	{
		if(isset($this->getData()["worlds"][$levelName]))
		{
			$tempUserData = $this->getData();
			
			$tempUserData["worlds"][$levelName] = $worldData;
				
			$this->setData($tempUserData);
		}
	}
	
	public function unsetUserPermission($permission, $levelName = null)
	{
		if($levelName == null)
		{
			$tempUserData = $this->getData();
			
			if(!in_array($permission, $tempUserData["permissions"])) return false;

			$tempUserData["permissions"] = array_diff($tempUserData["permissions"], [$permission]);
			
			$this->setData($tempUserData);
		}
		else
		{
			$worldData = $this->getWorldData($levelName);
			
			if(!in_array($permission, $worldData["permissions"])) return false;
			
			$worldData["permissions"] = array_diff($worldData["permissions"], [$permission]);
			
			$this->setWorldData($levelName, $worldData);
		}
		
		$this->plugin->updatePermissions($this->player, $levelName);
	}
}