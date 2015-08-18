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

    private $db, $groupsData;

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
     * @param PPUser $user
     * @return array
     */
    public function getUserData(PPUser $user)
    {
    }

    public function loadGroupsData()
    {

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