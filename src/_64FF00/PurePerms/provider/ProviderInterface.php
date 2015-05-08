<?php

namespace _64FF00\PurePerms\provider;

use _64FF00\PurePerms\ppdata\PPGroup;
use _64FF00\PurePerms\ppdata\PPUser;

interface ProviderInterface
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
    
    public function init();
    
    public function getGroupData(PPGroup $group);
    
    public function getGroupsData();
    
    public function getUserData(PPUser $user);
    
    public function setGroupData(PPGroup $group, array $tempGroupData);
    
    public function setGroupsData(array $tempGroupsData);

    public function setUserData(PPUser $user, array $tempUserData);
    
    public function close();
}