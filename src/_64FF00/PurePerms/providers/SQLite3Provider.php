<?php

namespace _64FF00\PurePerms\providers;

use _64FF00\PurePerms\PurePerms;
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
	}
	
	public function getGroupData(PPGroup $group)
	{
	}
	
	public function getGroupsData()
	{
	}
	
	public function getUserData(PPUser $user)
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
	}
}