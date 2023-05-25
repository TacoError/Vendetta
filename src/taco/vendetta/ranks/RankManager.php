<?php namespace taco\vendetta\ranks;

use pocketmine\Server;
use taco\vendetta\Main;
use taco\vendetta\ranks\commands\AddPermissionCommand;
use taco\vendetta\ranks\commands\RemovePermissionCommand;

class RankManager {

    /** @var array<string, Rank> */
    private array $ranks = [];

    public function __construct() {
        foreach(Main::getInstance()->getConfig()->get("ranks") as $name => $data) {
            $this->ranks[$name] = new Rank($name, $data["fancy-name"], $data["permissions"]);
        }
        Server::getInstance()->getCommandMap()->registerAll("Vendetta", [
            new AddPermissionCommand(),
            new RemovePermissionCommand()
        ]);
    }

    public function getRank(string $name) : ?Rank {
        return $this->ranks[$name] ?? null;
    }

    public function getRankList() : array {
        return array_keys($this->ranks);
    }

}