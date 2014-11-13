<?php

namespace _64FF00\PurePerms\providers;

use _64FF00\PurePerms\PurePerms;
use _64FF00\PurePerms\ppdata\PPGroup;
use _64FF00\PurePerms\ppdata\PPUser;

use pocketmine\IPlayer;

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
	
	public function getGroupData(PPGroup $group)
	{
		$groupName = $group->getName();
		
		if(isset($this->getGroupsData(true)[$groupName]) and is_array($this->getGroupsData(true)[$groupName]))
		{
			return $this->getGroupsData(true)[$groupName];
		}
	}
	
	public function getGroupsData($isArray = false)
	{
		if($isArray) return $this->groups->getAll();
		
		return $this->groups;
	}
	
	public function getUserData(PPUser $user, $isArray = false)
	{
		$userName = $user->getPlayer()->getName();
		
		if(!(file_exists($this->plugin->getDataFolder() . "players/" . strtolower($userName) . ".yml")))
		{
			$userConfig = new Config($this->plugin->getDataFolder() . "players/" . strtolower($userName) . ".yml", Config::YAML, array(
				"username" => $userName,
				"permissions" => array(
				),
				"worlds" => array(
				)
			));
		}
		else
		{
			$userConfig = new Config($this->plugin->getDataFolder() . "players/" . strtolower($userName) . ".yml", Config::YAML, array(
			));
		}
		
		if($isArray) return $userConfig->getAll();
		
		return $userConfig;
	}
	
	public function setGroupData(PPGroup $group, array $groupData)
	{
		$groupName = $group->getName();
		
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
		$userData = $this->getUserData($user);
		
		$userData->setAll($data);
			
		$userData->save();
	}
	
	public function close()
	{
	}
}