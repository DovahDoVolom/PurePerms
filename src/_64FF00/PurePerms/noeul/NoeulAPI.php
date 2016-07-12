<?php

namespace _64FF00\PurePerms\noeul;

use _64FF00\PurePerms\PurePerms;

use pocketmine\IPlayer;

use pocketmine\permission\PermissionAttachment;

use pocketmine\Player;

use pocketmine\utils\TextFormat;

class NoeulAPI
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

    /*
     * 1. 플레이어가 접속하고 SimpleAuth 인증이 끝나면 모든 퍼미션 차단 후 메시지 출력
     * 2. 플레이어 등록이 되어있지 않으면 새로 등록
     * 3. 명령어와 비밀번호 입력 후 퍼미션 다시 설정
     */

    const NOEUL_VERSION = '1.0.0';

    private $needAuth = [];

    public function __construct(PurePerms $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function auth(Player $player)
    {
        // TODO

        if($this->isAuthed($player))
            return true;

        if(isset($this->needAuth[spl_object_hash($player)]))
        {
            $attachment = $this->needAuth[spl_object_hash($player)];

            $player->removeAttachment($attachment);

            unset($this->needAuth[spl_object_hash($player)]);
        }

        $player->sendMessage(TextFormat::GREEN . PurePerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.ppsudo.messages.successfully_logged_in"));

        return true;
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function deAuth(Player $player)
    {
        $attachment = $player->addAttachment($this->plugin);

        $this->removePermissions($attachment);

        $this->needAuth[spl_object_hash($player)] = $attachment;

        $this->sendAuthMsg($player);

        return true;
    }

    /**
     * @param $password
     * @return bool|string
     */
    public function hash($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * @param $password
     * @param $hash
     * @return bool
     */
    public function hashEquals($password, $hash)
    {
        return password_verify($password, $hash);
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function isAuthed(Player $player)
    {
        return !isset($this->needAuth[spl_object_hash($player)]);
    }

    /**
     * @return bool
     */
    public function isNoeulEnabled()
    {
        return $this->plugin->getConfigValue("enable-noeul-sixtyfour");
    }

    /**
     * @return bool
     */
    public function isRegistered($player)
    {
        return !($this->plugin->getUserDataMgr()->getNode($player, 'noeulPW') === null);
    }

    /**
     * @param IPlayer $player
     * @param $password
     * @return bool
     */
    public function register(IPlayer $player, $password)
    {
        if(!$this->isRegistered($player))
        {
            $hash = $this->hash($password);

            $this->plugin->getUserDataMgr()->setNode($player, 'noeulPW', $hash);

            return true;
        }

        return false;
    }

    /**
     * @param PermissionAttachment $attachment
     */
    private function removePermissions(PermissionAttachment $attachment)
    {
        $permissions = [];

        foreach($this->plugin->getServer()->getPluginManager()->getPermissions() as $permission)
        {
            $permissions[$permission->getName()] = false;
        }

        $permissions["pocketmine.command.help"] = true;
        $permissions["pperms.noeul.ppsudo"] = true;

        ksort($permissions);

        $attachment->setPermissions($permissions);
    }

    /**
     * @param Player $player
     */
    public function sendAuthMsg(Player $player)
    {
        $player->sendMessage(TextFormat::RED . PurePerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.ppsudo.messages.deauth_01", self::NOEUL_VERSION));
        $player->sendMessage(TextFormat::RED . PurePerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.ppsudo.messages.deauth_02"));

        $player->sendMessage(TextFormat::RED . PurePerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.ppsudo.messages.deauth_03"));
    }

    /**
     * @param IPlayer $player
     * @return bool
     */
    public function unregister(IPlayer $player)
    {
        if($this->isRegistered($player))
        {
            $this->plugin->getUserDataMgr()->removeNode($player, 'noeulPW');

            return true;
        }

        return false;
    }
}