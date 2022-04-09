<?php

namespace _64FF00\PurePerms\DataManager;

use _64FF00\PurePerms\PPGroup;
use _64FF00\PurePerms\PurePerms;
use _64FF00\PurePerms\EventManager\PPRankChangedEvent;

use pocketmine\player\IPlayer;

class UserDataManager
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

    /**
     * @param PurePerms $plugin
     */
    public function __construct(PurePerms $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @param IPlayer $player
     * @return array
     */
    public function getData(IPlayer $player)
    {
        return $this->plugin->getProvider()->getPlayerData($player);
    }

    public function getExpDate(IPlayer $player, $WorldName = null)
    {
        $expDate = $WorldName !== null ? $this->getWorldData($player, $WorldName)["expTime"] : $this->getNode($player, "expTime");
        // TODO
        return $expDate;
    }

    /**
     * @param IPlayer $player
     * @param null $WorldName
     * @return PPGroup|null
     */
    public function getGroup(IPlayer $player, $WorldName = null)
    {
        $groupName = $WorldName !== null ? $this->getWorldData($player, $WorldName)["group"] : $this->getNode($player, "group");
        $group = $this->plugin->getGroup($groupName);
        // TODO: ...
        if($group === null)
        {
            $this->plugin->getLogger()->critical("Invalid group name found in " . $player->getName() . "'s player data (World: " . ($WorldName === null ? "GLOBAL" : $WorldName) . ")");
            $this->plugin->getLogger()->critical("Restoring the group data to 'default'");
            $defaultGroup = $this->plugin->getDefaultGroup($WorldName);
            $this->setGroup($player, $defaultGroup, $WorldName);
            return $defaultGroup;
        }

        return $group;
    }

    /**
     * @param IPlayer $player
     * @param $node
     * @return null|mixed
     */
    public function getNode(IPlayer $player, $node)
    {
        $userData = $this->getData($player);
        if(!isset($userData[$node]))
            return null;
        return $userData[$node];
    }

    /**
     * @param null $WorldName
     * @return array
     */
    public function getUserPermissions(IPlayer $player, $WorldName = null)
    {
        $permissions = $WorldName != null ? $this->getWorldData($player, $WorldName)["permissions"] : $this->getNode($player, "permissions");
        if(!is_array($permissions))
        {
            $this->plugin->getLogger()->critical("Invalid 'permissions' node given to " . __METHOD__ . '()');
            return [];
        }
        return $permissions;
    }

    /**
     * @param IPlayer $player
     * @param $WorldName
     * @return array
     */
    public function getWorldData(IPlayer $player, $WorldName)
    {
        if($WorldName === null)
            $WorldName = $this->plugin->getServer()->getWorldManager()->getDefaultWorld()->getDisplayName();
        if(!isset($this->getData($player)["worlds"][$WorldName]))
            return [
                "group" => $this->plugin->getDefaultGroup($WorldName)->getName(),
                "permissions" => [
                ],
                "expTime" => -1
            ];
        return $this->getData($player)["worlds"][$WorldName];
    }

    public function removeNode(IPlayer $player, $node)
    {
        $tempUserData = $this->getData($player);
        if(isset($tempUserData[$node]))
        {
            unset($tempUserData[$node]);
            $this->setData($player, $tempUserData);
        }
    }

    /**
     * @param IPlayer $player
     * @param array $data
     */
    public function setData(IPlayer $player, array $data)
    {
        $this->plugin->getProvider()->setPlayerData($player, $data);
    }

    /**
     * @param IPlayer $player
     * @param PPGroup $group
     * @param $WorldName
     * @param int $time
     */
    public function setGroup(IPlayer $player, PPGroup $group, $WorldName, $time = -1)
    {
        if($WorldName === null)
        {
            $this->setNode($player, "group", $group->getName());
            $this->setNode($player, "expTime", $time);
        }
        else
        {
            $worldData = $this->getWorldData($player, $WorldName);
            $worldData["group"] = $group->getName();
            $worldData["expTime"] = $time;
            $this->setWorldData($player, $WorldName, $worldData);
        }

        $event = new PPRankChangedEvent($this->plugin, $player, $group, $WorldName);

        $event->call();
    }

    /**
     * @param IPlayer $player
     * @param $node
     * @param $value
     */
    public function setNode(IPlayer $player, $node, $value)
    {
        $tempUserData = $this->getData($player);
        $tempUserData[$node] = $value;
        $this->setData($player, $tempUserData);
    }

    /**
     * @param IPlayer $player
     * @param $permission
     * @param null $WorldName
     */
    public function setPermission(IPlayer $player, $permission, $WorldName = null)
    {
        if($WorldName === null)
        {
            $tempUserData = $this->getData($player);
            $tempUserData["permissions"][] = $permission;
            $this->setData($player, $tempUserData);
        }
        else
        {
            $worldData = $this->getWorldData($player, $WorldName);
            $worldData["permissions"][] = $permission;
            $this->setWorldData($player, $WorldName, $worldData);
        }

        $this->plugin->updatePermissions($player);
    }

    public function setWorldData(IPlayer $player, $WorldName, array $worldData)
    {
        $tempUserData = $this->getData($player);
        if(!isset($this->getData($player)["worlds"][$WorldName]))
        {
            $tempUserData["worlds"][$WorldName] = [
                "group" => $this->plugin->getDefaultGroup()->getName(),
                "permissions" => [
                ],
                "expTime" => -1
            ];

            $this->setData($player, $tempUserData);
        }
        $tempUserData["worlds"][$WorldName] = $worldData;
        $this->setData($player, $tempUserData);
    }

    /**
     * @param IPlayer $player
     * @param $permission
     * @param null $WorldName
     */
    public function unsetPermission(IPlayer $player, $permission, $WorldName = null)
    {
        if($WorldName === null)
        {
            $tempUserData = $this->getData($player);
            if(!in_array($permission, $tempUserData["permissions"])) return;
            $tempUserData["permissions"] = array_diff($tempUserData["permissions"], [$permission]);
            $this->setData($player, $tempUserData);
        }
        else
        {
            $worldData = $this->getWorldData($player, $WorldName);
            if(!in_array($permission, $worldData["permissions"])) return;
            $worldData["permissions"] = array_diff($worldData["permissions"], [$permission]);
            $this->setWorldData($player, $WorldName, $worldData);
        }

        $this->plugin->updatePermissions($player);
    }
}