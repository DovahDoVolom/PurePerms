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
			$this->setNode($node, null);
		}
		
		return $this->getData()[$node];
	}
	
	public function getPermissions($levelName = null)
	{
		$isMultiWorldPermsEnabled = $this->plugin->getPPConfig()->getValue("enable-multiworld-perms");
		
		if($levelName == null and !$isMultiWorldPermsEnabled)
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
		return ($this->getNode("def-group") == true);
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
		$this->setNode("def-group", true);
	}
	
	public function setNode($node, $value)
	{
		$tempGroupData = $this->getData();
					
		$tempGroupData[$node] = $value;
			
		$this->setData($tempGroupData);
	}
	
	public function sortPermissions()
	{
		$tempGroupData = $this->getData();
			
		if(isset($tempGroupData["permissions"]))
		{
			array_unique($tempGroupData["permissions"]);
			
			ksort($tempGroupData["permissions"]);
		}
			
		if(isset($tempGroupData["worlds"]))
		{
			foreach($this->getServer()->getLevels() as $level)
			{
				$levelName = $level->getName();
					
				if(isset($tempGroupData["worlds"][$levelName]))
				{		
					array_unique($tempGroupData["worlds"][$levelName]["permissions"]);
					
					ksort($tempGroupData["worlds"][$levelName]["permissions"]);
				}
			}
		}
			
		$this->setData($tempGroupData);
	}
}