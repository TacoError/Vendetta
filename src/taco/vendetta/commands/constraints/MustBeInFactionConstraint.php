<?php namespace taco\vendetta\commands\constraints;

use CortexPE\Commando\constraint\BaseConstraint;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use taco\vendetta\Manager;

class MustBeInFactionConstraint extends BaseConstraint {

    public function test(CommandSender $sender, string $aliasUsed, array $args): bool {
        return $this->isVisibleTo($sender);
    }

    /** @var Player $sender */
    public function onFailure(CommandSender $sender, string $aliasUsed, array $args): void {
        $sender->sendMessage(Manager::getSessionManager()->getSession($sender)->getMessage("must-be-in-faction"));
    }

    /** @var Player $sender */
    public function isVisibleTo(CommandSender $sender) : bool {
        return Manager::getSessionManager()->getSession($sender)->isInFaction();
    }

}