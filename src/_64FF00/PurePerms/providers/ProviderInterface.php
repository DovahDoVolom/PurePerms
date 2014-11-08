<?php

namespace _64FF00\PurePerms\providers;

use _64FF00\PurePerms\ppdata\PPUser;

interface ProviderInterface
{
	public function init();
	
	public function getGroupData($groupName);
	
	public function getGroupsData();
	
	public function getUserData(PPUser $user);
	
	public function setGroupData($groupName, array $groupData);
	
	public function setGroupsData(array $data);

	public function setUserData(PPUser $user, array $data);
}