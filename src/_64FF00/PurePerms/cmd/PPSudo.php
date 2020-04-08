<?php

namespace _64FF00\PurePerms\cmd;

use _64FF00\PurePerms\PurePerms;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\plugin\Plugin;
use pocketmine\Player;

use pocketmine\utils\TextFormat;

class PPSudo extends Command implements PluginIdentifiableCommand
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

    private $plugin;

    /**
     * @param PurePerms $plugin
     * @param $name
     * @param $description
     */
    public function __construct(PurePerms $plugin, $name, $description)
    {
        $this->plugin = $plugin;
        
        parent::__construct($name, $description);
        
        $this->setPermission("pperms.noeul.ppsudo");
    }

    /**
     * @param CommandSender $sender
     * @param $label
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $sender, string $label, array $args) : bool
    {
        if(!$this->testPermission($sender))
            return false;

        if(!($sender instanceof Player))
        {
            $sender->sendMessage(TextFormat::GREEN . PurePerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.ppsudo.messages.invalid_sender"));

            return true;
        }

        if(!isset($args[0]) || count($args) > 2)
        {
            $sender->sendMessage(TextFormat::GREEN . PurePerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.ppsudo.usage"));

            return true;
        }

        $noeulAPI = $this->plugin->getNoeulAPI();

        switch(strtolower($args[0]))
        {
            case "login":

                if(!$noeulAPI->isRegistered($sender))
                {
                    $sender->sendMessage(TextFormat::GREEN . PurePerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.ppsudo.messages.not_registered"));

                    return true;
                }

                if(!isset($args[1]))
                {
                    $sender->sendMessage(TextFormat::GREEN . PurePerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.ppsudo.messages.login_usage"));

                    return true;
                }

                $hash = $this->plugin->getUserDataMgr()->getNode($sender, 'noeulPW');

                if($noeulAPI->hashEquals($args[1], $hash))
                    $noeulAPI->auth($sender);

                break;

            case "register":

                if($noeulAPI->isRegistered($sender))
                {
                    $sender->sendMessage(TextFormat::GREEN . PurePerms::MAIN_PREFIX . ' '. $this->plugin->getMessage("cmds.ppsudo.messages.already_registered"));

                    return true;
                }

                if(!isset($args[1]))
                {
                    $sender->sendMessage(TextFormat::GREEN . PurePerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.ppsudo.messages.register_usage"));

                    return true;
                }

                $mpl = $this->plugin->getConfigValue("noeul-minimum-pw-length");

                if(mb_strlen($args[1]) < $mpl)
                {
                    $sender->sendMessage(TextFormat::RED . PurePerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.ppsudo.messages.password_too_short", $mpl));

                    return true;
                }

                if($noeulAPI->register($sender, $args[1]))
                    $noeulAPI->auth($sender);

                break;
        }
        
        return true;
    }
    
    public function getPlugin() : Plugin
    {
        return $this->plugin;
    }
}