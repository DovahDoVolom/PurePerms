<?php

namespace _64FF00\PurePerms;              

use pocketmine\utils\Config;                                                    

class PPMessages
{
	private $messages;
	
	public function __construct(PurePerms $plugin)
	{
		$this->plugin = $plugin;
		
		$this->loadMessages();
	}
	
	public function getMessage($node, ...$vars)
	{
		$msg = $this->messages->getNested($node);
		
		if($msg != null)
		{
			$number = 0;
			
			foreach($vars as $v)
			{			
				$msg = str_replace("%var$number%", $v, $msg);
				
				$number++;
			}
			
			return $msg;
		}
		
		return null;
	}
	
	public function getVersion()
	{
		return $this->messages->get("messages-version");
	}
	
	public function loadMessages()
	{
		$this->plugin->saveResource("messages.yml");
		
		$this->messages = new Config($this->plugin->getDataFolder() . "messages.yml", Config::YAML, array(
		));
		
		if($this->getVersion() < $this->plugin->getPPVersion())
		{
			$this->plugin->saveResource("messages.yml", true);
			
			$this->reloadMessages();
		}
	}
	
	public function reloadMessages()
	{
		$this->messages->reload();
	}
}