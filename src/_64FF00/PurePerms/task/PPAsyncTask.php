<?php

namespace _64FF00\PurePerms;   

use pocketmine\scheduler\AsyncTask;  

use pocketmine\Server;                                                           

class PPAsyncTask extends AsyncTask
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
    
    public function __construct(PurePerms $plugin)
    {
        $this->plugin = $plugin;
    }
    
    public function onRun()
    {
    }
    
    public function onCompletion(Server $server)
    {
    }
}