<?php

namespace _64FF00\PurePerms\ppdata;

interface PPDataInterface
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
    
    public function getData();
    
    public function getName();
    
    public function getNode($node);
    
    public function getWorldData($levelName);
    
    public function removeNode($node);
    
    public function setData(array $data);
    
    public function setNode($node, $value);
    
    public function setWorldData($levelName, array $worldData);
}