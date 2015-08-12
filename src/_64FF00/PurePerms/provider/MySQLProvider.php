<?php

namespace _64FF00\PurePerms\provider;

use _64FF00\PurePerms\PurePerms;
use _64FF00\PurePerms\ppdata\PPGroup;
use _64FF00\PurePerms\ppdata\PPUser;
use _64FF00\PurePerms\task\PPMySQLTask;

class MySQLProvider implements ProviderInterface
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

    private $db;

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

        $db_query = stream_get_contents($this->plugin->getResource("mysql_deploy.sql"));

        $this->db->query($db_query);

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
     * @param PPUser $user
     * @return array
     */
    public function getUserData(PPUser $user)
    {
        $userName = $user->getName();
        $userData = [];

        $result01 = $this->db->query("
            SELECT userGroup, permissions
            FROM players
            WHERE userName = $userName;
        ");

        if($result01 instanceof \mysqli_result)
        {
            while($currentRow = $result01->fetch_array(MYSQLI_ASSOC))
            {
                $userData["userName"] = $userName;
                $userData["userGroup"] = $currentRow["userGroup"];
                $userData["permissions"] = explode(",", $currentRow["permissions"]);
            }
        }

        $result01->free();

        $result02 = $this->db->query("
            SELECT worldName, userGroup, permissions
            FROM players_mw
            WHERE userName = $userName;
        ");

        if($result02 instanceof \mysqli_result)
        {
            while($currentRow = $result02->fetch_array(MYSQLI_ASSOC))
            {
                $worldName = $currentRow["worldName"];
                $worldPerms = explode(",", $currentRow["permissions"]);

                $userData["worlds"][$worldName]["permissions"] = $worldPerms;
            }
        }

        $result02->free();

        return $userData;
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
                $this->groupsData[$groupName]["inheritance"] = explode(",", $currentRow["inheritance"]);
                $this->groupsData[$groupName]["permissions"] = explode(",", $currentRow["permissions"]);
            }
        }

        $result01->free();

        $result02 = $this->db->query("
            SELECT groupName, worldName, permissions
            FROM groups_mw;
        ");

        if($result02 instanceof \mysqli_result)
        {
            while($currentRow = $result02->fetch_array(MYSQLI_ASSOC))
            {
                $groupName = $currentRow["groupName"];

                foreach($currentRow["worlds"] as $worldName => $worldPerms)
                {
                    $this->groupsData[$groupName]["worlds"][$worldName] = explode(",", $worldPerms);
                }
            }
        }

        $result02->free();
    }

    /**
     * @param $groupName
     */
    public function removeGroupData($groupName)
    {
        $result01 = $this->db->query("
            DELETE FROM groups WHERE groupName = $groupName;
        ");

        $result01->free();

        $result02 = $this->db->query("
            DELETE OR IGNORE FROM groups_mw WHERE groupName = $groupName;
        ");

        $result02->free();
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
     * @param PPUser $user
     * @param array $tempUserData
     */
    public function setUserData(PPUser $user, array $tempUserData)
    {
        $userGroup = $this->plugin->getDefaultGroup()->getName();

        if(isset($tempUserData["userName"])) $userName = $tempUserData["userName"];
        if(isset($tempUserData["userGroup"])) $userGroup = $tempUserData["userGroup"];
        if(isset($tempUserData["permissions"])) $permissions = implode(",", $tempUserData["permissions"]);

        $result01 = $this->db->query("
            INSERT OR REPLACE INTO players (userName, userGroup, permissions)
            VALUES ($userName, $userGroup, $permissions);
        ");

        $result01->free();

        if(isset($tempUserData["worlds"]))
        {
            foreach($tempUserData["worlds"] as $worldName => $worldPerms)
            {
                $result02 = $this->db->query("
                    INSERT OR REPLACE INTO players_mw (userName, worldName, userGroup, permissions)
                    VALUES ($userName, $worldName, $userGroup, $worldPerms);
                ");

                $result02->free();
            }
        }
    }

    /**
     * @param $groupName
     * @param $tempGroupData
     */
    public function updateGroupData($groupName, $tempGroupData)
    {
        if(isset($tempGroupData["isDefault"])) $isDefault = $tempGroupData["isDefault"];
        if(isset($tempGroupData["inheritance"])) $inheritance = implode(",", $tempGroupData["inheritance"]);
        if(isset($tempGroupData["permissions"])) $permissions = implode(",", $tempGroupData["permissions"]);

        $result01 = $this->db->query("
            INSERT OR REPLACE INTO groups (groupName, isDefault, inheritance, permissions)
            VALUES ($groupName, $isDefault, $inheritance, $permissions);
        ");

        $result01->free();

        if(isset($tempGroupData["worlds"]))
        {
            foreach($tempGroupData["worlds"] as $worldName => $worldPerms)
            {
                $result02 = $this->db->prepare("
                    INSERT OR REPLACE INTO groups_mw (groupName, worldName, permissions)
                    VALUES ($groupName, $worldName, $worldPerms);
                ");

                $result02->free();
            }
        }
    }
    
    public function close()
    {
        $this->db->close();
    }
}