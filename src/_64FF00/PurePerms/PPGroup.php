<?php

namespace _64FF00\PurePerms;

class PPGroup
{
    /*
        PurePerms by 64FF00 (Twitter: @64FF00)

          888  888    .d8888b.      d8888  8888888888 8888888888 .d8888b.   .d8888b.
          888  888   d88P  Y88b    d8P888  888        888       d88P  Y88b d88P  Y88b
        888888888888 888          d8P 888  888        888       888    888 888    888
          888  888   888d888b.   d8P  888  8888888    8888888   888    888 888    888
          888  888   888P "Y88b d88   888  888        888       888    888 888    888
        888888888888 888    888 8888888888 888        888       888    888 888    888
          888  888   Y88b  d88P       888  888        888       Y88b  d88P Y88b  d88P
          888  888    "Y8888P"        888  888        888        "Y8888P"   "Y8888P"
    */

    private $name, $plugin;

    private $parents = [];

    /**
     * @param PurePerms $plugin
     * @param $name
     */
    public function __construct(PurePerms $plugin, $name)
    {
        $this->plugin = $plugin;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * @param PPGroup $group
     * @return bool
     */
    public function addParent(PPGroup $group)
    {
        $tempGroupData = $this->getData();

        if($this == $group || in_array($this->getName(), $group->getParentGroups()))
            return false;

        $tempGroupData["inheritance"][] = $group->getName();

        $this->setData($tempGroupData);

        $this->plugin->updatePlayersInGroup($this);

        return true;
    }

    /**
     * @param $levelName
     */
    public function createWorldData($levelName)
    {
        if(!isset($this->getData()["worlds"][$levelName]))
        {
            $tempGroupData = $this->getData();

            $tempGroupData["worlds"][$levelName] = [
                "isDefault" => false,
                "permissions" => [
                ]
            ];

            $this->setData($tempGroupData);
        }
    }

    /**
     * @return mixed
     */
    public function getAlias()
    {
        if($this->getNode("alias") === null)
            return $this->name;

        return $this->getNode("alias");
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->plugin->getProvider()->getGroupData($this);
    }

    /**
     * @param null $levelName
     * @return array
     */
    public function getGroupPermissions($levelName = null)
    {
        $permissions = $levelName !== null ? $this->getWorldData($levelName)["permissions"] : $this->getNode("permissions");

        if(!is_array($permissions))
        {
            $this->plugin->getLogger()->critical("Invalid 'permissions' node given to " .  __METHOD__);

            return [];
        }

        /** @var PPGroup $parentGroup */
        foreach($this->getParentGroups() as $parentGroup)
        {
            $parentPermissions = $parentGroup->getGroupPermissions($levelName);

            if($parentPermissions === null)
                $parentPermissions = [];

            // Fixed by @mad-hon (https://github.com/mad-hon) / Tysm! :D
            $permissions = array_merge($parentPermissions, $permissions);
        }

        return $permissions;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $node
     * @return null|mixed
     */
    public function getNode($node)
    {
        if(!isset($this->getData()[$node])) return null;
        
        return $this->getData()[$node];
    }

    /**
     * @return PPGroup[]
     */
    public function getParentGroups()
    {
        if($this->parents === [])
        {
            if(!is_array($this->getNode("inheritance")))
            {
                $this->plugin->getLogger()->critical("Invalid 'inheritance' node given to " . __METHOD__);

                return [];
            }

            foreach($this->getNode("inheritance") as $parentGroupName)
            {
                $parentGroup = $this->plugin->getGroup($parentGroupName);

                if($parentGroup !== null)
                    $this->parents[] = $parentGroup;
            }
        }

        return $this->parents;
    }

    /**
     * @param $levelName
     * @return null
     */
    public function getWorldData($levelName)
    {
        if($levelName === null)
            return null;
        
        $this->createWorldData($levelName);
            
        return $this->getData()["worlds"][$levelName];
    }

    /**
     * @param $levelName
     * @param $node
     * @return null
     */
    public function getWorldNode($levelName, $node)
    {
        if(!isset($this->getWorldData($levelName)[$node])) return null;

        return $this->getWorldData($levelName)[$node];
    }

    /**
     * @param null $levelName
     * @return bool
     */
    public function isDefault($levelName = null)
    {
        if($levelName === null)
        {
            return ($this->getNode("isDefault") === true);
        }
        else
        {
            return ($this->getWorldData($levelName)["isDefault"] === true);
        }
    }

    /**
     * @param $node
     */
    public function removeNode($node)
    {
        $tempGroupData = $this->getData();
        
        if(isset($tempGroupData[$node]))
        {               
            unset($tempGroupData[$node]);   
            
            $this->setData($tempGroupData);
        }
    }

    /**
     * @param PPGroup $group
     * @return bool
     */
    public function removeParent(PPGroup $group)
    {
        $tempGroupData = $this->getData();

        $tempGroupData["inheritance"] = array_diff($tempGroupData["inheritance"], [$group->getName()]);

        $this->setData($tempGroupData);

        $this->plugin->updatePlayersInGroup($this);

        return true;
    }

    /**
     * @param $levelName
     * @param $node
     */
    public function removeWorldNode($levelName, $node)
    {
        $worldData = $this->getWorldData($levelName);

        if(isset($worldData[$node]))
        {
            unset($worldData[$node]);

            $this->setWorldData($levelName, $worldData);
        }
    }

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->plugin->getProvider()->setGroupData($this, $data);
    }

    /**
     * @param null $levelName
     */
    public function setDefault($levelName = null)
    {
        if($levelName === null)
        {
            $this->setNode("isDefault", true);
        }
        else
        {
            $worldData = $this->getWorldData($levelName);

            $worldData["isDefault"] = true;

            $this->setWorldData($levelName, $worldData);
        }
    }

    /**
     * @param string $permission
     * @param string|null $levelName
     *
     * @return bool
     */
    public function setGroupPermission($permission, $levelName = null)
    {
        if($levelName == null)
        {
            $tempGroupData = $this->getData();
                    
            $tempGroupData["permissions"][] = $permission;
            
            $this->setData($tempGroupData);
        }
        else
        {
            $worldData = $this->getWorldData($levelName);
            
            $worldData["permissions"][] = $permission;
            
            $this->setWorldData($levelName, $worldData);
        }

        $this->plugin->updatePlayersInGroup($this);

        return true;
    }

    /**
     * @param $node
     * @param $value
     */
    public function setNode($node, $value)
    {
        $tempGroupData = $this->getData();
        
        $tempGroupData[$node] = $value;
            
        $this->setData($tempGroupData);
    }

    /**
     * @param $levelName
     * @param array $worldData
     */
    public function setWorldData($levelName, array $worldData)
    {
        if(isset($this->getData()["worlds"][$levelName]))
        {
            $tempGroupData = $this->getData();
            
            $tempGroupData["worlds"][$levelName] = $worldData;
                
            $this->setData($tempGroupData);
        }
    }

    /**
     * @param $levelName
     * @param $node
     * @param $value
     */
    public function setWorldNode($levelName, $node, $value)
    {
        $worldData = $this->getWorldData($levelName);

        $worldData[$node] = $value;

        $this->setWorldData($levelName, $worldData);
    }

    public function sortPermissions()
    {
        $tempGroupData = $this->getData();
            
        if(isset($tempGroupData["permissions"]))
        {
            $tempGroupData["permissions"] = array_unique($tempGroupData["permissions"]);

            sort($tempGroupData["permissions"]);
        }
        
        $isMultiWorldPermsEnabled = $this->plugin->getConfigValue("enable-multiworld-perms");
        
        if($isMultiWorldPermsEnabled and isset($tempGroupData["worlds"]))
        {
            foreach($this->plugin->getServer()->getLevels() as $level)
            {
                $levelName = $level->getName();
                        
                if(isset($tempGroupData["worlds"][$levelName]))
                {
                    $tempGroupData["worlds"][$levelName]["permissions"] = array_unique($tempGroupData["worlds"][$levelName]["permissions"]);

                    sort($tempGroupData["worlds"][$levelName]["permissions"]);
                }
            }
        }
        
        $this->setData($tempGroupData);
    }

    /**
     * @param $permission
     * @param null $levelName
     * @return bool
     */
    public function unsetGroupPermission($permission, $levelName = null)
    {
        if($levelName == null)
        {
            $tempGroupData = $this->getData();
                    
            if(!in_array($permission, $tempGroupData["permissions"])) return false;

            $tempGroupData["permissions"] = array_diff($tempGroupData["permissions"], [$permission]);
            
            $this->setData($tempGroupData);
        }
        else
        {
            $worldData = $this->getWorldData($levelName);
            
            if(!in_array($permission, $worldData["permissions"])) return false;
            
            $worldData["permissions"] = array_diff($worldData["permissions"], [$permission]);
            
            $this->setWorldData($levelName, $worldData);
        }

        $this->plugin->updatePlayersInGroup($this);

        return true;
    }
}