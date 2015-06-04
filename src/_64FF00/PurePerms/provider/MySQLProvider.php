<?php

namespace _64FF00\PurePerms\provider;

use _64FF00\PurePerms\PurePerms;
use _64FF00\PurePerms\ppdata\PPGroup;
use _64FF00\PurePerms\ppdata\PPUser;

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
    }

    /**
     * @param PPGroup $group
     * @return array
     */
    public function getGroupData(PPGroup $group)
    {
    }

    /**
     * @return array
     */
    public function getGroupsData()
    {
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
    }
}