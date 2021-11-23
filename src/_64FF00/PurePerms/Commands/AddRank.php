<?php

namespace _64FF00\PurePerms\Commands;

use _64FF00\PurePerms\PurePerms;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\PluginOwnedTrait;
use pocketmine\utils\TextFormat;

class AddRank extends Command implements PluginOwned
{
	use PluginOwnedTrait;
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
        $this->setPermission("pperms.command.addrank");
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
        if(!isset($args[0]) || count($args) > 1)
        {
            $sender->sendMessage(TextFormat::GREEN . PurePerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.addrank.usage"));
            return true;
        }

        $result = $this->plugin->addGroup($args[0]);
        if($result === PurePerms::SUCCESS)
        {
            $sender->sendMessage(TextFormat::GREEN . PurePerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.addrank.messages.rank_added_successfully", $args[0]));
        }
        elseif($result === PurePerms::ALREADY_EXISTS)
        {
            $sender->sendMessage(TextFormat::RED . PurePerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.rank.messages.rank_already_exists", $args[0]));
        }
        else
        {
            $sender->sendMessage(TextFormat::RED . PurePerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.addrank.messages.invalid_rank_name", $args[0]));
        }
        
        return true;
    }
    
    public function getPlugin() : Plugin
    {
        return $this->plugin;
    }
}