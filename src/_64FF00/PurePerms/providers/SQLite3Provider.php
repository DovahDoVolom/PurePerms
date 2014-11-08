<?php

namespace _64FF00\PurePerms\providers;

use _64FF00\PurePerms\PurePerms;
use _64FF00\PurePerms\ppdata\PPUser;

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
	
	public function getGroupData($groupName)
	{
	}
	
	public function getGroupsData()
	{
	}
	
	public function getUserData(PPUser $user)
	{
	}
	
	public function setGroupData($groupName, array $groupData)
	{
	}
	
	public function setGroupsData(array $data)
	{
	}

	public function setUserData(PPUser $user, array $data)
	{
	}
}