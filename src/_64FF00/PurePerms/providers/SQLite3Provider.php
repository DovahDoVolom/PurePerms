<?php

namespace _64FF00\PurePerms\providers;

use _64FF00\PurePerms\PurePerms;
use _64FF00\PurePerms\ppdata\PPGroup;
use _64FF00\PurePerms\ppdata\PPUser;

use pocketmine\IPlayer;

class SQLite3Provider implements ProviderInterface
{
	public function __construct(PurePerms $plugin)
	{
		$this->plugin = $plugin;
		
		$this->init();
	}
	
	public function init()
	{
		$this->players = new \SQLite3($this->plugin->getDataFolder() . "players.db");
		$this->groups = new \SQLite3($this->plugin->getDataFolder() . "groups.db");
			
		$players_query = stream_get_contents($this->plugin->getResource("players.sql"));
		$groups_query = stream_get_contents($this->plugin->getResource("groups.sql"));
			
		$this->players->exec($players_query);
		$this->groups->exec($groups_query);	
	}
	
	public function getGroupData(PPGroup $group)
	{
	}
	
	public function getGroupsData($isArray = false)
	{
	}
	
	public function getUserData(PPUser $user, $isArray = false)
	{
	}
	
	public function setGroupData(PPGroup $group, array $groupData)
	{
	}
	
	public function setGroupsData(array $data)
	{
	}

	public function setUserData(PPUser $user, array $data)
	{
	}
	
	public function close()
	{
		$this->players->close();
		$this->groups->close();
	}
}