<?php

namespace _64FF00\PurePerms\providers;

use _64FF00\PurePerms\PurePerms;
use _64FF00\PurePerms\ppdata\PPGroup;
use _64FF00\PurePerms\ppdata\PPUser;

use pocketmine\IPlayer;

use pocketmine\utils\Config;

class DefaultProvider implements ProviderInterface
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
    
    private $groups;
    
    public function __construct(PurePerms $plugin)
    {
        $this->plugin = $plugin;
        
        $this->init();
    }
    
    public function init()
    {
        @mkdir($this->plugin->getDataFolder() . "players/", 0777, true);
        
        $this->plugin->saveResource("groups.yml");
        
        $this->groups = new Config($this->plugin->getDataFolder() . "groups.yml", Config::YAML, array(
        ));
    }
    
    public function getGroupData(PPGroup $group)
    {
        $groupName = $group->getName();
        
        if(isset($this->getGroupsData()[$groupName]) and is_array($this->getGroupsData(true)[$groupName]))
        {
            return $this->getGroupsData()[$groupName];
        }
    }
    
    public function getGroupsConfig()
    {
        return $this->groups;
    }
    
    public function getGroupsData()
    {
        return $this->groups->getAll();
    }
    
    public function getUserConfig(PPUser $user)
    {
        $userName = $user->getPlayer()->getName();
        
        if(!(file_exists($this->plugin->getDataFolder() . "players/" . strtolower($userName) . ".yml")))
        {
            return new Config($this->plugin->getDataFolder() . "players/" . strtolower($userName) . ".yml", Config::YAML, array(
                "userName" => $userName,
                "group" => $this->plugin->getDefaultGroup()->getName(),
                "permissions" => array(
                ),
                "worlds" => array(
                )
            ));
        }
        else
        {
            return new Config($this->plugin->getDataFolder() . "players/" . strtolower($userName) . ".yml", Config::YAML, array(
            ));
        }
    }
    
    public function getUserData(PPUser $user)
    {
        $userConfig = $this->getUserConfig($user);
        
        return $userConfig->getAll();
    }
    
    public function setGroupData(PPGroup $group, array $tempGroupData)
    {
        $groupName = $group->getName();
        
        $this->groups->set($groupName, $tempGroupData);
        
        $this->groups->save();
    }
    
    public function setGroupsData(array $tempGroupsData)
    {
        $this->groups->setAll($tempGroupsData);
        
        $this->groups->save();
    }

    public function setUserData(PPUser $user, array $tempUserData)
    {
        $userData = $this->getUserConfig($user);
        
        $userData->setAll($tempUserData);
            
        $userData->save();
    }
    
    public function close()
    {
    }
}