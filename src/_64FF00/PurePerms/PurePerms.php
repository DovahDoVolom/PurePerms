<?php

namespace _64FF00\PurePerms;

use _64FF00\PurePerms\cmd\AddGroup;
use _64FF00\PurePerms\cmd\AddParent;
use _64FF00\PurePerms\cmd\DefGroup;
use _64FF00\PurePerms\cmd\FPerms;
use _64FF00\PurePerms\cmd\Groups;
use _64FF00\PurePerms\cmd\GrpInfo;
use _64FF00\PurePerms\cmd\ListGPerms;
use _64FF00\PurePerms\cmd\ListUPerms;
use _64FF00\PurePerms\cmd\PPInfo;
use _64FF00\PurePerms\cmd\PPReload;
use _64FF00\PurePerms\cmd\PPSudo;
use _64FF00\PurePerms\cmd\RmGroup;
use _64FF00\PurePerms\cmd\RmParent;
use _64FF00\PurePerms\cmd\SetGPerm;
use _64FF00\PurePerms\cmd\SetGroup;
use _64FF00\PurePerms\cmd\SetUPerm;
use _64FF00\PurePerms\cmd\UnsetGPerm;
use _64FF00\PurePerms\cmd\UnsetUPerm;
use _64FF00\PurePerms\cmd\UsrInfo;
use _64FF00\PurePerms\data\UserDataManager;
use _64FF00\PurePerms\noeul\NoeulAPI;
use _64FF00\PurePerms\provider\DefaultProvider;
use _64FF00\PurePerms\provider\MySQLProvider;
use _64FF00\PurePerms\provider\ProviderInterface;
use _64FF00\PurePerms\provider\YamlV1Provider;
use _64FF00\PurePerms\task\PPExpDateCheckTask;

use pocketmine\IPlayer;

use pocketmine\level\Level;

use pocketmine\permission\DefaultPermissions;

use pocketmine\Player;

use pocketmine\plugin\PluginBase;

