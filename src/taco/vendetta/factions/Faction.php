<?php namespace taco\vendetta\factions;

use Error;
use pocketmine\player\Player;
use pocketmine\Server;
use taco\vendetta\Main;
use taco\vendetta\Manager;

class Faction {

    public const RANK_OWNER = 0;

    public const RANK_CAPTAIN = 1;

    public const RANK_MEMBER = 2;

    public const RANK_RECRUIT = 3;

    public const RANK_TO_NAME = [
        self::RANK_OWNER => "Owner",
        self::RANK_CAPTAIN => "Captain",
        self::RANK_MEMBER => "Member",
        self::RANK_RECRUIT => "Recruit"
    ];

    private string $name;

    private string $description;

    private int $creationTimeStamp;

    private string $owner;

    private array $officers;

    private array $members;

    private array $recruits;

    private int $balance;

    private int $value;

    private int $power;

    private array $allies;

    private array $invites;

    private array $allyRequests;

    public function __construct(
        string $name,
        string $description,
        int $creationTimeStamp,
        string $owner,
        array $officers,
        array $members,
        array $recruits,
        int $balance,
        int $value,
        int $power,
        array $allies,
        array $invites,
        array $allyRequests
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->creationTimeStamp = $creationTimeStamp;
        $this->owner = $owner;
        $this->officers = $officers;
        $this->members = $members;
        $this->recruits = $recruits;
        $this->balance = $balance;
        $this->value = $value;
        $this->power = $power;
        $this->allies = $allies;
        $this->invites = $invites;
        $this->allyRequests = $allyRequests;
    }

    public function save() : void {
        Main::getInstance()->getDB()->executeGeneric("set_data_faction", [
            "name" => $this->name,
            "description" => $this->description,
            "creationTimeStamp" => $this->creationTimeStamp,
            "owner" => $this->owner,
            "officers" => serialize($this->officers),
            "members" => serialize($this->members),
            "recruits" => serialize($this->recruits),
            "balance" => $this->balance,
            "value" => $this->value,
            "power" => $this->power,
            "allies" => serialize($this->allies),
            "invites" => serialize($this->invites),
            "allyRequests" => serialize($this->allyRequests)
        ]);
    }

    public function messageEntireFactionTranslated(string $msg, ...$values) : void {
        /** @var Player $member */
        foreach($this->getOnlineMembers() as $member) {
            $member->sendMessage(sprintf(Manager::getSessionManager()->getSession($member)->getMessage($msg), $values));
        }
    }

    public function messageEntireFaction(string $msg) : void {
        /** @var Player $member */
        foreach($this->getOnlineMembers() as $member) {
            $member->sendMessage($msg);
        }
    }

    public function getFullMemberList() : array {
        return [$this->owner, ...$this->officers, ...$this->members, ...$this->recruits];
    }

    public function getOnlineMembers() : array {
        $list = [];
        foreach($this->getFullMemberList() as $name) {
            if (!is_null($player = Server::getInstance()->getPlayerExact($name))) {
                $list[] = $player;
            }
        }
        return $list;
    }

    public function isPlayerInFaction(Player|string $player) : bool {
        if ($player instanceof Player) {
            $player = $player->getName();
        }
        return in_array($player, $this->getFullMemberList());
    }

    public function getRank(Player|string $player) : int {
        $name = $player instanceof Player ? $player->getName() : $player;
        if ($this->owner == $name) {
            return self::RANK_OWNER;
        } else if (in_array($name, $this->officers)) {
            return self::RANK_CAPTAIN;
        } else if (in_array($name, $this->members)) {
            return self::RANK_MEMBER;
        }
        return self::RANK_RECRUIT;
    }

    public function removeInvite(string $name) : void {
        unset($this->invites[$name]);
    }

    public function getTimeLeftOnInvite(string $name) : ?int {
        return $this->invites[$name] ?? null;
    }

    public function isInvited(string $name) : bool {
        return isset($this->invites[$name]);
    }

    public function tickInvites() : void {
        foreach($this->invites as $name => $timeLeft) {
            $this->invites[$name]--;
            if ($this->invites[$name] < 1) {
                unset($this->invites[$name]);
            }
        }
    }

    public function invite(string $name) : void {
        $this->invites[$name] = 60;
    }

    public function getName() : string {
        return $this->name;
    }

    public function addMember(string $name, int $role = self::RANK_RECRUIT) : void {
        switch($role) {
            case self::RANK_RECRUIT:
                $this->recruits[] = $name;
                break;
            case self::RANK_MEMBER:
                $this->members[] = $name;
                break;
            case self::RANK_CAPTAIN:
                $this->officers[] = $name;
                break;
            default:
                throw new Error("Could not find role \"" . $role . "\" whilst adding member.");
        }
        $this->messageEntireFactionTranslated("member-join", $name);
    }

}