<?php

namespace _64FF00\PurePerms;

use pocketmine\event\Listener;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\event\player\PlayerQuitEvent;

class PPListener implements Listener
{
	public function __construct(PurePerms $plugin)
	{
		$this->plugin = $plugin;
	}
	
	public function onLevelChange(EntityLevelChangeEvent $event)
	{
		$player = $event->getEntity();
		
		$isMultiWorldPermsEnabled = $this->plugin->getPPConfig()->getValue("enable-multiworld-perms");
		
		$levelName = $isMultiWorldPermsEnabled ? $event->getTarget()->getName() : null;
		
		if($player instanceof Player)
		{
			$this->plugin->updatePermissions($player, $levelName);
		}
	}
	
	public function onPlayerJoin(PlayerJoinEvent $event)
	{
		$player = $event->getPlayer();
		
		$isMultiWorldPermsEnabled = $this->plugin->getPPConfig()->getValue("enable-multiworld-perms");
		
		$levelName = $isMultiWorldPermsEnabled ? $player->getLevel()->getName() : null;
		
		$this->plugin->updatePermissions($player, $levelName);
	}

	public function onPlayerKick(PlayerKickEvent $event)
	{
		$player = $event->getPlayer();
		
		$this->plugin->removeAttachment($player);
	}

	public function onPlayerQuit(PlayerQuitEvent $event)
	{
		$player = $event->getPlayer();
		
		$this->plugin->removeAttachment($player);
	}
}