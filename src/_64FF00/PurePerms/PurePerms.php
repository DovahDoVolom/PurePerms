<?php

namespace _64FF00\PurePerms;

use _64FF00\PurePerms\commands\AddGroup;
use _64FF00\PurePerms\commands\GroupList;
use _64FF00\PurePerms\commands\PPInfo;
use _64FF00\PurePerms\commands\PPReload;
use _64FF00\PurePerms\commands\RemoveGroup;
use _64FF00\PurePerms\commands\SetGPerm;
use _64FF00\PurePerms\commands\SetGroup;
use _64FF00\PurePerms\commands\SetUPerm;
use _64FF00\PurePerms\commands\UnsetGPerm;
use _64FF00\PurePerms\commands\UnsetUPerm;
use _64FF00\PurePerms\ppdata\PPGroup;
use _64FF00\PurePerms\ppdata\PPUser;
use _64FF00\PurePerms\providers\DefaultProvider;
use _64FF00\PurePerms\providers\SQLite3Provider;

use pocketmine\IPlayer;

use pocketmine\plugin\PluginBase;

/*
      # #    #####  #       ####### #######   ###     ###   
      # #   #     # #    #  #       #        #   #   #   #  
    ####### #       #    #  #       #       #     # #     # 
      # #   ######  #    #  #####   #####   #     # #     # 
    ####### #     # ####### #       #       #     # #     # 
      # #   #     #      #  #       #        #   #   #   #  
      # #    #####       #  #       #         ###     ###                                        
                                                                                   
*/
class PurePerms extends PluginBase
{
	private $config, $provider;
	
	public function onLoad()
	{
		$this->config = new PPConfig($this);
	}
	
	public function onEnable()
	{
		$this->registerCommands();
		
		$this->setProvider();
		
		$this->getServer()->getPluginManager()->registerEvents(new PPListener($this), $this);
	}
	
	private function registerCommands()
	{
		$commandMap = $this->getServer()->getCommandMap();
		
		$this->getLogger()->info("Registering PurePerms commands...");
		
		$commandMap->register("addgroup", new AddGroup($this, "addgroup", "Adds a new group."));
		$commandMap->register("grouplist", new GroupList($this, "grouplist", "Allows you to see a list of all groups."));
		$commandMap->register("ppinfo", new PPInfo($this, "ppinfo", "Shows the info of the PurePerms."));
		$commandMap->register("ppreload", new PPReload($this, "ppreload", "Reloads all PurePerms configurations."));
		$commandMap->register("removegroup", new RemoveGroup($this, "removegroup", "Removes a group."));
		$commandMap->register("setgperm", new SetGPerm($this, "setgperm", "Adds a permission to the group."));
		$commandMap->register("setgroup", new SetGroup($this, "setgroup", "Sets group for the user."));
		$commandMap->register("setuperm", new SetUPerm($this, "setuperm", "Adds a permission to the user."));
		$commandMap->register("unsetgperm", new UnsetGPerm($this, "unsetgperm", "Removes a permission from the group."));
		$commandMap->register("unsetuperm", new UnsetUPerm($this, "unsetuperm", "Removes a permission from the user."));		
	}
	
	private function setProvider()
	{
		$providerName = $this->config->getValue("data-provider");
		
		switch(strtolower($providerName))
		{
			case "sqlite3":
			
				$this->provider = new SQLite3Provider($this);
				
				break;
				
			case "yaml":
			
				$this->provider = new DefaultProvider($this);
				
				break;
				
			default:
				
				$this->getLogger()->warning("Provider $providerName does NOT exist. Setting the data provider to default.");
				
				$this->provider = new DefaultProvider($this);
				
				break;				
		}
		
		$this->getLogger()->info("Set data provider to " . strtoupper($providerName) . ".");
	}
	
	/*	
	
	       #    ######  ###    ### 
          # #   #     #  #     ### 
         #   #  #     #  #     ### 
        #     # ######   #      #  
        ####### #        #         
        #     # #        #     ### 
        #     # #       ###    ###
	  
	*/
	
	public function createGroup()
	{
	}
	
	public function getDefaultGroup()
	{
	}
	
	public function getGroup()
	{
	}
	
	public function getGroups()
	{
	}
	
	public function getProvider()
	{
		return $this->provider;
	}
	
	public function getUser(IPlayer $player)
	{
		return new PPUser($this, $player->getName());
	}
	
	public function reload()
	{
		$this->config->reloadConfig();
		
		$this->provider->init();
	}
	
	public function removeGroup()
	{
	}
	
	public function setDefault()
	{
	}
}