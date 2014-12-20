<?php

namespace _64FF00\PurePerms\providers;

use _64FF00\PurePerms\PurePerms;
use _64FF00\PurePerms\ppdata\PPGroup;
use _64FF00\PurePerms\ppdata\PPUser;

use pocketmine\IPlayer;

class SQLite3Provider implements ProviderInterface
{
	private $groups, $groupsData = [], $players;
	
	public function __construct(PurePerms $plugin)
	{
		$this->plugin = $plugin;
		
		$this->init();
	}
	
	public function init()
	{
		$this->db = new \SQLite3($this->plugin->getDataFolder() . "PurePerms.db");
			
		$db_query = stream_get_contents($this->plugin->getResource("sqlite3_deploy.sql"));
		
		$this->db->exec($db_query);
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
		if(empty($this->groupsData))
		{
			$result = $this->db->query("
				SELECT groupName, isDefault, inheritance, permissions FROM groups;
			");
			
			if($result instanceof \SQLite3Result)
			{
				while($currentRow = $result->fetchArray(SQLITE3_ASSOC))
				{
					$groupName = $currentRow["groupName"];
					
					$this->groupsData[$groupName] = $currentRow;
					
					$inheritance = $this->groupsData[$groupName]["inheritance"];
								
					if(!is_array($inheritance)) 
					{
						$this->groupsData[$groupName]["inheritance"] = explode(",", $inheritance);
					}
					
					$permissions = $this->groupsData[$groupName]["permissions"];
					
					if(!is_array($permissions))
					{
						$this->groupsData[$groupName]["permissions"] = explode(",", $permissions);
					}
				}
				
				$result->finalize();
			}
			
			$result_mw = $this->db->query("
				SELECT groupName, worldName, permissions FROM groups_mw;
			");
			
			if($result_mw instanceof \SQLite3Result)
			{
				while($currentRow = $result_mw->fetchArray(SQLITE3_ASSOC))
				{
					$groupName = $currentRow["groupName"];
					$worldName = $currentRow["worldName"];
					$permissions = $currentRow["permissions"];
					
					if(!is_array($permissions))
					{
						$this->groupsData[$groupName]["worlds"][$worldName] =  explode(",", $permissions);
					}
				}
				
				$result_mw->finalize();
			}
		}
		
		return $this->groupsData;
	}
	
	public function getUserData(PPUser $user, $isArray = false)
	{
	}
	
	public function setGroupData(PPGroup $group, array $groupData)
	{
	}
	
	public function setGroupsData(array $data)
	{
		if(!empty($this->groupsData))
		{
			$tempGroupsData = $this->groupsData;
			
			$inheritance = $tempGroupsData[$groupName]["inheritance"];
								
			if(is_array($inheritance)) 
			{
				$tempGroupsData[$groupName]["inheritance"] = implode(",", $inheritance);
			}
					
			$permissions = $tempGroupsData[$groupName]["permissions"];
					
			if(is_array($permissions))
			{
				$tempGroupsData[$groupName]["permissions"] = implode(",", $permissions);
			}
		}
	}

	public function setUserData(PPUser $user, array $data)
	{
	}
	
	public function close()
	{
		$this->db->close();
	}
}