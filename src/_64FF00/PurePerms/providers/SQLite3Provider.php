<?php

namespace _64FF00\PurePerms\providers;

use _64FF00\PurePerms\PurePerms;
use _64FF00\PurePerms\ppdata\PPGroup;
use _64FF00\PurePerms\ppdata\PPUser;

use pocketmine\IPlayer;

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
    
    private $groups, $groupsData = [], $players;

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
    }

    /**
     * @param PPGroup $group
     * @return mixed
     */
    public function getGroupData(PPGroup $group)
    {
        $groupName = $group->getName();
        
        if(isset($this->getGroupsData()[$groupName]) and is_array($this->getGroupsData()[$groupName]))
        {
            return $this->getGroupsData()[$groupName];
        }
    }

    /**
     * @return array
     */
    public function getGroupsData()
    {
        if(empty($this->groupsData))
        {
            $result = $this->db->query("
                SELECT groupName, isDefault, inheritance, permissions 
                FROM groups;
            ");
            
            if($result instanceof \SQLite3Result)
            {
                while($currentRow = $result->fetchArray(SQLITE3_ASSOC))
                {
                    $groupName = $currentRow["groupName"];
                    
                    unset($currentRow["groupName"]);
                    
                    $this->groupsData[$groupName] = $currentRow;
                    
                    $inheritance = $this->groupsData[$groupName]["inheritance"];
                                
                    if(!is_array($inheritance)) 
                    {
                        $this->groupsData[$groupName]["inheritance"] = explode(",", $inheritance);
                    }
                    
                    if(empty($inheritance)) $this->groupsData[$groupName]["inheritance"] = [];
                    
                    $permissions = $this->groupsData[$groupName]["permissions"];
                    
                    if(!is_array($permissions))
                    {
                        $this->groupsData[$groupName]["permissions"] = explode(",", $permissions);
                    }
                }
                
                $result->finalize();
            }
            
            $result_mw = $this->db->query("
                SELECT groupName, worldName, permissions_mw
                FROM groups_mw;
            ");
            
            if($result_mw instanceof \SQLite3Result)
            {
                while($currentRow = $result_mw->fetchArray(SQLITE3_ASSOC))
                {
                    $groupName = $currentRow["groupName"];
                    $worldName = $currentRow["worldName"];
                    $permissions_mw = $currentRow["permissions_mw"];
                    
                    if(!is_array($permissions_mw))
                    {
                        $this->groupsData[$groupName]["worlds"][$worldName] =  explode(",", $permissions_mw);
                    }
                }
                
                $result_mw->finalize();
            }
        }
        
        return $this->groupsData;
    }
    
    public function getUserData(PPUser $user)
    {
        
    }

    /**
     * @param PPGroup $group
     * @param array $tempGroupData
     */
    public function setGroupData(PPGroup $group, array $tempGroupData)
    {
        $groupName = $group->getName();
        
        if(!empty($this->groupsData[$groupName]))
        {
            if(isset($tempGroupData["isDefault"])) $isDefault = $tempGroupData["isDefault"];
                
            if(isset($tempGroupData["inheritance"])) $inheritance = $tempGroupData["inheritance"];
                                
            if(is_array($inheritance)) 
            {
                $inheritance = implode(",", $inheritance);
            }
                    
            if(isset($tempGroupData["permissions"])) $permissions = $tempGroupData["permissions"];
                    
            if(is_array($permissions))
            {
                $permissions = implode(",", $permissions);
            }
            
            if(isset($tempGroupData["worlds"]))
            {
                foreach($tempGroupData["worlds"] as $worldName => $permissions_mw)
                {
                    if(is_array($permissions_mw))
                    {
                        $permissions_mw = implode(",", $permissions_mw);
                    }
                    
                    // http://php.net/manual/en/sqlite3.query.php#111658
                    /*
                        The recommended way to do a SQLite3 query is to use a statement. 
                        For a table creation, a query might be fine (and easier) but for an insert, update or select, 
                        you should really use a statement, it's really easier and safer as SQLite will escape your parameters according to their type. 
                        SQLite will also use less memory than if you created the whole query by yourself. 
                    */
            
                    $stmt_mw = $this->db->prepare("
                        UPDATE groups_mw
                        SET worldName = :worldName, permissions_mw = :permissions_mw
                        WHERE groupName = :groupName;
                    ");
                    
                    $stmt_mw->bindValue(":groupName", $groupName, SQLITE3_TEXT);
                    $stmt_mw->bindValue(":worldName", $worldName, SQLITE3_TEXT);
                    $stmt_mw->bindValue(":permissions_mw", $permissions_mw, SQLITE3_TEXT);
                    
                    $result_mw = $stmt_mw->execute();
                }
                
                $result_mw->finalize();
            }
            
            $stmt = $this->db->prepare("
                UPDATE groups 
                SET isDefault = :isDefault, inheritance = :inheritance, permissions = :permissions 
                WHERE groupName = :groupName;
            ");
                
            $stmt->bindValue(":groupName", $groupName, SQLITE3_TEXT);
            $stmt->bindValue(":isDefault", $isDefault, SQLITE3_INTEGER);
            $stmt->bindValue(":inheritance", $inheritance, SQLITE3_TEXT);
            $stmt->bindValue(":permissions", $permissions, SQLITE3_TEXT);
                
            $result = $stmt->execute();
            
            $result->finalize();
        }
    }

    /**
     * @param array $tempGroupsData
     */
    public function setGroupsData(array $tempGroupsData)
    {
        foreach(array_keys($tempGroupsData) as $groupName)
        {
            $group = $this->plugin->getGroup($groupName);
            
            $this->setGroupData($group, $tempGroupsData[$groupName]);
        }
    }

    public function setUserData(PPUser $user, array $tempUserData)
    {
    }
    
    public function close()
    {
        $this->db->close();
    }
}