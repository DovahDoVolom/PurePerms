<?php

namespace _64FF00\PurePerms;

use _64FF00\PurePerms\commands\AddGroup;
use _64FF00\PurePerms\commands\FPerms;
use _64FF00\PurePerms\commands\Groups;
use _64FF00\PurePerms\commands\ListGPerms;
use _64FF00\PurePerms\commands\ListUPerms;
use _64FF00\PurePerms\commands\PPInfo;
use _64FF00\PurePerms\commands\PPReload;
use _64FF00\PurePerms\commands\RmGroup;
use _64FF00\PurePerms\commands\SetGPerm;
use _64FF00\PurePerms\commands\SetGroup;
use _64FF00\PurePerms\commands\SetUPerm;
use _64FF00\PurePerms\commands\UnsetGPerm;
use _64FF00\PurePerms\commands\UnsetUPerm;
use _64FF00\PurePerms\commands\UsrInfo;
use _64FF00\PurePerms\ppdata\PPGroup;
use _64FF00\PurePerms\ppdata\PPUser;
use _64FF00\PurePerms\provider\DefaultProvider;
use _64FF00\PurePerms\provider\ProviderInterface;
use _64FF00\PurePerms\provider\SQLite3Provider;

use pocketmine\IPlayer;

use pocketmine\permission\PermissionAttachment;

use pocketmine\Player;

use pocketmine\plugin\PluginBase;

class PurePerms extends PluginBase
{
    /* PurePerms by 64FF00 (xktiverz@gmail.com, @64ff00 for Twitter) */

    /*
          # #    #####  #       ####### #######   ###     ###
          # #   #     # #    #  #       #        #   #   #   #
        ####### #       #    #  #       #       #     # #     #
          # #   ######  #    #  #####   #####   #     # #     #
        ####### #     # ####### #       #       #     # #     #
          # #   #     #      #  #       #        #   #   #   #
          # #    #####       #  #       #         ###     ###

    */

    private $attachments = [];
    
    private $provider;
    
    public function onLoad()
    {
        $this->messages = new PPMessages($this);
        
        $this->saveDefaultConfig();
        
        if($this->getConfigValue("enable-multiworld-perms") == false)
        {
            $this->getLogger()->notice("Multiworld support is currently disabled.");
            $this->getLogger()->notice("Don't forget to set enable-multiworld-perms option in config.yml to true if you want to use per-world permissions!");
        }
        else
        {
            $this->getLogger()->notice("Multiworld support enabled.");
        }
    }
    
    public function onEnable()
    {
        $this->registerCommands();
        
        $this->setProvider();
        
        $this->cleanUpGroups();
        
        $this->updateAllPlayers();
        
        $this->getServer()->getPluginManager()->registerEvents(new PPListener($this), $this);
    }
    
    public function onDisable()
    {
        if($this->provider instanceof ProviderInterface) $this->provider->close();
    }
    
    private function cleanUpGroups()
    {
        foreach($this->getGroups() as $group)
        {
            $group->sortPermissions();
        }
    }
    
    private function registerCommands()
    {
        $commandMap = $this->getServer()->getCommandMap();
        
        $commandMap->register("addgroup", new AddGroup($this, "addgroup", $this->getMessage("cmds.addgroup.desc")));
        $commandMap->register("fperms", new FPerms($this, "fperms", $this->getMessage("cmds.fperms.desc")));
        $commandMap->register("groups", new Groups($this, "groups", $this->getMessage("cmds.groups.desc")));
        $commandMap->register("listgperms", new ListGPerms($this, "listgperms", $this->getMessage("cmds.listgperms.desc")));
        $commandMap->register("listuperms", new ListUPerms($this, "listuperms", $this->getMessage("cmds.listuperms.desc")));
        $commandMap->register("ppinfo", new PPInfo($this, "ppinfo", $this->getMessage("cmds.ppinfo.desc")));
        $commandMap->register("ppreload", new PPReload($this, "ppreload", $this->getMessage("cmds.ppreload.desc")));
        $commandMap->register("rmgroup", new RmGroup($this, "rmgroup", $this->messages->getMessage("cmds.rmgroup.desc")));
        $commandMap->register("setgperm", new SetGPerm($this, "setgperm", $this->getMessage("cmds.setgperm.desc")));
        $commandMap->register("setgroup", new SetGroup($this, "setgroup", $this->getMessage("cmds.setgroup.desc")));
        $commandMap->register("setuperm", new SetUPerm($this, "setuperm", $this->getMessage("cmds.setuperm.desc")));
        $commandMap->register("unsetgperm", new UnsetGPerm($this, "unsetgperm", $this->getMessage("cmds.unsetgperm.desc")));
        $commandMap->register("unsetuperm", new UnsetUPerm($this, "unsetuperm", $this->getMessage("cmds.unsetuperm.desc")));
        $commandMap->register("usrinfo", new UsrInfo($this, "usrinfo", $this->getMessage("cmds.usrinfo.desc")));
    }
    
