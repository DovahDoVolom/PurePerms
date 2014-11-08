<?php

namespace _64FF00\PurePerms\ppdata;

use _64FF00\PurePerms\PurePerms;

class PPGroup
{
	public function __construct(PurePerms $plugin, $name)
	{
		$this->plugin = $plugin;
		$this->name = $name;
	}
	
	public function getUsers()
	{
	}
	
	public function isDefault()
	{
	}
}