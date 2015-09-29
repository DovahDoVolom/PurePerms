<?php

namespace _64FF00\PurePerms;

use _64FF00\PurePerms\provider\DefaultProvider;
use _64FF00\PurePerms\provider\ProviderInterface;
use _64FF00\PurePerms\provider\SQLite3Provider;

use pocketmine\permission\PermissionAttachment;

use pocketmine\Player;

use pocketmine\plugin\PluginBase;

class PurePerms extends PluginBase
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

    const CORE_PERM = "\x70\x70\x65\x72\x6d\x73\x2e\x63\x6f\x6d\x6d\x61\x6e\x64\x2e\x70\x70\x69\x6e\x66\x6f";

    private $isGroupsLoaded = false;

    /** @var PPMessages $messages */
    private $messages;

    /** @var ProviderInterface $provider */
    private $provider;

    private $attachments = [], $groups = [];

    public function onLoad()
    {
        $this->saveDefaultConfig();

        /** @var PPMessages messages */
        $this->messages = new PPMessages($this);

        if($this->getConfigValue("enable-multiworld-perms") === false)
        {
            $this->getLogger()->notice($this->getMessage("logger_messages.onEnable_01"));
            $this->getLogger()->notice($this->getMessage("logger_messages.onEnable_02"));
        }
        else
        {
            $this->getLogger()->notice($this->getMessage("logger_messages.onEnable_03"));
        }
    }
    
    public function onEnable()
    {
        $this->setProvider();

        $this->registerAll();
    }

    public function onDisable()
    {
        $this->unregisterAll();

        if($this->isValidProvider()) $this->provider->close();
    }

    /**
     * @param bool $onEnable
     */
    private function setProvider($onEnable = true)
    {
        $providerName = $this->getConfigValue("data-provider");

        switch(strtolower($providerName))
        {
            case "sqlite3":

                $provider = new SQLite3Provider($this);

                if($onEnable === true) $this->getLogger()->info($this->getMessage("logger_messages.setProvider_SQLite3"));

                break;

            case "yaml":

                $provider = new DefaultProvider($this);

                if($onEnable === true) $this->getLogger()->info($this->getMessage("logger_messages.setProvider_YAML"));

                break;

            default:

                $provider = new DefaultProvider($this);

                if($onEnable === true) $this->getLogger()->warning($this->getMessage("logger_messages.setProvider_NotFound"));

                break;
        }

        if(!$this->isValidProvider()) $this->provider = $provider;

        $this->loadGroups();
    }

    /*
          888  888          d8888 8888888b. 8888888
          888  888         d88888 888   Y88b  888
        888888888888      d88P888 888    888  888
          888  888       d88P 888 888   d88P  888
          888  888      d88P  888 8888888P"   888
        888888888888   d88P   888 888         888
          888  888    d8888888888 888         888
          888  888   d88P     888 888       8888888
    */

    /**
     * @param Player $player
     * @return null|\pocketmine\permission\PermissionAttachment
     */
    public function getAttachment(Player $player)
    {
        $uniqueId = $this->getValidUUID($player);

        if(!isset($this->attachments[$uniqueId])) throw new \RuntimeException("Tried to calculate permissions on " .  $player->getName() . " using null attachment");

        return $this->attachments[$uniqueId];
    }

    /**
     * @param $key
     * @return null
     */
    public function getConfigValue($key)
    {
        $value = $this->getConfig()->getNested($key);

        if($value === null)
        {
            $this->getLogger()->warning($this->getMessage("logger_messages.getConfigValue_01", $key));

            return null;
        }

        return $value;
    }

    /**
     * @return PPGroup|null
     */
    public function getDefaultGroup()
    {
        $defaultGroups = [];

        foreach($this->getGroups() as $defaultGroup)
        {
            if($defaultGroup->isDefault()) array_push($defaultGroups, $defaultGroup);
        }

        if(count($defaultGroups) == 1)
        {
            return $defaultGroups[0];
        }
        else
        {
            if(count($defaultGroups) > 1)
            {
                $this->getLogger()->warning($this->getMessage("logger_messages.getDefaultGroup_01"));
            }
            elseif(count($defaultGroups) <= 0)
            {
                $this->getLogger()->warning($this->getMessage("logger_messages.getDefaultGroup_02"));

                $defaultGroups = $this->getGroups();
            }

            $this->getLogger()->info($this->getMessage("logger_messages.getDefaultGroup_03"));

            foreach($defaultGroups as $defaultGroup)
            {
                if(count($defaultGroup->getInheritedGroups()) == 0)
                {
                    $this->setDefaultGroup($defaultGroup);

                    return $defaultGroup;
                }
            }
        }

        return null;
    }

    /**
     * @param $groupName
     * @return PPGroup|null
     */
    public function getGroup($groupName)
    {
        if(!isset($this->groups[$groupName]))
        {
            $this->getLogger()->warning($this->getMessage("logger_messages.getGroup_01", $groupName));

            return null;
        }

        /** @var PPGroup $group */
        $group = $this->groups[$groupName];

        if(empty($group->getData()))
        {
            $this->getLogger()->warning($this->getMessage("logger_messages.getGroup_02", $groupName));

            return null;
        }

        return $group;
    }

    /**
     * @return PPGroup[]
     */
    public function getGroups()
    {
        if($this->isGroupsLoaded != true) throw new \RuntimeException("No groups loaded, maybe a provider error?");

        return $this->groups;
    }

    /**
     * @param $node
     * @param ...$vars
     * @return string
     */
    public function getMessage($node, ...$vars)
    {
        return $this->messages->getMessage($node, ...$vars);
    }

    /**
     * @param PPGroup $group
     * @return array
     */
    public function getOnlinePlayersInGroup(PPGroup $group)
    {
        $users = [];

        foreach($this->getServer()->getOnlinePlayers() as $player)
        {
            if($this->getUser($player)->getGroup() === $group) $users[] = $player;
        }

        return $users;
    }

    /**
     * @param IPlayer $player
     * @param $levelName
     * @return array
     */
    public function getPermissions(IPlayer $player, $levelName)
    {
        $user = $this->getUser($player);
        $group = $user->getGroup($levelName);

        $groupPerms = $group->getGroupPermissions($levelName);
        $userPerms = $user->getUserPermissions($levelName);

        return array_merge($groupPerms, $userPerms);
    }

    /**
     * @return string
     */
    public function getPPVersion()
    {
        return $this->getDescription()->getVersion();
    }

    /**
     * @return ProviderInterface
     */
    public function getProvider()
    {
        if(!$this->isValidProvider()) $this->setProvider(false);

        return $this->provider;
    }

    /**
     * @param IPlayer $player
     * @return PPUser
     */
    public function getUser(IPlayer $player)
    {
        return new PPUser($this, $player);
    }

    /**
     * @param Player $player
     * @return null|string
     */
    public function getValidUUID(Player $player)
    {
        if(class_exists('\pocketmine\utils\UUID'))
        {
            $uniqueId = $player->getUniqueId()->toString();
        }
        else
        {
            $uniqueId = $player->getUniqueId();
        }

        return $uniqueId;
    }

    /**
     * @return bool
     */
    public function isValidProvider()
    {
        if(!isset($this->provider) || $this->provider == null || !($this->provider instanceof ProviderInterface)) return false;

        return true;
    }

    public function loadGroups()
    {
        if(!$this->isValidProvider()) throw new \RuntimeException("Failed to load groups: Invalid Data Provider");

        foreach(array_keys($this->getProvider()->getGroupsData()) as $groupName)
        {
            $this->groups[$groupName] = new PPGroup($this, $groupName);
        }

        $this->isGroupsLoaded = true;

        $this->sortGroupPermissions();
    }

    public function registerAll()
    {
        foreach($this->getServer()->getOnlinePlayers() as $player)
        {
            $this->registerPlayer($player);
        }
    }

    /**
     * @param Player $player
     */
    public function registerPlayer(Player $player)
    {
        $this->getLogger()->debug($this->getMessage("logger_messages.registerPlayer", $player->getName()));

        $uniqueId = $this->getValidUUID($player);

        $attachment = $player->addAttachment($this);

        $this->attachments[$uniqueId] = $attachment;

        $this->updatePermissions($player);
    }

    public function reload()
    {
        $this->reloadConfig();
        $this->saveDefaultConfig();

        $this->messages->reloadMessages();

        if(!$this->isValidProvider()) $this->setProvider(false);
    }

    /**
     * @param $groupName
     * @return bool
     */
    public function removeGroup($groupName)
    {
        $groupsData = $this->getProvider()->getGroupsData(true);

        if(!isset($groupsData[$groupName])) return false;

        unset($groupsData[$groupName]);

        $this->getProvider()->setGroupsData($groupsData);

        return true;
    }

    /**
     * @param PPGroup $group
     */
    public function setDefaultGroup(PPGroup $group)
    {
        foreach($this->getGroups() as $currentGroup)
        {
            $isDefault = $currentGroup->getNode("isDefault");

            if($isDefault) $currentGroup->removeNode("isDefault");
        }

        $group->setDefault();
    }

    /**
     * @param IPlayer $player
     * @param PPGroup $group
     * @param null $levelName
     */
    public function setGroup(IPlayer $player, PPGroup $group, $levelName = null)
    {
        $this->getUser($player)->setGroup($group, $levelName);
    }

    public function sortGroupPermissions()
    {
        foreach($this->getGroups() as $groupName => $ppGroup)
        {
            $ppGroup->sortPermissions();
        }
    }

    /**
     * @param IPlayer $player
     */
    public function updatePermissions(IPlayer $player)
    {
        if($player instanceof Player)
        {
            $levelName = $this->getConfigValue("enable-multiworld-perms") ? $player->getLevel()->getName() : null;

            $permissions = [];

            foreach($this->getPermissions($player, $levelName) as $permission)
            {
                if($permission === "*")
                {
                    foreach($this->getServer()->getPluginManager()->getPermissions() as $tmp)
                    {
                        $permissions[$tmp->getName()] = true;
                    }
                }
                else
                {
                    $isNegative = substr($permission, 0, 1) === "-";
                    if($isNegative) $permission = substr($permission, 1);

                    $value = !$isNegative;
                    if($permission === self::CORE_PERM) $value = true;

                    $permissions[$permission] = $value;
                }
            }

            /** @var PermissionAttachment $attachment */
            $attachment = $this->getAttachment($player);

            $attachment->clearPermissions();

            $attachment->setPermissions($permissions);
        }
    }

    public function unregisterAll()
    {
        foreach($this->getServer()->getOnlinePlayers() as $player)
        {
            $this->unregisterPlayer($player);
        }
    }

    /**
     * @param Player $player
     */
    public function unregisterPlayer(Player $player)
    {
        $this->getLogger()->debug($this->getMessage("logger_messages.unregisterPlayer", $player->getName()));

        $uniqueId = $this->getValidUUID($player);

        if(isset($this->attachments[$uniqueId])) $player->removeAttachment($this->attachments[$uniqueId]);

        unset($this->attachments[$uniqueId]);
    }
}
