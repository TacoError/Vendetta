<?php namespace taco\vendetta\sessions;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use taco\vendetta\Manager;

class SessionListener implements Listener {

    public function onLogin(PlayerLoginEvent $event) : void {
        Manager::getSessionManager()->addSession($event->getPlayer());
    }

    public function onJoin(PlayerJoinEvent $event) : void {
        Manager::getSessionManager()->getSession($event->getPlayer())->onJoin();
    }

    public function onQuit(PlayerQuitEvent $event) : void {
        Manager::getSessionManager()->closeSession($event->getPlayer());
    }

}