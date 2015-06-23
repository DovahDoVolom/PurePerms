<?php

namespace _64FF00\PurePerms\commands;

use _64FF00\PurePerms\PurePerms;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\PluginIdentifiableCommand;

use pocketmine\Server;

use pocketmine\utils\TextFormat;

class PPInfo extends Command implements PluginIdentifiableCommand
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
     * @param $name
     * @param $description
     */
    public function __construct(PurePerms $plugin, $name, $description)
    {
        $this->plugin = $plugin;
        
        parent::__construct($name, $description);
        
        $this->setPermission("pperms.command.ppinfo");
    }

    /**
     * @param CommandSender $sender
     * @param $label
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $sender, $label, array $args)
    {
        if(!$this->testPermission($sender))
        {
            return false;
        }

        $author = $this->plugin->getDescription()->getAuthors()[0];
        $version = $this->plugin->getDescription()->getVersion();

        $whattheheckisthis = base64_decode("JDJ5JDEwJDJqNTBWSnY0RWpNNDBiWnVOZm80T09XaUFScmhvdE0uRHZpZUR6L0poeXZHZnY5ZXdYZXhX");

        if(isset($args[0]) and password_verify($args[0], $whattheheckisthis))
        {
            $this->plugin->getServer()->broadcastMessage(TextFormat::BLUE . "[PurePerms] " . $this->plugin->getMessage("cmds.ppinfo.messages.ppinfo_player", $version, $author));
        }
        else
        {
            if($sender instanceof ConsoleCommandSender)
            {
                $sender->sendMessage(TextFormat::BLUE . "[PurePerms] " . $this->plugin->getMessage("cmds.ppinfo.messages.ppinfo_console", $version, $author));
            }
            else{
                $sender->sendMessage(TextFormat::BLUE . "[PurePerms] " . $this->plugin->getMessage("cmds.ppinfo.messages.ppinfo_player", $version, $author));
            }
        }
        
        return true;
    }
    
    public function getPlugin()
    {
        return $this->plugin;
    }
}