<?php

namespace _64FF00\PurePerms\ppdata;

use _64FF00\PurePerms\event\PPGroupChangedEvent;
use _64FF00\PurePerms\PurePerms;
use _64FF00\PurePerms\ppdata\PPGroup;

use pocketmine\IPlayer;

class PPUser implements PPDataInterface
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

    /**
     * @param PurePerms $plugin
     * @param IPlayer $player
     */
    public function __construct(PurePerms $plugin, IPlayer $player)
    {
        $this->player = $player;
        $this->plugin = $plugin;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->plugin->getProvider()->getUserData($this);
    }

    /**
     * @param null $levelName
     * @return PPGroup|null
     */
    public function getGroup($levelName = null)
    {
        $groupName = $levelName != null ? $this->getWorldData($levelName)["group"] : $this->getNode("group");

        if(!isset($groupName)) return null;
        
        $group = $this->plugin->getGroup($groupName);
        
        if($group == null)
        {
            $group = $this->plugin->getDefaultGroup();
            
            $this->setGroup($group, $levelName);
        }
        
        return $group;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->player->getName();
    }

    /**
     * @param $node
     * @return null
     */
    public function getNode($node)
    {
        if(!isset($this->getData()[$node])) return null;

        return $this->getData()[$node];
    }

    /**
     * @return IPlayer
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * @param null $levelName
     * @return array|null
     */
    public function getUserPermissions($levelName = null)
    {
        $permissions = $levelName != null ? $this->getWorldData($levelName)["permissions"] : $this->getNode("permissions");
        
        if(!is_array($permissions))
        {
            $this->plugin->getLogger()->critical("Invalid 'permissions' node given to " . __METHOD__);
            
            return [];
        }
        
        return $permissions;
    }

    /**
     * @param $levelName
     * @return null
     */
    public function getWorldData($levelName)
    {
        if($levelName == null) return null;
        
        if(!isset($this->getData()["worlds"][$levelName]))
        {
            $tempUserData = $this->getData();
            
            $tempUserData["worlds"][$levelName] = [
                "group" => $this->plugin->getDefaultGroup()->getName(),
                "permissions" => [
                ]
            ];
                
            $this->setData($tempUserData);
        }
            
        return $this->getData()["worlds"][$levelName];
    }

    /**
     * @param $node
     */
    public function removeNode($node)
    {
        $tempUserData = $this->getData();
        
        if(isset($tempUserData[$node]))
        {               
            unset($tempUserData[$node]);
            
            $this->setData($tempUserData);
        }
    }

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->plugin->getProvider()->setUserData($this, $data);
    }

    /**
     * @param PPGroup $group
     * @param $levelName
     */
    public function setGroup(PPGroup $group, $levelName)
    {
        if($levelName == null)
        {
            $this->setNode("group", $group->getName());
        }
        else
        {
            $worldData = $this->getWorldData($levelName);

            $worldData["group"] = $group->getName();

            $this->setWorldData($levelName, $worldData);
        }

        $event = new PPGroupChangedEvent($this->plugin, $this->player, $group);

        $this->plugin->getServer()->getPluginManager()->callEvent($event);
    }

    /**
     * @param $node
     * @param $value
     */
    public function setNode($node, $value)
    {
        $tempUserData = $this->getData();

        $tempUserData[$node] = $value;

        $this->setData($tempUserData);
    }

    /**
     * @param $permission
     * @param null $levelName
     */
    public function setUserPermission($permission, $levelName = null)
    {
        if($levelName == null)
        {
            $tempUserData = $this->getData();
                    
            $tempUserData["permissions"][] = $permission;
            
            $this->setData($tempUserData);
        }
        else
        {
            $worldData = $this->getWorldData($levelName);
            
            $worldData["permissions"][] = $permission;
            
            $this->setWorldData($levelName, $worldData);
        }
        
        $this->plugin->updatePermissions($this->player, $levelName);
    }

    /**
     * @param $levelName
     * @param array $worldData
     */
    public function setWorldData($levelName, array $worldData)
    {
        if(isset($this->getData()["worlds"][$levelName]))
        {
            $tempUserData = $this->getData();
            
            $tempUserData["worlds"][$levelName] = $worldData;
                
            $this->setData($tempUserData);
        }
    }

    /**
     * @param $permission
     * @param null $levelName
     */
    public function unsetUserPermission($permission, $levelName = null)
    {
        if($levelName == null)
        {
            $tempUserData = $this->getData();
            
            if(!in_array($permission, $tempUserData["permissions"])) return;

            $tempUserData["permissions"] = array_diff($tempUserData["permissions"], [$permission]);
            
            $this->setData($tempUserData);
        }
        else
        {
            $worldData = $this->getWorldData($levelName);
            
            if(!in_array($permission, $worldData["permissions"])) return;
            
            $worldData["permissions"] = array_diff($worldData["permissions"], [$permission]);
            
            $this->setWorldData($levelName, $worldData);
        }
        
        $this->plugin->updatePermissions($this->player, $levelName);
    }
}