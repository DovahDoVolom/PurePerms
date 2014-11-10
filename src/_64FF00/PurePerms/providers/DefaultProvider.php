<?php

namespace _64FF00\PurePerms\providers;

use _64FF00\PurePerms\PurePerms;
use _64FF00\PurePerms\ppdata\PPUser;

use pocketmine\IPlayer;

use pocketmine\utils\Config;

class DefaultProvider implements ProviderInterface
{
	public static $DATH_PATH;
	
	private $groups;
	
	public function __construct(PurePerms $plugin)
	{
		$this->plugin = $plugin;
		
		$DATH_PATH = $this->plugin->getDataFolder() . "players/";
		
		$this->init();
	}
	
	public function init()
	{
		@mkdir(self::$DATH_PATH, 0777, true);
		
		$this->plugin->saveResource("groups.yml");
		
		$this->groups = new Config($this->plugin->getDataFolder() . "groups.yml", Config::YAML, array(
		));
	}
	
	public function getGroupData(PPGroup $group)
	{
		$groupName = $group->getName();
		
		if(isset($this->getGroupsData()[$groupName]) and is_array($this->getGroupsData()[$groupName]))
		{
			return $this->getGroupsData()[$groupName];
		}
	}
	
	public function getGroupsData()
	{
		return $this->groups->getAll();
	}
	
	public function getUserData(PPUser $user)
	{
		$userName = $user->getPlayer()->getName();
		
		if(!(file_exists(self::$DATH_PATH . strtolower($userName) . ".yml")))
		{
			return new Config(self::$DATH_PATH . strtolower($userName) . ".yml", Config::YAML, array(
				"username" => $userName,
				"permissions" => array(
				),
				"worlds" => array(
				)
			));
		}
		else
		{
			return new Config(self::$DATH_PATH . strtolower($userName) . ".yml", Config::YAML, array(
			));
		}
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