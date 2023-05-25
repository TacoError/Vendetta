<?php namespace taco\vendetta\sessions;

use pocketmine\permission\PermissionAttachment;
use pocketmine\player\Player;
use taco\vendetta\Main;
use taco\vendetta\Manager;

class PlayerSession {

    public const CHAT_PUBLIC = 0;

    public const CHAT_FACTION = 1;

    private Player $player;

    private int $kills = 0;

    private int $deaths = 0;

    private int $killStreak = 0;

    private string $faction = "";

    private string $language = "English";

    private int $chatMode = 0;

    private string $rank = "Guest";

    private array $permissions = [];

    /** @var array<PermissionAttachment> */
    private array $attachments = [];

    public function __construct(Player $player) {
        $this->player = $player;
        Main::getInstance()->getDB()->executeSelect("get_data", ["xuid" => $player->getXuid()], function(array $rows) : void {
            if (count($rows) < 1) {
                return;
            }
            $data = $rows[0];
            $this->kills = $data["kills"] ?? 0;
            $this->deaths = $data["deaths"] ?? 0;
            $this->killStreak = $data["killStreak"] ?? 0;
            $this->faction = $data["faction"] ?? "";
            $this->language = $data["language"] ?? "English";
            $this->rank = $data["rank"] ?? "Guest";
            $this->permissions = unserialize($data["permissions"] ?? serialize([]));

            if (!isset(Main::getInstance()->getMessages()[$this->language])) {
                $this->language = "English";
            }
            $faction = Manager::getFactionManager()->getFactionFromName($this->faction);
            if (is_null($faction)) {
                $this->faction = "";
            } else if (!$faction->isPlayerInFaction($this->player->getName())) {
                $this->faction = "";
            }
        });
    }

    public function close() : void {
        Main::getInstance()->getDB()->executeGeneric("set_data", [
            "xuid" => $this->player->getXuid(),
            "playerName" => $this->player->getName(),
            "kills" => $this->kills,
            "deaths" => $this->deaths,
            "killStreak" => $this->killStreak,
            "faction" => $this->faction,
            "language" => $this->language,
            "rank" => $this->rank,
            "permissions" => serialize($this->permissions)
        ]);
    }

    public function onJoin() : void {

        $this->applyPermissions();
        $this->player->sendMessage($this->getMessage("welcome"));
    }

    public function getPlayer() : Player {
        return $this->player;
    }

    public function getMessage(string $name) : string {
        return Main::getInstance()->getMessages()[$this->language][$name] ?? "§cCould not find message. Please contact server administration.";
    }

    public function getFaction() : string {
        return $this->faction;
    }

    public function isInFaction() : bool {
        return $this->faction !== "";
    }

    public function setFaction(string $new) : void {
        if ($new == "") {
            $this->setChatMode(self::CHAT_PUBLIC);
        }
        $this->faction = $new;
    }

    public function setChatMode(int $mode) : void {
        $this->chatMode = $mode;
    }

    public function getChatMode() : int {
        return $this->chatMode;
    }

    public function setRank(string $rank) : void {
        $this->rank = $rank;
        $this->applyPermissions();
    }

    public function getRank() : string {
        return $this->rank;
    }

    public function applyPermissions() : void {
        foreach($this->attachments as $attachment) {
            $this->player->removeAttachment($attachment);
        }
        $this->attachments = [];
        foreach([...$this->permissions, ...Manager::getRankManager()->getRank($this->rank)->getPermissions()] as $permission) {
            $this->attachments[] = $this->player->addAttachment(Main::getInstance(), $permission, $permission);
        }
    }

    public function addPermission(string $permission) : void {
        $this->permissions[] = $permission;
        $this->applyPermissions();
    }

    public function removePermission(string $permission) : void {
        $this->permissions = array_diff($this->permissions, [$permission]);
        $this->applyPermissions();
    }

}