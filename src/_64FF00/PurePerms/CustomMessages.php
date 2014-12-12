<?php

namespace _64FF00\PurePerms;                                                                  

class CustomMessages
{
	public function __construct(PurePerms $plugin)
	{
		$this->plugin = $plugin;
		
		$this->loadMessages();
	}
	
	public function getMessage($node, ...$vars)
	{
	}
	
	public function loadMessages()
	{
	}
	
	public function reloadMessages()
	{
	}
}