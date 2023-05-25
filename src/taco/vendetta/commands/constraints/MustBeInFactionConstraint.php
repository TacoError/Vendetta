<?php namespace taco\vendetta\commands\constraints;

use CortexPE\Commando\constraint\BaseConstraint;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use taco\vendetta\Manager;

class MustBeInFactionConstraint extends BaseConstraint {

    /** @var Player $sender */
    public function test(CommandSender $sender, string $aliasUsed, array $args): bool {
        return Manager::getSessionManager()->getSession($sender)->isInFaction();
    }

    /** @var Player $sender */
    public function onFailure(CommandSender $sender, string $aliasUsed, array $args): void {
        $sender->sendMessage(Manager::getSessionManager()->getSession($sender)->getMessage("must-be-in-faction"));
    }

    public function isVisibleTo(CommandSender $sender) : bool {
        // This has to be true because Commando won't show the command otherwise
        return true;
    }

}