<?php namespace taco\vendetta\sessions;

use pocketmine\player\Player;
use pocketmine\Server;
use taco\vendetta\Main;

class SessionManager {

    private array $sessions = [];

    public function __construct() {
        Server::getInstance()->getPluginManager()->registerEvents(new SessionListener(), Main::getInstance());
    }

    public function getSession(Player $player) : PlayerSession {
        return $this->sessions[$player->getId()];
    }

    public function addSession(Player $player) : PlayerSession {
        $session = new PlayerSession($player);
        $this->sessions[$player->getId()] = $session;
        return $session;
    }

    public function closeSession(Player $player) : void {
        $this->sessions[$player->getId()]->close();
        unset($this->sessions[$player->getId()]);
    }

    public function getSessions() : array {
        return array_values($this->sessions);
    }

}