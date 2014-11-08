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
	}
	
	public function onPlayerJoin(PlayerJoinEvent $event)
	{
	}

	public function onPlayerKick(PlayerKickEvent $event)
	{
	}

	public function onPlayerQuit(PlayerQuitEvent $event)
	{
	}
}