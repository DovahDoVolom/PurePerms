<?php

namespace _64FF00\PurePerms;              

use pocketmine\utils\Config;                                                    

class PPMessages
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

    private $messages;

    /**
     * @param PurePerms $plugin
     */
    public function __construct(PurePerms $plugin)
    {
        $this->plugin = $plugin;
        
        $this->loadMessages();
    }

    /**
     * @param $node
     * @param ...$vars
     * @return mixed|null
     */
    public function getMessage($node, ...$vars)
    {
        $msg = $this->messages->getNested($node);
        
        if($msg != null)
        {
            $number = 0;
            
            foreach($vars as $v)
            {           
                $msg = str_replace("%var$number%", $v, $msg);
                
                $number++;
            }
            
            return $msg;
        }
        
        return null;
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        $version = $this->messages->get("messages-version");

        return $version;
    }
    
    public function loadMessages()
    {
        $this->plugin->saveResource("messages.yml");
        
        $this->messages = new Config($this->plugin->getDataFolder() . "messages.yml", Config::YAML, array(
        ));
        
        if(version_compare($this->getVersion(), $this->plugin->getPPVersion()) == -1)
        {
            $this->plugin->saveResource("messages.yml", true);
            
            $this->reloadMessages();
        }
    }
    
    public function reloadMessages()
    {
        $this->messages->reload();
    }
}