    private function setProvider()
    {
        $providerName = $this->getConfigValue("data-provider");
        
        switch(strtolower($providerName))
        {
            case "sqlite3":
            
                $provider = new SQLite3Provider($this);
                
                $this->getLogger()->info("Set data provider to SQLite3.");
                
                break;
                
            case "yaml":
            
                $provider = new DefaultProvider($this);
                
                $this->getLogger()->info("Set data provider to YAML.");
                
                break;
                
            default:
                
                $this->getLogger()->warning("Provider $providerName does NOT exist. Setting the data provider to default.");
                
                $provider = new DefaultProvider($this);
                
                break;              
        }
        
        if(!isset($this->provider) || !($this->provider instanceof ProviderInterface))
        {
            $this->provider = $provider;
        }
    }
    
    /*
            #    ######  ### ### 
           # #   #     #  #  ### 
          #   #  #     #  #  ### 
         #     # ######   #   #  
         ####### #        #      
         #     # #        #  ### 
         #     # #       ### ###
    */

    /**
     * @param $groupName
     * @return bool
     */
    public function addGroup($groupName)
    {
        $groupsData = $this->provider->getGroupsData(true);
        
        if(isset($groupsData[$groupName])) return false;
        
        $groupsData[$groupName] = array(
            "inheritance" => array(
            ), 
            "permissions" => array(
            ),
            "worlds" => array(
            )
        );
            
        $this->provider->setGroupsData($groupsData);
        
        return true;
    }

    /**
     * @param Player $player
     */
    public function dumpPermissions(Player $player)
    {
        foreach($player->getEffectivePermissions() as $attachmentInfo)
        {
            $permission = $attachmentInfo->getPermission();
            
            $value = $attachmentInfo->getValue();
            
            $this->getLogger()->info("[" . $player->getName() . "] -> $permission : " . ($value ? "true" : "false"));
        }
        
        $this->getLogger()->info("...");
    }

    /**
     * @param Player $player
     * @return mixed
     */
    public function getAttachment(Player $player)
    {
        if(!isset($this->attachments[$player->getUniqueId()])) $this->attachments[$player->getUniqueId()] = $player->addAttachment($this);
        
        return $this->attachments[$player->getUniqueId()];
    }

