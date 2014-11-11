<?php

namespace _64FF00\PurePerms\providers;

use _64FF00\PurePerms\ppdata\PPGroup;
use _64FF00\PurePerms\ppdata\PPUser;

interface ProviderInterface
{
	public function init();
	
	public function getGroupData(PPGroup $group);
	
	public function getGroupsData($isArray = false);
	
	public function getUserData(PPUser $user, $isArray = false);
	
	public function setGroupData(PPGroup $group, array $groupData);
	
	public function setGroupsData(array $data);

	public function setUserData(PPUser $user, array $data);
	
	public function close();
}