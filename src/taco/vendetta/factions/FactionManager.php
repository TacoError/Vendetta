<?php namespace taco\vendetta\factions;

use pocketmine\Server;
use taco\vendetta\factions\commands\FactionCommand;
use taco\vendetta\factions\tasks\InviteTask;
use taco\vendetta\Main;
class FactionManager {

    private array $factions = [];

    public function __construct() {
        Main::getInstance()->getDB()->executeSelect("get_all_factions", [], function(array $factions) : void {
            foreach($factions as $data) {
                $this->factions[$data["name"]] = new Faction(
                    $data["name"],
                    $data["description"],
                    $data["creationTimeStamp"],
                    $data["owner"],
                    unserialize($data["officers"]),
                    unserialize($data["members"]),
                    unserialize($data["recruits"]),
                    $data["balance"],
                    $data["value"],
                    $data["power"],
                    unserialize($data["allies"]),
                    unserialize($data["invites"]),
                    unserialize($data["allyRequests"])
                );
            }
        });
        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new InviteTask(), 20);
        Server::getInstance()->getCommandMap()->register("Vendetta", new FactionCommand());
    }

    public function createFaction(string $owner, string $name) : void {
        $this->factions[$name] = new Faction($name, "Basic faction description.", time(), $owner, [], [], [], 0, 0, 50, [], [], []);
    }

    public function factionExists(string $name) : bool {
        return !is_null($this->getFactionFromName($name));
    }

    public function getFactionFromName(string $name) : ?Faction {
        return $this->factions[$name] ?? null;
    }

    public function saveAllFactions() : void {
        foreach($this->factions as $faction) {
            $faction->save();
        }
    }

    public function getAllFactions() : array {
        return array_values($this->factions);
    }

    public function unsetFaction(string $name) : void {
        unset($this->factions[$name]);
        Main::getInstance()->getDB()->executeGeneric("delete_faction", ["name" => $name]);
    }

}