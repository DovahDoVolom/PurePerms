<?php

namespace _64FF00\PurePerms;

use pocketmine\event\Listener;

class PPListener implements Listener
{
	public function __construct(PurePerms $plugin)
	{
		$this->plugin = $plugin;
	}
}