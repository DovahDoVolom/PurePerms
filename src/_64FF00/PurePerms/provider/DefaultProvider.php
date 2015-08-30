<?php

namespace _64FF00\PurePerms\provider;

use _64FF00\PurePerms\PurePerms;
use _64FF00\PurePerms\ppdata\PPGroup;
use _64FF00\PurePerms\ppdata\PPUser;

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
    
    private $userDataFolder, $groups;

    /**
     * @param PurePerms $plugin
     */
    public function __construct(PurePerms $plugin)
    {
        $this->plugin = $plugin;
        
        $this->init();
    }
    
    public function init()
    {
        $this->userDataFolder = $this->plugin->getDataFolder() . "players/";

        if(!file_exists($this->userDataFolder)) @mkdir($this->userDataFolder, 0777, true);
        
        $this->plugin->saveResource("groups.yml");
        
        $this->groups = new Config($this->plugin->getDataFolder() . "groups.yml", Config::YAML, array(
        ));
    }

    /**
     * @param PPGroup $group
     * @return mixed
     */
    public function getGroupData(PPGroup $group)
    {
        $groupName = $group->getName();
        
        if(!isset($this->getGroupsData()[$groupName]) || !is_array($this->getGroupsData()[$groupName]))
        {
            return [];
        }

        return $this->getGroupsData()[$groupName];
    }

    /**
     * @return mixed
     */
    public function getGroupsConfig()
    {
        return $this->groups;
    }

    /**
     * @return mixed
     */
    public function getGroupsData()
    {
        return $this->groups->getAll();
    }

    /**
     * @param PPUser $user
     * @return Config
     */
    public function getUserConfig(PPUser $user)
    {
        $userName = $user->getPlayer()->getName();
        
        if(!(file_exists($this->userDataFolder . strtolower($userName) . ".yml")))
        {
            return new Config($this->userDataFolder . strtolower($userName) . ".yml", Config::YAML, array(
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
            return new Config($this->userDataFolder . strtolower($userName) . ".yml", Config::YAML, array(
            ));
        }
    }

    /**
     * @param PPUser $user
     * @return mixed
     */
    public function getUserData(PPUser $user)
    {
        $userConfig = $this->getUserConfig($user);
        
        return $userConfig->getAll();
    }

    /**
     * @param PPGroup $group
     * @param array $tempGroupData
     */
    public function setGroupData(PPGroup $group, array $tempGroupData)
    {
        $groupName = $group->getName();
        
        $this->groups->set($groupName, $tempGroupData);
        
        $this->groups->save();
    }

    /**
     * @param array $tempGroupsData
     */
    public function setGroupsData(array $tempGroupsData)
    {
        $this->groups->setAll($tempGroupsData);
        
        $this->groups->save();
    }

    /**
     * @param PPUser $user
     * @param array $tempUserData
     */
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