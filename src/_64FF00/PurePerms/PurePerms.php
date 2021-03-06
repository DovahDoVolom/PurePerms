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

use pocketmine\OfflinePlayer;

use pocketmine\Player;

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
	private $attachments = [];
	
	private $config, $provider;
	
	public function onLoad()
	{
		$this->config = new PPConfig($this);
	}
	
	public function onEnable()
	{
		$this->registerCommands();
		
		$this->setProvider();
		
		$this->sortGroupPermissions();
		
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
	
	private function sortGroupPermissions()
	{
		foreach($this->getGroups() as $group)
		{
			$group->sortPermissions();
		}
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
	
	public function addGroup($groupName)
	{
		$groupsData = $this->provider->getGroupsData(true);
		
		if(!isset($groupsData[$groupName]))
		{
			$groupsData[$groupName] = array(
				"inheritance" => array(
				), 
				"permissions" => array(
				),
				"worlds" => array(
				)
			);
		}
		
		$this->provider->setGroupsData($groupsData);
	}
	
	public function getAttachment(Player $player)
	{
		if(!isset($this->attachments[$player->getName()]))
		{
			$this->attachments[$player->getName()] = $player->addAttachment($this);
		}
		
		return $this->attachments[$player->getName()];
	}
	
	public function getDefaultGroup()
	{		
		$defaultGroups = [];
		
		foreach($this->getGroups() as $defaultGroup)
		{
			if($defaultGroup->isDefault()) array_push($defaultGroups, $defaultGroup);
		}
		
		if(count($defaultGroups) == 1)
		{
			return $defaultGroups[0];
		}
		else
		{
			if(count($defaultGroups) > 1)
			{
				$this->getLogger()->warning("More than one default groups were declared in the groups file.");
			}
			elseif(count($defaultGroups) <= 0)
			{
				$this->getLogger()->warning("No default group was found in the groups file.");
				
				$defaultGroups = $this->getGroups();
			}
			
			$this->getLogger()->warning("Setting the default group automatically...");	
			
			foreach($defaultGroups as $defaultGroup)
			{
				if(count($defaultGroup->getInheritedGroups()) == 0)
				{
					$this->setDefaultGroup($defaultGroup);
						
					return $defaultGroup;
				}
			}
		}
	}
	
	public function getGroup($groupName)
	{
		if(empty($groupName)) return null;
		
		$group = new PPGroup($this, $groupName);
			
		if(empty($group->getData()))
		{
			return null;
		}
		
		return $group;
	}
	
	public function getGroups()
	{
		$result = [];
		
		foreach(array_keys($this->provider->getGroupsData(true)) as $groupName)
		{
			array_push($result, new PPGroup($this, $groupName));
		}
		
		return $result;
	}
	
	public function getPlayer($name)
	{
		$player = $this->getServer()->getPlayer($name);
		
		return $player instanceof Player ? $player : $this->getServer()->getOfflinePlayer($name);
	}
	
	public function getPPConfig()
	{
		return $this->config;
	}
	
	public function getProvider()
	{
		return $this->provider;
	}
	
	public function getUser(IPlayer $player)
	{
		return new PPUser($this, $player);
	}
	
	public function reload()
	{
		$this->config->reloadConfig();
		
		$this->provider->init();
		
		$this->sortGroupPermissions();
	}
	
	public function removeAttachment(Player $player)
	{
		$attachment = $this->getAttachment($player);
		
		$player->removeAttachment($attachment);
		
		unset($this->attachments[$player->getName()]);
	}
	
	public function removeAttachments()
	{
		foreach($this->attachments as $attachment)
		{
			unset($this->attachments[$attachment]);
		}
	}
	
	public function removeGroup($groupName)
	{
		$groupsData = $this->provider->getGroupsData(true);
		
		if(isset($groupsData[$groupName]))
		{
			unset($groupsData[$groupName]);
		}
		
		$this->provider->setGroupsData($groupsData);
	}
	
	public function setDefaultGroup(PPGroup $group)
	{
		foreach($this->getGroups() as $currentGroup)
		{
			$isDefault = $currentGroup->getNode("def-group");
			
			if($isDefault)
			{
				$currentGroup->removeNode("def-group");
			}
		}
		
		$group->setDefault();
	}
	
	public function setGroup(IPlayer $player, PPGroup $group, $levelName = null)
	{
		$this->getUser($player)->setGroup($group, $levelName);
		
		$this->updatePermissions($player, $levelName);
	}
	
	public function updatePermissions(Player $player, $levelName = null)
	{
		$attachment = $this->getAttachment($player);
		
		$originalPermissions = $this->getUser($player)->getPermissions($levelName);
		
		$permissions = [];
		
		foreach($originalPermissions as $permission)
		{
			$isNegative = substr($permission, 0, 1) === "-";
			
			if($isNegative) $permission = substr($permission, 1);
			
			$value = !$isNegative;
			
			$permissions[$permission] = $value;
		}
		
		$attachment->clearPermissions();

		$attachment->setPermissions($permissions);
	}
}