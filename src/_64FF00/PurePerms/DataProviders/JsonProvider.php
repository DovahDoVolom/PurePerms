<?php

namespace _64FF00\PurePerms\DataProviders;

use _64FF00\PurePerms\PurePerms;
use _64FF00\PurePerms\PPGroup;

use pocketmine\player\IPlayer;

use pocketmine\utils\Config;
use RuntimeException;

class JsonProvider implements ProviderInterface
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

    private $groups, $userDataFolder;

    private PurePerms $plugin;

    /**
     * @param PurePerms $plugin
     */
    public function __construct(PurePerms $plugin)
    {
        $this->plugin = $plugin;
        $this->plugin->saveResource("ranks.yml");
        $this->groups = new Config($this->plugin->getDataFolder() . "ranks.yml", Config::YAML, []);
        $this->userDataFolder = $this->plugin->getDataFolder() . "players/";
        if(!file_exists($this->userDataFolder))
            @mkdir($this->userDataFolder, 0777, true);
    }
    /**
     * @param PPGroup $group
     * @return mixed
     */
    public function getGroupData(PPGroup $group)
    {
        $groupName = $group->getName();

        if(!isset($this->getGroupsData()[$groupName]) || !is_array($this->getGroupsData()[$groupName]))
            return [];

        return $this->getGroupsData()[$groupName];
    }

    /**
     * @return Config
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
     * @param IPlayer $player
     * @param bool $onUpdate
     * @return array|Config
     */
    public function getPlayerConfig(IPlayer $player, $onUpdate = false)
    {
        $userName = $player->getName();

        // TODO
        if($onUpdate === true)
        {
            if(!file_exists($this->userDataFolder . strtolower($userName) . ".json"))
            {
                return new Config($this->userDataFolder . strtolower($userName) . ".json", Config::YAML, [
                    "userName" => $userName,
                    "group" => $this->plugin->getDefaultGroup()->getName(),
                    "permissions" => [],
                    "worlds" => [],
                    "time" => -1
                ]);
            }
            else
            {
                return new Config($this->userDataFolder . strtolower($userName) . ".json", Config::YAML, [
                ]);
            }
        }
        else
        {
            if(file_exists($this->userDataFolder . strtolower($userName) . ".json"))
            {
                return new Config($this->userDataFolder . strtolower($userName) . ".json", Config::YAML, [
                ]);
            }
            else
            {
                return [
                    "userName" => $userName,
                    "group" => $this->plugin->getDefaultGroup()->getName(),
                    "permissions" => [],
                    "worlds" => [],
                    "time" => -1
                ];
            }
        }
    }
    /**
     * @param IPlayer $player
     * @return array|Config
     */
    public function getPlayerData(IPlayer $player)
    {
        $userConfig = $this->getPlayerConfig($player);

        return (($userConfig instanceof Config) ? $userConfig->getAll() : $userConfig);
    }

    public function getUsers()
    {
        // TODO: Implement getUsers() method.
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
        $userData = $this->getPlayerConfig($player, true);

        if(!$userData instanceof Config)
            throw new RuntimeException("Failed to update player data: Invalid data type (" . get_class($userData) . ")");

        $userData->setAll($tempUserData);

        $userData->save();
    }

    public function close()
    {
    }
}