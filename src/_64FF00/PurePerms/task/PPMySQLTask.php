<?php

namespace _64FF00\PurePerms\task;

use _64FF00\PurePerms\PurePerms;

use pocketmine\scheduler\PluginTask;

class PPMySQLTask extends PluginTask
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

	private $db;

    /**
     * @param PurePerms $plugin
     * @param \mysqli $db
     */
	public function __construct(PurePerms $plugin, \mysqli $db)
    {
		parent::__construct($plugin);

        $this->plugin = $plugin;
		$this->db = $db;
	}

    /**
     * @param $currentTick
     */
	public function onRun($currentTick)
    {
		if($this->db->ping())
        {
            $this->plugin->getLogger()->debug("Connected to MySQLi Server");
        }
        else
        {
            $this->plugin->getLogger()->debug("Warning: " . $this->db->error);
        }
	}
}