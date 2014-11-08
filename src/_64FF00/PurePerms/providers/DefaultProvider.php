<?php

namespace _64FF00\PurePerms\providers;

use _64FF00\PurePerms\PurePerms;
use _64FF00\PurePerms\ppdata\PPUser;

use pocketmine\utils\Config;

class DefaultProvider implements ProviderInterface
{
	private $groups;
	
	public function __construct(PurePerms $plugin)
	{
		$this->plugin = $plugin;
		
		$this->init();
	}
	
	public function init()
	{
		@mkdir($this->plugin->getDataFolder() . "players/", 0777, true);
		
		$this->plugin->saveResource("groups.yml");
		
		$this->groups = new Config($this->plugin->getDataFolder() . "groups.yml", Config::YAML, array(
		));
	}
	
	public function getGroupData($groupName)
	{
		if(isset($this->groups->getAll()[$groupName]) and is_array($this->groups->getAll()[$groupName]))
		{
			return $this->groups->getAll()[$groupName];
		}
	}
	
	public function getGroupsData()
	{
		return $this->groups->getAll();
	}
	
	public function getUserData(PPUser $user)
	{
		
	}
	
	public function setGroupData($groupName, array $groupData)
	{
		$this->groups->set($groupName, $groupData);
		
		$this->groups->save();
	}
	
	public function setGroupsData(array $data)
	{
		$this->groups->setAll($data);
		
		$this->groups->save();
	}

	public function setUserData(PPUser $user, array $data)
	{
	}
}