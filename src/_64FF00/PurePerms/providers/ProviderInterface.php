<?php

namespace _64FF00\PurePerms\providers;

interface ProviderInterface
{
	public function getGroupData();
	
	public function getUserData();
	
	public function setGroupData();
	
	public function setUserData();
}