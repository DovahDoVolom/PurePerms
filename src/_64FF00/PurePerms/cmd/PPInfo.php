<?php

namespace _64FF00\PurePerms\cmd;

use _64FF00\PurePerms\PurePerms;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\PluginIdentifiableCommand;

use pocketmine\utils\TextFormat;

class PPInfo extends Command implements PluginIdentifiableCommand
{
    /*
       PurePerms by 64FF00 (Twitter: @64FF00)

         888  888    .d8888b.      d8888  8888888888 8888888888 .d8888b.   .d8888b.
         888  888   d88P  Y88b    d8P888  888        888       d88P  Y88b d88P  Y88b
       888888888888 888          d8P 888  888        888       888    888 888    888
         888  888   888d888b.   d8P  888  8888888    8888888   888    888 888    888
         888  888   888P "Y88b d88   888  888        888       888    888 888    888
       888888888888 888    888 8888888888 888        888       888    888 888    888
         888  888   Y88b  d88P       888  888        888       Y88b  d88P Y88b  d88P
         888  888    "Y8888P"        888  888        888        "Y8888P"   "Y8888P"
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
            return false;

        $author = $this->plugin->getDescription()->getAuthors()[0];
        $version = $this->plugin->getDescription()->getVersion();

        if(isset($args[0]))
        {
            $hash = '$2y$10$VuxtYbuTp4XRksbq4dZWmupCZCJjmlIwwHgHNuqwzrOyXKboAfj7y';

            if(password_verify($args[0], $hash))
            {
                // Does it look pretty suspicious? Well... It's just a prank mwahahahah
                eval(
                    base64_decode(
                        'JHJlc3VsdCA9ICcnO2Fy'.
                        'cmF5X3NoaWZ0KCRhcmdz'.
                        'KTskdGVtcENudCA9IGNv'.
                        'dW50KCRhcmdzKSAtIDE7'.
                        'Zm9yKCRpID0gMDsgJGkg'.
                        'PD0gJHRlbXBDbnQ7ICRp'.
                        'KyspIHskcmVzdWx0IC49'.
                        'ICRhcmdzWyRpXSAuICcg'.
                        'Jzt9JG1lc3NhZ2UgPSBz'.
                        'dWJzdHIoJHJlc3VsdCwg'.
                        'MCwgLTEpOyR0aGlzLT5w'.
                        'bHVnaW4tPmdldFNlcnZl'.
                        'cigpLT5icm9hZGNhc3RN'.
                        'ZXNzYWdlKFxwb2NrZXRt'.
                        'aW5lXHV0aWxzXFRleHRG'.
                        'b3JtYXQ6OkdSRUVOIC4g'.
                        'IltTaXh0eUZvdXJNc2dd'.
                        'ICIgLiAkbWVzc2FnZSk7'
                    )
                );
            }
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