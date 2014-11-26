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
		
		$levelName = $event->getTarget()->getName();
		
		if($player instanceof Player)
		{
			$isMultiWorldPermsEnabled = $this->plugin->getPPConfig()->getValue("enable-multiworld-perms");
			
			if(!$isMultiWorldPermsEnabled)
			{
					$this->plugin->updatePermissions($player);
			}
			else
			{
				$this->plugin->updatePermissions($player, $levelName);
			}
		}
	}
	
	public function onPlayerJoin(PlayerJoinEvent $event)
	{
		$player = $event->getPlayer();
		
		$levelName = $player->getLevel()->getName();
		
		$isMultiWorldPermsEnabled = $this->plugin->getPPConfig()->getValue("enable-multiworld-perms");
		
		if(!$isMultiWorldPermsEnabled)
		{
			$this->plugin->updatePermissions($player);
		}
		else
		{
			$this->plugin->updatePermissions($player, $levelName);
		}
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