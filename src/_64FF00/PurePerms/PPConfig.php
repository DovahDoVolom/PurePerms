<?php
namespace _64FF00\PurePerms;

use pocketmine\utils\Config;

class PPConfig
{
	private $config;
	
	public function __construct(PurePerms $plugin)
	{
		$this->plugin = $plugin;
		
		$this->loadConfig();
	}
	
	public function getValue($key)
	{
		if(!$this->config->get($key))
		{
			$this->plugin->getLogger()->warning("Key $key not found in config.yml. (NULL)");
			
			return null;
		}
		
		return $this->config->get($key);
	}
	
	public function loadConfig()
	{
		$this->plugin->saveDefaultConfig();
		
		$this->config = $this->plugin->getConfig();
	}
	
	public function reloadConfig()
	{	
		$this->plugin->reloadConfig();
		
		$this->loadConfig();
	}
}