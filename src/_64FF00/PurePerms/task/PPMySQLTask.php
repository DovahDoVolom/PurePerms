<?php

namespace _64FF00\PurePerms\task;

use _64FF00\PurePerms\PurePerms;

use pocketmine\scheduler\Task;

class PPMySQLTask extends Task
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

    private $db;

    /**
     * @param PurePerms $plugin
     * @param \mysqli $db
     */
    public function __construct(PurePerms $plugin, \mysqli $db)
    {
        $this->plugin = $plugin;
        $this->db = $db;
    }

    /**
     * @param $currentTick
     */
    public function onRun(int $currentTick)
    {
        if($this->db->ping())
        {
            $this->plugin->getLogger()->debug("Connected to MySQLi Server");
        }
        else
        {
            $this->plugin->getLogger()->debug("[MySQL] Warning: " . $this->db->error);
        }
    }
}