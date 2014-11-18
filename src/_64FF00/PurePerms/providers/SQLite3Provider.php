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
		if(!file_exists($this->plugin->getDataFolder() . "players.db"))
		{
			$this->players = new \SQLite3($this->plugin->getDataFolder() . "players.db", SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
			
			$query = stream_get_contents($this->plugin->getResource("players.sql"));
			
			$this->players->exec($query);
		}
		
		if(!file_exists($this->plugin->getDataFolder() . "groups.db"))
		{
			$this->groups = new \SQLite3($this->plugin->getDataFolder() . "groups.db", SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
			
			$query = stream_get_contents($this->plugin->getResource("groups.sql"));
			
			$this->groups->exec($query);
		}
		
		$this->players = new \SQLite3($this->plugin->getDataFolder() . "players.db", SQLITE3_OPEN_READWRITE);
		$this->groups = new \SQLite3($this->plugin->getDataFolder() . "groups.db", SQLITE3_OPEN_READWRITE);
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