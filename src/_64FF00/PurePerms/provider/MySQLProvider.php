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

    private $db, $plugin;

    private $groupsData = [];

    /**
     * @param PurePerms $plugin
     */
    public function __construct(PurePerms $plugin)
    {
        $this->plugin = $plugin;

        $mySQLSettings = $this->plugin->getConfigValue("mysql-settings");

        if(!isset($mySQLSettings["host"]) || !isset($mySQLSettings["port"]) || !isset($mySQLSettings["user"]) || !isset($mySQLSettings["password"]) || !isset($mySQLSettings["db"]))
            throw new \RuntimeException("Failed to connect to the MySQL database: Invalid MySQL settings");


        $this->db = new \mysqli($mySQLSettings["host"], $mySQLSettings["user"], $mySQLSettings["password"], $mySQLSettings["db"], $mySQLSettings["port"]);

        if($this->db->connect_error)
            throw new \RuntimeException("Failed to connect to the MySQL database: " . $this->db->connect_error);

        $resource = $this->plugin->getResource("mysql_deploy_01.sql");

        $this->db->multi_query(stream_get_contents($resource));

        while($this->db->more_results())
        {
            $this->db->next_result();
        }

        fclose($resource);

        $this->loadGroupsData();

        $this->plugin->getScheduler()->scheduleRepeatingTask(new PPMySQLTask($this->plugin, $this->db), 1200);
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
        $userData = [
            "userName" => $player->getName(),
            "group" => $this->plugin->getDefaultGroup()->getName(),
            "permissions" => []
        ];

        $result01 = $this->db->query("SELECT * FROM players WHERE userName = '" .  $this->db->escape_string($player->getName()) . "';");

        if($result01 instanceof \mysqli_result)
        {
            while($currentRow = $result01->fetch_assoc())
            {
                $userData["group"] = $currentRow["userGroup"];
                $userData["permissions"] =  explode(",", $currentRow["permissions"]);
            }

            $result01->free();
        }

        $result02 = $this->db->query("SELECT * FROM players_mw WHERE userName = '" .  $this->db->escape_string($player->getName()) . "';");

        if($result02 instanceof \mysqli_result)
        {
            while($currentRow = $result02->fetch_assoc())
            {
                $userGroup = $currentRow["userGroup"];
                $worldName = $currentRow["worldName"];
                $worldPerms = explode(",", $currentRow["permissions"]);

                $userData["worlds"][$worldName]["group"] = $userGroup;

                $userData["worlds"][$worldName]["permissions"] = $worldPerms;
            }

            $result02->free();
        }

        return $userData;
    }

    public function getUsers()
    {
        // TODO
    }

    public function loadGroupsData()
    {
        $result01 = $this->db->query("SELECT * FROM groups;");

        if($result01 instanceof \mysqli_result)
        {
            if($result01->num_rows <= 0)
            {
                $this->plugin->getLogger()->notice("No groups found in table 'groups', loading groups defined in default SQL script");

                $resource = $this->plugin->getResource("mysql_deploy_02.sql");

                $this->db->multi_query(stream_get_contents($resource));

                while($this->db->more_results())
                {
                    $this->db->next_result();
                }

                fclose($resource);

                $result01 = $this->db->query("SELECT * FROM groups;");
            }

            while($currentRow = $result01->fetch_assoc())
            {
                $groupName = $currentRow["groupName"];

                $this->groupsData[$groupName]["alias"] = $currentRow["alias"];
                $this->groupsData[$groupName]["isDefault"] = $currentRow["isDefault"] === "1" ? true : false;
                $this->groupsData[$groupName]["inheritance"] = $currentRow["inheritance"] !== "" ? explode(",", $currentRow["inheritance"]) : [];
                $this->groupsData[$groupName]["permissions"] = explode(",", $currentRow["permissions"]);
            }

            $result01->free();
        }

        $result02 = $this->db->query("SELECT * FROM groups_mw;");

        if($result02 instanceof \mysqli_result)
        {
            while($currentRow = $result02->fetch_assoc())
            {
                $isDefault = $currentRow["isDefault"] === "1" ? true : false;
                $groupName = $currentRow["groupName"];
                $worldName = $currentRow["worldName"];
                $worldPerms = explode(",", $currentRow["permissions"]);

                $this->groupsData[$groupName]["worlds"][$worldName]["isDefault"] = $isDefault;
                $this->groupsData[$groupName]["worlds"][$worldName]["permissions"] = $worldPerms;
            }

            $result02->free();
        }
    }

    /**
     * @param $groupName
     */
    public function removeGroupData($groupName)
    {
         $this->db->query("
            DELETE FROM groups
            WHERE groupName = '" . $this->db->escape_string($groupName) . "';");

        $this->db->query("
            DELETE OR IGNORE FROM groups_mw
            WHERE groupName = '" . $this->db->escape_string($groupName) . "';");
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

        if($tempGroupData01 !== []) $this->removeGroupData($tempGroupName01);

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
        if(isset($tempUserData["group"]) and isset($tempUserData["permissions"]))
        {
            $userName = $player->getName();
            $userGroup = $tempUserData["group"];
            $permissions = implode(",", $tempUserData["permissions"]);

            $this->db->query("INSERT INTO players
                (userName, userGroup, permissions)
                VALUES
                ('" . $this->db->escape_string($userName) . "', '" . $this->db->escape_string($userGroup) . "', '" . $this->db->escape_string($permissions) . "')
                ON DUPLICATE KEY UPDATE
                userGroup = VALUES(userGroup),
                permissions = VALUES(permissions);");

            if(isset($tempGroupData["worlds"]))
            {
                foreach($tempGroupData["worlds"] as $worldName => $worldData)
                {
                    $worldGroup = $worldData["group"];
                    $worldPerms = implode(",", $worldData["permissions"]);

                    if(!is_array($worldPerms))
                    {
                        $this->db->query("INSERT INTO players_mw
                            (userName, worldName, userGroup, permissions)
                            VALUES
                            ('" . $this->db->escape_string($userName) . "', '" . $this->db->escape_string($worldName) . "', '" . $this->db->escape_string($worldGroup) . "', '" . $this->db->escape_string($worldPerms) . "')
                            ON DUPLICATE KEY UPDATE
                            userGroup = VALUES(userGroup),
                            permissions = VALUES(permissions);");
                    }
                }
            }
        }
    }

    /**
     * @param $groupName
     * @param array $tempGroupData
     */
    public function updateGroupData($groupName, array $tempGroupData)
    {
        if(isset($tempGroupData["isDefault"]) and isset($tempGroupData["inheritance"]) and isset($tempGroupData["permissions"]))
        {
            $alias = $tempGroupData["alias"];
            $isDefault = $tempGroupData["isDefault"] === true ? "1" : "0";
            $inheritance = implode(",", $tempGroupData["inheritance"]);
            $permissions = implode(",", $tempGroupData["permissions"]);

            $this->db->query("INSERT INTO groups
                (groupName, alias, isDefault, inheritance, permissions)
                VALUES
                ('" . $this->db->escape_string($groupName) . "', '" . $this->db->escape_string($alias) . "', '" . $this->db->escape_string($isDefault) . "', '" . $this->db->escape_string($inheritance) . "', '" . $this->db->escape_string($permissions) . "')
                ON DUPLICATE KEY UPDATE
                alias = VALUES(alias),
                isDefault = VALUES(isDefault),
                inheritance = VALUES(inheritance),
                permissions = VALUES(permissions);");

            if(isset($tempGroupData["worlds"]))
            {
                foreach($tempGroupData["worlds"] as $worldName => $worldData)
                {
                    $isDefault = $worldData["isDefault"]  === true ? "1" : "0";
                    $worldPerms = implode(",", $worldData["permissions"]);

                    if(!is_array($worldPerms))
                    {
                        $this->db->query("INSERT INTO groups_mw
                            (groupName, isDefault, worldName, permissions)
                            VALUES
                            ('" . $this->db->escape_string($groupName) . "', '" . $this->db->escape_string($isDefault) . "', '" . $this->db->escape_string($worldName) . "', '" . $this->db->escape_string($worldPerms) . "')
                            ON DUPLICATE KEY UPDATE
                            isDefault = VALUES(isDefault),
                            worldName = VALUES(worldName),
                            permissions = VALUES(permissions);");
                    }
                }
            }
        }
    }
    
    public function close()
    {
        $this->db->close();
    }
}