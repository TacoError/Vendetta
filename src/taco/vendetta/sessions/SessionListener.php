<?php namespace taco\vendetta\sessions;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use taco\vendetta\factions\Faction;
use taco\vendetta\Manager;
use taco\vendetta\utils\BroadcastUtils;

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

    public function onChat(PlayerChatEvent $event) : void {
        $event->cancel();
        $player = $event->getPlayer();
        $session = Manager::getSessionManager()->getSession($player);
        $faction = Manager::getFactionManager()->getFactionFromName($session->getFaction());
        if ($session->getChatMode() == PlayerSession::CHAT_FACTION) {
            $faction->messageEntireFactionTranslated("faction-chat", $player->getName(), $event->getMessage());
            return;
        }
        $factionPrefix = "";
        if (!is_null($faction)) {
            $factionPrefix = "§7[" . Faction::RANK_TO_PREFIX[$faction->getRank($player)] . "§r" . $faction->getName() . "§r§7] ";
        }
        BroadcastUtils::broadcastMessage("chat", $factionPrefix, $player->getName(), $event->getMessage());
    }

}