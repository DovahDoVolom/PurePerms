<?php

namespace _64FF00\PurePerms\provider;

use _64FF00\PurePerms\PurePerms;
use _64FF00\PurePerms\PPGroup;
use _64FF00\PurePerms\task\PPMySQLTask;

use pocketmine\IPlayer;

class MySQLProvider implements ProviderInterface
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
    
    private $db, $groupsData;

    /**
     * @param PurePerms $plugin
     */
    public function __construct(PurePerms $plugin)
    {
        $this->plugin = $plugin;

        $mySQLSettings = $this->plugin->getConfigValue("mysql-settings");

        if(!isset($mySQLSettings["host"]) || !isset($mySQLSettings["port"]) || !isset($mySQLSettings["user"]) || !isset($mySQLSettings["password"]) || !isset($mySQLSettings["db"]))
        {
            $this->plugin->getLogger()->critical("Failed to connect to the MySQL database: Invalid MySQL settings");
            $this->plugin->getServer()->getPluginManager()->disablePlugin($this->plugin);
        }

        $this->db = new \mysqli($mySQLSettings["host"], $mySQLSettings["user"], $mySQLSettings["password"], $mySQLSettings["db"], $mySQLSettings["port"]);

        if($this->db->connect_error)
        {
            $this->plugin->getLogger()->critical("Failed to connect to the MySQL database: " . $this->db->connect_error);
            $this->plugin->getServer()->getPluginManager()->disablePlugin($this->plugin);
        }

        $resource = $this->plugin->getResource("mysql_deploy.sql");
        $this->db->multi_query(stream_get_contents($resource));
        fclose($resource);
        
        $this->loadGroupsData();

        $this->plugin->getServer()->getScheduler()->scheduleRepeatingTask(new PPMySQLTask($this->plugin, $this->db), 1200);
    }

    /**
     * @param PPGroup $group
     * @return array
     */
    public function getGroupData(PPGroup $group)
    {
        $groupName = $group->getName();

        if(!isset($this->getGroupsData()[$groupName]) || !is_array($this->getGroupsData()[$groupName])) return [];

        return $this->getGroupsData()[$groupName];
    }

    /**
     * @return array
     */
    public function getGroupsData()
    {
        return $this->groupsData;
    }

    /**
     * @param IPlayer $player
     * @return array
     */
    public function getPlayerData(IPlayer $player)
    {
    }

    public function loadGroupsData()
    {
        $this->groupsData = [];

        $result01 = $this->db->query("
            SELECT groupName, isDefault, inheritance, permissions
            FROM groups;
        ");

        if($result01 instanceof \mysqli_result)
        {
            while($currentRow = $result01->fetch_array(MYSQLI_ASSOC))
            {
                $groupName = $currentRow["groupName"];

                $this->groupsData[$groupName]["isDefault"] = $currentRow["isDefault"];
                $this->groupsData[$groupName]["inheritance"] = $currentRow["inheritance"] !== "" ? explode(",", $currentRow["inheritance"]) : [];
                $this->groupsData[$groupName]["permissions"] = explode(",", $currentRow["permissions"]);
            }

            $result01->free();
        }

        $result02 = $this->db->query("
            SELECT groupName, worldName, permissions
            FROM groups_mw;
        ");

        if($result02 instanceof \mysqli_result)
        {
            while($currentRow = $result01->fetch_array(MYSQLI_ASSOC))
            {
                $groupName = $currentRow["groupName"];

                foreach($currentRow["worlds"] as $worldName => $worldPerms)
                {
                    $this->groupsData[$groupName]["worlds"][$worldName] = explode(",", $worldPerms);
                }
            }

            $result02->free();
        }
    }

    /**
     * @param $groupName
     */
    public function removeGroupData($groupName)
    {
    }

    /**
     * @param PPGroup $group
     * @param array $tempGroupData
     */
    public function setGroupData(PPGroup $group, array $tempGroupData)
    {
        $groupName = $group->getName();

        $this->updateGroupData($groupName, $tempGroupData);

        $this->loadGroupsData();
    }

    /**
     * @param array $tempGroupsData
     */
    public function setGroupsData(array $tempGroupsData)
    {
        $tempGroupData01 = array_diff_key($this->groupsData, $tempGroupsData);

        $tempGroupName01 = key($tempGroupData01);

        if($tempGroupData01 != []) $this->removeGroupData($tempGroupName01);

        foreach($tempGroupsData as $tempGroupName02 => $tempGroupData02)
        {
            $this->updateGroupData($tempGroupName02, $tempGroupData02);
        }

        $this->loadGroupsData();
    }

    /**
     * @param IPlayer $player
     * @param array $tempUserData
     */
    public function setPlayerData(IPlayer $player, array $tempUserData)
    {
    }

    /**
     * @param $groupName
     * @param $tempGroupData
     */
    public function updateGroupData($groupName, $tempGroupData)
    {

    }
    
    public function close()
    {
        $this->db->close();
    }
}