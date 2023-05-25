<?php namespace taco\vendetta\utils;

use taco\vendetta\Manager;
use taco\vendetta\sessions\PlayerSession;

class BroadcastUtils {

    public static function broadcastMessage(string $message, ...$values) : void {
        /** @var PlayerSession $session */
        foreach(Manager::getSessionManager()->getSessions() as $session) {
            $session->getPlayer()->sendMessage(sprintf($session->getMessage($message), ...$values));
        }
    }

}