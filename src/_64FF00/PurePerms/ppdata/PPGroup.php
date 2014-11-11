<?php

namespace _64FF00\PurePerms\ppdata;

use _64FF00\PurePerms\PurePerms;

class PPGroup
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
	
	public function getName()
	{
		return $this->name;
	}
	
	public function getNode($node)
	{
		if(isset($this->getData()[$node]))
		{
			return $this->getData()[$node];
		}
	}
	
	public function getUsers($isActive = false)
	{
	}
	
	public function getWorldData($levelName = null)
	{
		if($levelName != null)
		{
			$levelName = $this->plugin->getServer()->getDefaultLevel()->getName();
		}
		
		if(!isset($this->getData()["worlds"][$levelName]))
		{
			$tempGroupData = $this->getData();
			
			$tempGroupData["worlds"][$levelName] = array(
				"permissions" => array(
				)
			);
				
			$this->setData($tempGroupData);
				
			unset($tempGroupData);
		}
			
		return $this->getData()["worlds"][$levelName];
	}
	
	public function isDefault()
	{
		return ($this->getNode("def-group") == true);
	}
	
	public function setData(array $groupData)
	{
		$this->plugin->getProvider()->setGroupData($this, $groupData);
	}
	
	public function setDefault()
	{
		$this->setNode("def-group", true);
	}
	
	public function setNode($node, $value)
	{
		$tempGroupData = $this->getData();
		
		if(isset($tempGroupData[$node]))
		{				
			$tempGroupData[$node] = $value;
			
			$this->setData($tempGroupData);
			
			unset($tempGroupData);
		}
	}
}