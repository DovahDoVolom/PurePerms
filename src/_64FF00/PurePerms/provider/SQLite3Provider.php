<?php

namespace _64FF00\PurePerms\provider;

use _64FF00\PurePerms\PurePerms;
use _64FF00\PurePerms\ppdata\PPGroup;
use _64FF00\PurePerms\ppdata\PPUser;

class SQLite3Provider implements ProviderInterface
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
    
    private $groupsData = [];

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
        $this->db = new \SQLite3($this->plugin->getDataFolder() . "PurePerms.db");
            
        $db_query = stream_get_contents($this->plugin->getResource("sqlite3_deploy.sql"));
        
        $this->db->exec($db_query);

        $this->loadGroupsData();
    }

    /**
     * @param PPGroup $group
     * @return array
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

    public function loadGroupsData()
    {
        $result01 = $this->db->query("
            SELECT groupName, isDefault, inheritance, permissions
            FROM groups;
        ");

        if($result01 instanceof \SQLite3Result)
        {
            while($currentRow = $result01->fetchArray(SQLITE3_ASSOC))
            {
                $groupName = $currentRow["groupName"];

                $this->groupsData[$groupName]["isDefault"] = $currentRow["isDefault"];
                $this->groupsData[$groupName]["inheritance"] = explode(",", $currentRow["inheritance"]);
                $this->groupsData[$groupName]["permissions"] = explode(",", $currentRow["permissions"]);
            }
        }

        $result01->finalize();

        // TODO: Multiworld Support
    }

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
        $defaultGroup = $this->plugin->getDefaultGroup()->getName();

        $this->db->exec("
            INSERT OR IGNORE INTO players (
                userName,
                userGroup,
                permissions
            )
            VALUES (
                $userName,
                $defaultGroup,
                ''
            );
        ");

        $result01 = $this->db->query("
            SELECT userName, userGroup, permissions
            FROM players;
        ");

        $userData = [];

        if($result01 instanceof \SQLite3Result)
        {
            while($currentRow = $result01->fetchArray(SQLITE3_ASSOC))
            {
                $userData["userName"] = $currentRow["userName"];
                $userData["userGroup"] = $currentRow["userGroup"];
                $userData["permissions"] = explode(",", $currentRow["permissions"]);
            }
        }

        $result01->finalize();

        // TODO: Multiworld Support

        return $userData;
    }

    /**
     * @param PPGroup $group
     * @param array $tempGroupData
     */
    public function setGroupData(PPGroup $group, array $tempGroupData)
    {
        $groupName = $group->getName();

        // INSERT OR IGNORE INTO groups ...

        if(isset($tempGroupData["isDefault"])) $isDefault = $tempGroupData["isDefault"];
        if(isset($tempGroupData["inheritance"])) $inheritance = implode(",", $tempGroupData["inheritance"]);
        if(isset($tempGroupData["permissions"])) $permissions = implode(",", $tempGroupData["permissions"]);

        $stmt01 = $this->db->prepare("
            UPDATE groups
            SET isDefault = :isDefault, inheritance = :inheritance, permissions = :permissions
            WHERE groupName = :groupName;
        ");

        $stmt01->bindValue(":groupName", $groupName, SQLITE3_TEXT);
        $stmt01->bindValue(":isDefault", $isDefault, SQLITE3_INTEGER);
        $stmt01->bindValue(":inheritance", $inheritance, SQLITE3_TEXT);
        $stmt01->bindValue(":permissions", $permissions, SQLITE3_TEXT);

        $result01 = $stmt01->execute();

        $result01->finalize();

        // TODO: Multiworld Support
    }

    /**
     * @param array $tempGroupsData
     */
    public function setGroupsData(array $tempGroupsData)
    {
        foreach($tempGroupsData as $groupName => $groupData)
        {
            // INSERT OR IGNORE INTO groups ...

            $stmt01 = $this->db->prepare("
                UPDATE groups
                SET isDefault = :isDefault, inheritance = :inheritance, permissions = :permissions
                WHERE groupName = :groupName;
            ");

            $isDefault = $groupData["isDefault"];
            $inheritance = implode(",", $groupData["inheritance"]);
            $permissions = implode(",", $groupData["permissions"]);

            $stmt01->bindValue(":groupName", $groupName, SQLITE3_TEXT);
            $stmt01->bindValue(":isDefault", $isDefault, SQLITE3_INTEGER);
            $stmt01->bindValue(":inheritance", $inheritance, SQLITE3_TEXT);
            $stmt01->bindValue(":permissions", $permissions, SQLITE3_TEXT);

            $result01 = $stmt01->execute();

            $result01->finalize();

            // TODO: Multiworld Support
        }
    }

    /**
     * @param PPUser $user
     * @param array $tempUserData
     */
    public function setUserData(PPUser $user, array $tempUserData)
    {
        if(isset($tempUserData["userName"])) $userName = $tempUserData["userName"];
        if(isset($tempUserData["userGroup"])) $userGroup = $tempUserData["inheritance"];
        if(isset($tempUserData["permissions"])) $permissions = implode(",", $tempUserData["permissions"]);

        $stmt01 = $this->db->prepare("
            UPDATE players
            SET userGroup = :userGroup, permissions = :permissions
            WHERE userName = :userName;
        ");

        $stmt01->bindValue(":userName", $userName, SQLITE3_TEXT);
        $stmt01->bindValue(":userGroup", $userGroup, SQLITE3_TEXT);
        $stmt01->bindValue(":permissions", $permissions, SQLITE3_TEXT);

        $result01 = $stmt01->execute();

        $result01->finalize();

        // TODO: Multiworld Support
    }
    
    public function close()
    {
        $this->db->close();
    }
}