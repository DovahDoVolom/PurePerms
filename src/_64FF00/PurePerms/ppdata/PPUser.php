<?php

namespace _64FF00\PurePerms\ppdata;

use _64FF00\PurePerms\PurePerms;

class PPUser
{	
	public function __construct(PurePerms $plugin, $name)
	{
		$this->plugin = $plugin;
		$this->name = $name;
	}
	
	public function getGroup()
	{
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function getPermissions()
	{
	}
	
	public function getPlayer()
	{
	}
	
	public function setGroup()
	{
	}
}