    /**
     * @return mixed
     */
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
                $this->getLogger()->warning("More than one default group was declared in the groups file.");
            }
            elseif(count($defaultGroups) <= 0)
            {
                $this->getLogger()->warning("No default group was found in the groups file.");
                
                $defaultGroups = $this->getGroups();
            }
            
            $this->getLogger()->info("Setting the default group automatically.");  
            
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

    /**
     * @param Player $player
     * @return array
     */
    public function getEffectivePermissions(Player $player)
    {
        $permissions = [];
        
        foreach($player->getEffectivePermissions() as $attachmentInfo)
        {
            $permission = $attachmentInfo->getPermission();
            
            $value = $attachmentInfo->getConfigValue();
            
            $permissions[$permission] = $value;
        }
        
        ksort($permissions);
        
        return $permissions;
    }

    /**
     * @param $groupName
     * @return PPGroup|null
     */
    public function getGroup($groupName)
    {
        $group = new PPGroup($this, $groupName);
            
        if(empty($group->getData())) 
        {
            $this->getLogger()->warning("Group $groupName has invalid or corrupted data");
            
            return null;
        }
        
        return $group;
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        $result = [];
        
        foreach(array_keys($this->provider->getGroupsData(true)) as $groupName)
        {
            array_push($result, new PPGroup($this, $groupName));
        }
        
        return $result;
    }

    /**
     * @param $node
     * @param ...$vars
     * @return mixed
     */
    public function getMessage($node, ...$vars)
    {
        return $this->messages->getMessage($node, ...$vars);
    }

    /**
     * @param $name
     * @return Player
     */
    public function getPlayer($name)
    {
        $player = $this->getServer()->getPlayer($name);
        
        return $player instanceof Player ? $player : $this->getServer()->getOfflinePlayer($name);
    }

    /**
     * @return mixed
     */
    public function getPPVersion()
    {
        return $this->getDescription()->getVersion();
    }

    /**
     * @return mixed
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @param IPlayer $player
     * @return PPUser
     */
    public function getUser(IPlayer $player)
    {
        return new PPUser($this, $player);
    }

    /**
     * @param $key
     * @return null
     */
    public function getConfigValue($key)
    {
        $value = $this->getConfig()->getNested($key);
        
        if($value === null)
        {
            $this->getLogger()->warning("Key $key not found in config.yml.");
            
            return null;
        }
        
        return $value;
    }

    public function reload()
    {
        $this->reloadConfig();
        $this->saveDefaultConfig();
        
        $this->messages->reloadMessages();
        
        $this->registerCommands();
        
        $this->provider->init();
        
        $this->cleanUpGroups();
        
        $this->updateAllPlayers();
    }

    /**
     * @param Player $player
     */
    public function removeAttachment(Player $player)
    {
        $attachment = $this->getAttachment($player);
        
        $player->removeAttachment($attachment);
        
        unset($this->attachments[$player->getUniqueId()]);
    }
    
    public function removeAttachments()
    {
        $this->attachments = [];
    }

    /**
     * @param $groupName
     * @return bool
     */
    public function removeGroup($groupName)
    {
        $groupsData = $this->provider->getGroupsData(true);
        
        if(!isset($groupsData[$groupName])) return false;
        
        unset($groupsData[$groupName]);
        
        $this->provider->setGroupsData($groupsData);
        
        return true;
    }

    /**
     * @param PPGroup $group
     */
    public function setDefaultGroup(PPGroup $group)
    {
        foreach($this->getGroups() as $currentGroup)
        {
            $isDefault = $currentGroup->getNode("isDefault");
            
            if($isDefault)
            {
                $currentGroup->removeNode("isDefault");
            }
        }
        
        $group->setDefault();
    }

    /**
     * @param IPlayer $player
     * @param PPGroup $group
     * @param null $levelName
     */
    public function setGroup(IPlayer $player, PPGroup $group, $levelName = null)
    {
        $this->getUser($player)->setGroup($group, $levelName);
    }
    
    public function updateAllPlayers()
    {
        foreach($this->getServer()->getOnlinePlayers() as $player)
        {
            $this->updatePermissions($player, null);
            
            foreach($this->getServer()->getLevels() as $level)
            {
                $levelName = $level->getName();
                
                $this->updatePermissions($player, $levelName);
            }
        }
    }

    /**
     * @param PermissionAttachment $attachment
     */
    public function unsetAll(PermissionAttachment $attachment)
    {
        foreach($this->getServer()->getPluginManager()->getPermissions() as $permission)
        {
            $attachment->unsetPermission($permission);
        }
    }

    /**
     * @param IPlayer $player
     * @param null $levelName
     */
    public function updatePermissions(IPlayer $player, $levelName = null)
    {
        if($player instanceof Player)
        {
            $attachment = $this->getAttachment($player);
            
            $attachment->clearPermissions();

            $this->unsetAll($attachment);
                
            $permissions = [];

            foreach($this->getUser($player)->getPermissions($levelName) as $permission)
            {
                if($permission == "*")
                {                 
                    foreach($this->getServer()->getPluginManager()->getPermissions() as $tmp)
                    {
                        $permissions[$tmp->getName()] = true;
                    }
                }
                else
                {
                    $isNegative = substr($permission, 0, 1) === "-";
                        
                    if($isNegative) $permission = substr($permission, 1);
                        
                    $value = !$isNegative;
                        
                    $permissions[$permission] = $value;
                }
            }
            
            ksort($permissions);
            
            $attachment->setPermissions($permissions);
        }
    }
}