use pocketmine\utils\UUID;

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

    const MAIN_PREFIX = "\x5b\x50\x75\x72\x65\x50\x65\x72\x6d\x73\x3a\x36\x34\x46\x46\x30\x30\x5d";

    const CORE_PERM = "\x70\x70\x65\x72\x6d\x73\x2e\x63\x6f\x6d\x6d\x61\x6e\x64\x2e\x70\x70\x69\x6e\x66\x6f";

    const NOT_FOUND = null;
    const INVALID_NAME = -1;
    const ALREADY_EXISTS = 0;
    const SUCCESS = 1;

    private $isGroupsLoaded = false;

    /** @var PPMessages $messages */
    private $messages;

    /** @var NoeulAPI $noeulAPI */
    private $noeulAPI;

    /** @var ProviderInterface $provider */
    private $provider;

    /** @var UserDataManager $userDataMgr */
    private $userDataMgr;

    private $attachments = [], $groups = [], $pmDefaultPerms = [];

    public function onLoad()
    {
        $this->getServer()->getLogger()->notice(base64_decode('UHVyZVBlcm1zIGJ5IDY0RkYwMCAmIFByb2plY3RJbmZpbml0eSEgI0xFRVQuQ0MNCg0KICA4ODggIDg4OCAgICAuZDg4ODhiLiAgICAgIGQ4ODg4ICA4ODg4ODg4ODg4IDg4ODg4ODg4ODggLmQ4ODg4Yi4gICAuZDg4ODhiLiANCiAgODg4ICA4ODggICBkODhQICBZODhiICAgIGQ4UDg4OCAgODg4ICAgICAgICA4ODggICAgICAgZDg4UCAgWTg4YiBkODhQICBZODhiDQo4ODg4ODg4ODg4ODggODg4ICAgICAgICAgIGQ4UCA4ODggIDg4OCAgICAgICAgODg4ICAgICAgIDg4OCAgICA4ODggODg4ICAgIDg4OA0KICA4ODggIDg4OCAgIDg4OGQ4ODhiLiAgIGQ4UCAgODg4ICA4ODg4ODg4ICAgIDg4ODg4ODggICA4ODggICAgODg4IDg4OCAgICA4ODgNCiAgODg4ICA4ODggICA4ODhQICJZODhiIGQ4OCAgIDg4OCAgODg4ICAgICAgICA4ODggICAgICAgODg4ICAgIDg4OCA4ODggICAgODg4DQo4ODg4ODg4ODg4ODggODg4ICAgIDg4OCA4ODg4ODg4ODg4IDg4OCAgICAgICAgODg4ICAgICAgIDg4OCAgICA4ODggODg4ICAgIDg4OA0KICA4ODggIDg4OCAgIFk4OGIgIGQ4OFAgICAgICAgODg4ICA4ODggICAgICAgIDg4OCAgICAgICBZODhiICBkODhQIFk4OGIgIGQ4OFANCiAgODg4ICA4ODggICAgIlk4ODg4UCIgICAgICAgIDg4OCAgODg4ICAgICAgICA4ODggICAgICAgICJZODg4OFAiICAgIlk4ODg4UCIgDQo='));

        $this->saveDefaultConfig();

        $this->fixConfig();

        $this->messages = new PPMessages($this);

        $this->noeulAPI = new NoeulAPI($this);

        $this->userDataMgr = new UserDataManager($this);

        if($this->getConfigValue("enable-multiworld-perms") === false)
        {
            $this->getLogger()->notice($this->getMessage("logger_messages.onLoad_01"));
            $this->getLogger()->notice($this->getMessage("logger_messages.onLoad_02"));
        }
        else
        {
            $this->getLogger()->notice($this->getMessage("logger_messages.onLoad_03"));
        }
    }
    
    public function onEnable()
    {
        $this->registerCommands();

        $this->setProvider();

        $this->registerPlayers();

        $this->getServer()->getPluginManager()->registerEvents(new PPListener($this), $this);

        $this->getScheduler()->scheduleRepeatingTask(new PPExpDateCheckTask($this), 72000);
    }

    public function onDisable()
    {
        $this->unregisterPlayers();

        if($this->isValidProvider())
            $this->provider->close();
    }

    private function fixConfig()
    {
        $config = $this->getConfig();

        if(!$config->exists("default-language"))
            $config->set("default-language", "en");

        if(!$config->exists("disable-op"))
            $config->set("disable-op", true);

        if(!$config->exists("enable-multiworld-perms"))
            $config->set("enable-multiworld-perms", false);

        if(!$config->exists("enable-noeul-sixtyfour"))
            $config->set("enable-noeul-sixtyfour", false);

        if(!$config->exists("noeul-minimum-pw-length"))
            $config->set("noeul-minimum-pw-length", 6);

        if(!$config->exists("superadmin-ranks"))
            $config->set("superadmin-ranks", ["OP"]);

        $this->saveConfig();
        $this->reloadConfig();
    }

    private function registerCommands()
    {
        $commandMap = $this->getServer()->getCommandMap();

        if($this->getNoeulAPI()->isNoeulEnabled())
            $commandMap->register("pureperms", new PPSudo($this, "ppsudo", $this->getMessage("cmds.ppsudo.desc") . ' #64FF00'));

        $commandMap->register("pureperms", new AddGroup($this, "addgroup", $this->getMessage("cmds.addgroup.desc") . ' #64FF00'));
        $commandMap->register("pureperms", new AddParent($this, "addparent", $this->getMessage("cmds.addparent.desc") . ' #64FF00'));
        $commandMap->register("pureperms", new DefGroup($this, "defgroup", $this->getMessage("cmds.defgroup.desc") . ' #64FF00'));
        $commandMap->register("pureperms", new FPerms($this, "fperms", $this->getMessage("cmds.fperms.desc") . ' #64FF00'));
        $commandMap->register("pureperms", new Groups($this, "groups", $this->getMessage("cmds.groups.desc") . ' #64FF00'));
        $commandMap->register("pureperms", new GrpInfo($this, "grpinfo", $this->getMessage("cmds.grpinfo.desc") . ' #64FF00'));
        $commandMap->register("pureperms", new ListGPerms($this, "listgperms", $this->getMessage("cmds.listgperms.desc") . ' #64FF00'));
        $commandMap->register("pureperms", new ListUPerms($this, "listuperms", $this->getMessage("cmds.listuperms.desc") . ' #64FF00'));
        $commandMap->register("pureperms", new PPInfo($this, "ppinfo", $this->getMessage("cmds.ppinfo.desc") . ' #64FF00'));
        $commandMap->register("pureperms", new PPReload($this, "ppreload", $this->getMessage("cmds.ppreload.desc") . ' #64FF00'));
        $commandMap->register("pureperms", new RmGroup($this, "rmgroup", $this->getMessage("cmds.rmgroup.desc") . ' #64FF00'));
        $commandMap->register("pureperms", new RmParent($this, "rmparent", $this->getMessage("cmds.rmparent.desc") . ' #64FF00'));
        $commandMap->register("pureperms", new SetGPerm($this, "setgperm", $this->getMessage("cmds.setgperm.desc") . ' #64FF00'));
        $commandMap->register("pureperms", new SetGroup($this, "setgroup", $this->getMessage("cmds.setgroup.desc") . ' #64FF00'));
        $commandMap->register("pureperms", new SetUPerm($this, "setuperm", $this->getMessage("cmds.setuperm.desc") . ' #64FF00'));
        $commandMap->register("pureperms", new UnsetGPerm($this, "unsetgperm", $this->getMessage("cmds.unsetgperm.desc") . ' #64FF00'));
        $commandMap->register("pureperms", new UnsetUPerm($this, "unsetuperm", $this->getMessage("cmds.unsetuperm.desc") . ' #64FF00'));
        $commandMap->register("pureperms", new UsrInfo($this, "usrinfo", $this->getMessage("cmds.usrinfo.desc") . ' #64FF00'));

    }

    /**
     * @param bool $onEnable
     */
    private function setProvider($onEnable = true)
    {
        $providerName = $this->getConfigValue("data-provider");

        switch(strtolower($providerName))
        {
            case "mysql":

                $provider = new MySQLProvider($this);

                if($onEnable === true)
                    $this->getLogger()->notice($this->getMessage("logger_messages.setProvider_MySQL"));

                break;

            case "yamlv1":

                $provider = new YamlV1Provider($this);

                if($onEnable === true)
                    $this->getLogger()->notice($this->getMessage("logger_messages.setProvider_YAMLv1"));

                break;

            case "yamlv2":

                $provider = new DefaultProvider($this);

                if($onEnable === true)
                    $this->getLogger()->notice($this->getMessage("logger_messages.setProvider_YAMLv2"));

                break;

            default:

                $provider = new DefaultProvider($this);

                if($onEnable === true)
                    $this->getLogger()->warning($this->getMessage("logger_messages.setProvider_NotFound", "'$providerName'"));

                break;
        }

        if($provider instanceof ProviderInterface)
            $this->provider = $provider;

        $this->updateGroups();
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
     * @param $groupName
     * @return bool
     */
    public function addGroup($groupName)
    {
        $groupsData = $this->getProvider()->getGroupsData();

        if(!$this->isValidGroupName($groupName))
            return self::INVALID_NAME;

        if(isset($groupsData[$groupName]))
            return self::ALREADY_EXISTS;

        $groupsData[$groupName] = [
            "alias" => "",
            "isDefault" => false,
            "inheritance" => [
            ],
            "permissions" => [
            ],
            "worlds" => [
            ]
        ];

        $this->getProvider()->setGroupsData($groupsData);

        $this->updateGroups();

        return self::SUCCESS;
    }

    /**
     * @param $date
     * @return int
     * Example: $date = '1d2h3m';
     */
    public function date2Int($date)
    {
        if(preg_match("/([0-9]+)d([0-9]+)h([0-9]+)m/", $date, $result_array) and count($result_array) === 4)
            return time() + ($result_array[1] * 86400) + ($result_array[2] * 3600) + ($result_array[3] * 60);

        return -1;
    }

    /**
     * @param Player $player
     * @return null|\pocketmine\permission\PermissionAttachment
     */
    public function getAttachment(Player $player)
    {
        $uniqueId = $this->getValidUUID($player);

        if(!isset($this->attachments[$uniqueId]))
            throw new \RuntimeException("Tried to calculate permissions on " .  $player->getName() . " using null attachment");

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
     * @param null $levelName
     * @return PPGroup|null
     */
    public function getDefaultGroup($levelName = null)
    {
        $defaultGroups = [];

        foreach($this->getGroups() as $defaultGroup)
        {
            if($defaultGroup->isDefault($levelName))
                $defaultGroups[] = $defaultGroup;
        }

        if(count($defaultGroups) === 1)
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
            }

            $this->getLogger()->info($this->getMessage("logger_messages.getDefaultGroup_03"));

            foreach($this->getGroups() as $tempGroup)
            {
                if(count($tempGroup->getParentGroups()) === 0)
                {
                    $this->setDefaultGroup($tempGroup, $levelName);

                    return $tempGroup;
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
            /** @var PPGroup $group */
            foreach($this->groups as $group)
            {
                if($group->getAlias() === $groupName)
                    return $group;
            }

            $this->getLogger()->debug($this->getMessage("logger_messages.getGroup_01", $groupName));

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
        if($this->isGroupsLoaded !== true)
            throw new \RuntimeException("No groups loaded, maybe a provider error?");

        return $this->groups;
    }

    /**
     * @param $node
     * @param array ...$vars
     * @return string
     */
    public function getMessage($node, ...$vars)
    {
        return $this->messages->getMessage($node, ...$vars);
    }

    /**
     * @return NoeulAPI
     */
    public function getNoeulAPI()
    {
        return $this->noeulAPI;
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
            foreach($this->getServer()->getLevels() as $level)
            {
                $levelName = $level->getName();

                if($this->userDataMgr->getGroup($player, $levelName) === $group)
                    $users[] = $player;
            }
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
        // TODO: Fix this
        $group = $this->userDataMgr->getGroup($player, $levelName);

        $groupPerms = $group->getGroupPermissions($levelName);
        $userPerms = $this->userDataMgr->getUserPermissions($player, $levelName);

        return array_merge($groupPerms, $userPerms);
    }

    /**
     * @param $userName
     * @return Player
     */
    public function getPlayer($userName)
    {
        $player = $this->getServer()->getPlayer($userName);

        return $player instanceof Player ? $player : $this->getServer()->getOfflinePlayer($userName);
    }

    /**
     * @return array
     */
    public function getPocketMinePerms()
    {
        if($this->pmDefaultPerms === [])
        {
            /** @var \pocketmine\permission\Permission $permission */
            foreach($this->getServer()->getPluginManager()->getPermissions() as $permission)
            {
                if(strpos($permission->getName(), DefaultPermissions::ROOT) !== false)
                    $this->pmDefaultPerms[] = $permission;
            }
        }

        return $this->pmDefaultPerms;
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
        if(!$this->isValidProvider())
            $this->setProvider(false);

        return $this->provider;
    }

    /**
     * @return UserDataManager
     */
    public function getUserDataMgr()
    {
        return $this->userDataMgr;
    }

    /**
     * @param Player $player
     * @return null|string
     */
    public function getValidUUID(Player $player)
    {
        $uuid = $player->getUniqueId();

        if($uuid instanceof UUID)
            return $uuid->toString();

        $this->getLogger()->debug("Invalid UUID detected! *cri* (userName: " . $player->getName() . ", isConnected: " . ($player->isConnected() ? "true" : "false") . ", isOnline: " . ($player->isOnline() ? "true" : "false") . ", isValid: " . ($player->isValid() ? "true" : "false") .  ")");

        return null;
    }

    /**
     * @param $groupName
     * @return int
     */
    public function isValidGroupName($groupName)
    {
        return preg_match('/[0-9a-zA-Z\xA1-\xFE]$/', $groupName);
    }

    /**
     * @return bool
     */
    public function isValidProvider()
    {
        if(!isset($this->provider) || ($this->provider === null) || !($this->provider instanceof ProviderInterface))
            return false;

        return true;
    }

    /**
     * @param Player $player
     */
    public function registerPlayer(Player $player)
    {
        $this->getLogger()->debug($this->getMessage("logger_messages.registerPlayer", $player->getName()));

        $uniqueId = $this->getValidUUID($player);

        if(!isset($this->attachments[$uniqueId]))
        {
            $attachment = $player->addAttachment($this);

            $this->attachments[$uniqueId] = $attachment;

            $this->updatePermissions($player);
        }
    }

    public function registerPlayers()
    {
        foreach($this->getServer()->getOnlinePlayers() as $player)
        {
            $this->registerPlayer($player);
        }
    }

    public function reload()
    {
        $this->reloadConfig();
        $this->saveDefaultConfig();

        $this->messages->reloadMessages();

        $this->setProvider(false);

        foreach($this->getServer()->getOnlinePlayers() as $player)
        {
            $this->updatePermissions($player);
        }
    }

    /**
     * @param $groupName
     * @return bool
     */
    public function removeGroup($groupName)
    {
        if(!$this->isValidGroupName($groupName))
            return self::INVALID_NAME;

        $groupsData = $this->getProvider()->getGroupsData();

        if(!isset($groupsData[$groupName]))
            return self::NOT_FOUND;

        unset($groupsData[$groupName]);

        $this->getProvider()->setGroupsData($groupsData);

        $this->updateGroups();

        return self::SUCCESS;
    }

    /**
     * @param PPGroup $group
     * @param $levelName
     */
    public function setDefaultGroup(PPGroup $group, $levelName = null)
    {
        foreach($this->getGroups() as $currentGroup)
        {
            if($levelName === null)
            {
                $isDefault = $currentGroup->getNode("isDefault");

                if($isDefault)
                    $currentGroup->removeNode("isDefault");
            }
            else
            {
                $isDefault = $currentGroup->getWorldNode($levelName, "isDefault");

                if($isDefault)
                    $currentGroup->removeWorldNode($levelName, "isDefault");
            }
        }

        $group->setDefault($levelName);
    }

    /**
     * @param IPlayer $player
     * @param PPGroup $group
     * @param null $levelName
     * @param int $time
     */
    public function setGroup(IPlayer $player, PPGroup $group, $levelName = null, $time = -1)
    {
        $this->userDataMgr->setGroup($player, $group, $levelName, $time);
    }

    public function sortGroupData()
    {
        foreach($this->getGroups() as $groupName => $ppGroup)
        {
            $ppGroup->sortPermissions();

            if($this->getConfigValue("enable-multiworld-perms"))
            {
                /** @var Level $level */
                foreach($this->getServer()->getLevels() as $level)
                {
                    $levelName = $level->getName();

                    $ppGroup->createWorldData($levelName);
                }
            }
        }
    }

    public function updateGroups()
    {
        if(!$this->isValidProvider())
            throw new \RuntimeException("Failed to load groups: Invalid data provider");

        // Make group list empty first to reload it
        $this->groups = [];

        foreach(array_keys($this->getProvider()->getGroupsData()) as $groupName)
        {
            $this->groups[$groupName] = new PPGroup($this, $groupName);
        }

        if(empty($this->groups))
            throw new \RuntimeException("No groups found, I guess there's definitely something wrong with your data provider... *cough cough*");

        $this->isGroupsLoaded = true;

        $this->sortGroupData();
    }

    /**
     * @param IPlayer $player
     * @param string|null $levelName
     */
    public function updatePermissions(IPlayer $player, string $levelName = null)
    {
        if($player instanceof Player)
        {
            if($this->getConfigValue("enable-multiworld-perms") == null) {
                $levelName = null;
            }elseif($levelName == null) {
                $levelName = $player->getLevel()->getName();
            }

            $permissions = [];

            /** @var string $permission */
            foreach($this->getPermissions($player, $levelName) as $permission)
            {
                if($permission === '*')
                {
                    foreach($this->getServer()->getPluginManager()->getPermissions() as $tmp)
                    {
                        $permissions[$tmp->getName()] = true;
                    }
                }
                else
                {
                    $isNegative = substr($permission, 0, 1) === "-";

                    if($isNegative)
                        $permission = substr($permission, 1);

                    $permissions[$permission] = !$isNegative;
                }
            }

            $permissions[self::CORE_PERM] = true;

            /** @var \pocketmine\permission\PermissionAttachment $attachment */
            $attachment = $this->getAttachment($player);

            $attachment->clearPermissions();

            $attachment->setPermissions($permissions);
        }
    }

    /**
     * @param PPGroup $group
     */
    public function updatePlayersInGroup(PPGroup $group)
    {
        foreach($this->getServer()->getOnlinePlayers() as $player)
        {
            if($this->userDataMgr->getGroup($player) === $group)
                $this->updatePermissions($player);
        }
    }

    /**
     * @param Player $player
     */
    public function unregisterPlayer(Player $player)
    {
        $this->getLogger()->debug($this->getMessage("logger_messages.unregisterPlayer", $player->getName()));

        $uniqueId = $this->getValidUUID($player);

        // Do not try to remove attachments with invalid unique ids
        if($uniqueId !== null)
        {
            if(isset($this->attachments[$uniqueId]))
                $player->removeAttachment($this->attachments[$uniqueId]);

            unset($this->attachments[$uniqueId]);
        }
    }

    public function unregisterPlayers()
    {
        foreach($this->getServer()->getOnlinePlayers() as $player)
        {
            $this->unregisterPlayer($player);
        }
    }
}
