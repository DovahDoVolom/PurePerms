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

    private $attachments = [], $groups = [];
    
    private $provider;
    
    public function onLoad()
    {
        $this->messages = new PPMessages($this);
        
        $this->saveDefaultConfig();
        
        if($this->getConfigValue("enable-multiworld-perms") == false)
        {
            $this->getLogger()->notice($this->getMessage("logger_messages.onEnable_01"));
            $this->getLogger()->notice($this->getMessage("logger_messages.onEnable_02"));
        }
        else
        {
            $this->getLogger()->notice($this->getMessage("logger_messages.onEnable_03"));
        }
    }
    
    public function onEnable()
    {
        $this->registerCommands();
        
        $this->setProvider();
        
        $this->updateAllPlayers();
        
        $this->getServer()->getPluginManager()->registerEvents(new PPListener($this), $this);
    }

    public function onDisable()
    {
        if($this->isValidProvider()) $this->provider->close();
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

    /**
     * @param bool $onEnable
     */
    private function setProvider($onEnable = true)
    {
        $providerName = $this->getConfigValue("data-provider");
        
        switch(strtolower($providerName))
        {
            case "sqlite3":
            
                $provider = new SQLite3Provider($this);
                
                if($onEnable == true) $this->getLogger()->info($this->getMessage("logger_messages.setProvider_SQLite3"));
                
                break;
                
            case "yaml":
            
                $provider = new DefaultProvider($this);

                if($onEnable == true) $this->getLogger()->info($this->getMessage("logger_messages.setProvider_YAML"));
                
                break;
                
            default:

                $provider = new DefaultProvider($this);

                if($onEnable == true) $this->getLogger()->warning($this->getMessage("logger_messages.setProvider_NotFound"));
                
                break;              
        }

        if(!$this->isValidProvider()) $this->provider = $provider;
        
        $this->loadGroups();
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
        $groupsData = $this->getProvider()->getGroupsData(true);
        
        if(isset($groupsData[$groupName])) return false;
        
        $groupsData[$groupName] = [
            "isDefault" => false,
            "inheritance" => [
            ],
            "permissions" => [
            ],
            "worlds" => [
            ]
        ];
            
        $this->getProvider()->setGroupsData($groupsData);
        
        return true;
    }

    /**
     * @param Player $player
     */
    public function dumpPermissions(Player $player)
    {
        $this->getLogger()->notice("--- List of all permissions from " . $player->getName() . " ---");

        foreach($this->getEffectivePermissions() as $permission => $value)
        {
            $this->getLogger()->notice("- " . $permission . " : " . ($value ? "true" : "false"));
        }
    }

    /**
     * @param Player $player
     * @return mixed
     */
    public function getAttachment(Player $player)
    {
        $uuid = $player->getUniqueId();

        if(!isset($this->attachments[$uuid])) $this->attachments[$uuid] = $player->addAttachment($this);

        return $this->attachments[$uuid];
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
            $this->getLogger()->warning($this->getMessage("logger_messages.getConfigValue_01", $key));

            return null;
        }

        return $value;
    }

    /**
     * @param $permission
     * @return array
     */
    public function getChildNodes($tempNode)
    {
        $result = [];

        $permission = $this->getServer()->getPluginManager()->getPermission($tempNode);

        $childNodes = $permission->getChildren();

        if($childNodes != [])
        {
            foreach($childNodes as $childNode => $value)
            {
                $result[] = $childNode;
            }
        }

        return $result;
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
                $this->getLogger()->warning($this->getMessage("logger_messages.getDefaultGroup_01"));
            }
            elseif(count($defaultGroups) <= 0)
            {
                $this->getLogger()->warning($this->getMessage("logger_messages.getDefaultGroup_02"));
                
                $defaultGroups = $this->getGroups();
            }
            
            $this->getLogger()->info($this->getMessage("logger_messages.getDefaultGroup_03"));
            
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
        if(!isset($this->groups[$groupName]))
        {
            $this->getLogger()->warning($this->getMessage("logger_messages.getGroup_01", $groupName));

            return null;
        }

        $group = $this->groups[$groupName];
            
        if(empty($group->getData()))
        {
            $this->getLogger()->warning($this->getMessage("logger_messages.getGroup_02", $groupName));
            
            return null;
        }
        
        return $group;
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        return $this->groups;
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
     * @param IPlayer $player
     * @param $levelName
     * @return array
     */
    public function getPermissions(IPlayer $player, $levelName)
    {
        $user = $this->getUser($player);
        $group = $user->getGroup($levelName);

        return array_merge($group->getGroupPermissions($levelName), $user->getUserPermissions($levelName));
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
        $version = $this->getDescription()->getVersion();

        return $version;
    }

    /**
     * @return ProviderInterface
     */
    public function getProvider()
    {
        if(!$this->isValidProvider()) $this->setProvider(false);

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
     * @return bool
     */
    public function isValidProvider()
    {
        if(!isset($this->provider) || $this->provider == null || !($this->provider instanceof ProviderInterface)) return false;

        return true;
    }
    
    public function loadGroups()
    {
        if($this->isValidProvider())
        {
            foreach(array_keys($this->getProvider()->getGroupsData(true)) as $groupName)
            {
                $this->groups[$groupName] = new PPGroup($this, $groupName);
            }
            
            $this->sortGroupPermissions();
        }
    }

    public function reload()
    {
        $this->reloadConfig();
        $this->saveDefaultConfig();
        
        $this->messages->reloadMessages();

        if(!$this->isValidProvider()) $this->setProvider(false);

        $this->provider->init();
        
        $this->updateAllPlayers();
    }

    /**
     * @param Player $player
     */
    public function removeAttachment(Player $player)
    {
        $uuid = $player->getUniqueId();

        if(isset($this->attachments[$uuid]) and $this->attachments[$uuid] instanceof PermissionAttachment)
        {
            $player->removeAttachment($this->attachments[$uuid]);

            unset($this->attachments[$player->getUniqueId()]);
        }
    }

    /**
     * @param $groupName
     * @return bool
     */
    public function removeGroup($groupName)
    {
        $groupsData = $this->getProvider()->getGroupsData(true);
        
        if(!isset($groupsData[$groupName])) return false;
        
        unset($groupsData[$groupName]);
        
        $this->getProvider()->setGroupsData($groupsData);
        
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
    
    public function sortGroupPermissions()
    {
        foreach($this->getGroups() as $group)
        {
            $group->sortPermissions();
        }
    }
    
    public function updateAllPlayers()
    {
        foreach($this->getServer()->getOnlinePlayers() as $player)
        {
            $this->updatePermissions($player, null);

            if($this->getConfigValue("enable-multiworld-perms") == true)
            {
                foreach ($this->getServer()->getLevels() as $level)
                {
                    $levelName = $level->getName();

                    $this->updatePermissions($player, $levelName);
                }
            }
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

            $permissions = [];

            foreach($this->getPermissions($player, $levelName) as $permission)
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

            $attachment->setPermissions($permissions);
        }
    }
}
