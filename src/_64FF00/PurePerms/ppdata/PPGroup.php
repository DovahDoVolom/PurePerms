<?php

namespace _64FF00\PurePerms\ppdata;

use _64FF00\PurePerms\PurePerms;

class PPGroup implements PPDataInterface
{
	public function __construct(PurePerms $plugin, $name)
	{
		$this->plugin = $plugin;
		$this->name = $name;
	}
	
	public function getData()
	{
		return $this->plugin->getProvider()->getGroupData($this);
	}
	
	public function getInheritedGroups()
	{
		$inheritedGroups = [];
		
		foreach($this->getNode("inheritance") as $inheritedGroupName)
		{
			$inheritedGroup = $this->plugin->getGroup($inheritedGroupName);
			
			array_push($inheritedGroups, $inheritedGroup);
		}
		
		return $inheritedGroups;
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function getNode($node)
	{
		if(!isset($this->getData()[$node]))
		{
			return null;
		}
		
		return $this->getData()[$node];
	}
	
	public function getPermissions($levelName = null)
	{
		if($levelName == null)
		{
			$permissions = $this->getNode("permissions");
		}
		else
		{
			$permissions = $this->getWorldData($levelName)["permissions"];
		}
		
		foreach($this->getInheritedGroups() as $inheritedGroup)
		{
			if($permissions == null) $permissions = [];
			
			array_merge($permissions, $inheritedGroup->getPermissions($levelName)); 
		}
			
		return $permissions;
	}
	
	// TODO
	public function getUsers($isActive = false)
	{
		
	}
	
	public function getWorldData($levelName)
	{
		if($levelName == null) return null;
		
		if(!isset($this->getData()["worlds"][$levelName]))
		{
			$tempGroupData = $this->getData();
			
			$tempGroupData["worlds"][$levelName] = array(
				"permissions" => array(
				)
			);
				
			$this->setData($tempGroupData);
		}
			
		return $this->getData()["worlds"][$levelName];
	}
	
	public function isDefault()
	{
		return ($this->getNode("isDefault") == true);
	}
	
	public function removeNode($node)
	{
		$tempGroupData = $this->getData();
		
		if(isset($tempGroupData[$node]))
		{				
			unset($tempGroupData[$node]);	
			
			$this->setData($tempGroupData);
		}
	}
	
	public function setData(array $data)
	{
		$this->plugin->getProvider()->setGroupData($this, $data);
	}
	
	public function setDefault()
	{
		$this->setNode("isDefault", true);
	}
	
	public function setGroupPermission($permission, $levelName = null)
	{
		if($levelName == null)
		{
			$tempGroupData = $this->getData();
					
			$tempGroupData["permissions"][] = $permission;
			
			$this->setData($tempGroupData);
		}
		else
		{
			$worldData = $this->getWorldData($levelName);
			
			$worldData["permissions"][] = $permission;
			
			$this->setWorldData($levelName, $worldData);
		}
	}
	
	public function setNode($node, $value)
	{
		$tempGroupData = $this->getData();
		
		$tempGroupData[$node] = $value;
			
		$this->setData($tempGroupData);
	}
	
	public function setWorldData($levelName, array $worldData)
	{
		if(isset($this->getData()["worlds"][$levelName]))
		{
			$tempGroupData = $this->getData();
			
			$tempGroupData["worlds"][$levelName] = $worldData;
				
			$this->setData($tempGroupData);
		}
	}
	
	public function sortPermissions()
	{
		$tempGroupData = $this->getData();
			
		if(isset($tempGroupData["permissions"]))
		{		
			array_unique($tempGroupData["permissions"]);	
			
			sort($tempGroupData["permissions"]);
		}
		
		$isMultiWorldPermsEnabled = $this->plugin->getPPConfig()->getValue("enable-multiworld-perms");
		
		if($isMultiWorldPermsEnabled)
		{				
			if(isset($tempGroupData["worlds"]))
			{
				foreach($this->getServer()->getLevels() as $level)
				{
					$levelName = $level->getName();
						
					if(isset($tempGroupData["worlds"][$levelName]))
					{		
						array_unique($tempGroupData["worlds"][$levelName]["permissions"]);
						
						sort($tempGroupData["worlds"][$levelName]["permissions"]);
					}
				}
			}
		}
		
		$this->setData($tempGroupData);
	}
	
	public function unsetGroupPermission($permission, $levelName = null)
	{
		if($levelName == null)
		{
			$tempGroupData = $this->getData();
					
			if(!in_array($permission, $tempGroupData["permissions"])) return false;

			$tempGroupData["permissions"] = array_diff($tempGroupData["permissions"], [$permission]);
			
			$this->setData($tempGroupData);
		}
		else
		{
			$worldData = $this->getWorldData($levelName);
			
			if(!in_array($permission, $worldData["permissions"])) return false;
			
			$worldData["permissions"] = array_diff($worldData["permissions"], [$permission]);
			
			$this->setWorldData($levelName, $worldData);
		}
	}
}