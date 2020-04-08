<?php

namespace _64FF00\PurePerms\provider;

use _64FF00\PurePerms\PurePerms;
use _64FF00\PurePerms\PPGroup;

use pocketmine\IPlayer;

use pocketmine\utils\Config;

class DefaultProvider implements ProviderInterface
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

    private $groups, $players, $plugin;

    /**
     * @param PurePerms $plugin
     */
    public function __construct(PurePerms $plugin)
    {
        $this->plugin = $plugin;

        $this->plugin->saveResource("groups.yml");

        $this->groups = new Config($this->plugin->getDataFolder() . "groups.yml", Config::YAML);

        if(empty($this->groups->getAll()))
            throw new \RuntimeException($this->plugin->getMessage("logger_messages.YAMLProvider_InvalidGroupsSettings"));

        $this->plugin->saveResource("players.yml");

        $this->players = new Config($this->plugin->getDataFolder() . "players.yml", Config::YAML);
    }

    /**
     * @param PPGroup $group
     * @return mixed
     */
    public function getGroupData(PPGroup $group)
    {
        $groupName = $group->getName();

        if(!isset($this->getGroupsData()[$groupName]) || !is_array($this->getGroupsData()[$groupName])) return [];

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

    public function getPlayerData(IPlayer $player)
    {
        $userName = strtolower($player->getName());

        if(!$this->players->exists($userName))
        {
            return [
                "group" => $this->plugin->getDefaultGroup()->getName(),
                "permissions" => [],
                "worlds" => [],
                "time" => -1
            ];
        }

        return $this->players->get($userName);
    }

    public function getUsers()
    {
        /*
        if(empty($this->players->getAll()))
            return null;

        return $this->players->getAll();
        */
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
     * @param IPlayer $player
     * @param array $tempUserData
     */
    public function setPlayerData(IPlayer $player, array $tempUserData)
    {
        $userName = strtolower($player->getName());

        if(!$this->players->exists($userName))
        {
            $this->players->set($userName, [
                "group" => $this->plugin->getDefaultGroup()->getName(),
                "permissions" => [],
                "worlds" => [],
                "time" => -1
            ]);
        }

        if(isset($tempUserData["userName"]))
            unset($tempUserData["userName"]);

        $this->players->set($userName, $tempUserData);

        $this->players->save();
    }

    public function close()
    {
    }
}