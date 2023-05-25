<?php namespace taco\vendetta\factions\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use taco\vendetta\commands\constraints\MustBeInFactionConstraint;
use taco\vendetta\Manager;
use taco\vendetta\sessions\PlayerSession;

class ChatSubCommand extends BaseSubCommand {

    public function __construct() {
        parent::__construct("chat", "Change the current chat mode.");
    }

    public function prepare() : void {
        $this->addConstraint(new InGameRequiredConstraint($this));
        $this->addConstraint(new MustBeInFactionConstraint($this));
    }

    /** @var Player $sender */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        $session = Manager::getSessionManager()->getSession($sender);
        if ($session->getChatMode() == PlayerSession::CHAT_PUBLIC) {
            $session->setChatMode(PlayerSession::CHAT_FACTION);
            $sender->sendMessage($session->getMessage("faction-chat-faction"));
        } else {
            $session->setChatMode(PlayerSession::CHAT_PUBLIC);
            $sender->sendMessage($session->getMessage("faction-chat-public"));
        }
    }

}