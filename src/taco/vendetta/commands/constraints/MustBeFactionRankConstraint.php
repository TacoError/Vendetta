<?php namespace taco\vendetta\commands\constraints;

use CortexPE\Commando\constraint\BaseConstraint;
use CortexPE\Commando\IRunnable;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use taco\vendetta\Manager;

class MustBeFactionRankConstraint extends BaseConstraint {

    private int $rankNeeded;

    public function __construct(IRunnable $context, int $rank) {
        parent::__construct($context);
        $this->rankNeeded = $rank;
    }

    public function test(CommandSender $sender, string $aliasUsed, array $args): bool {
        return $this->isVisibleTo($sender);
    }

    /** @var Player $sender */
    public function onFailure(CommandSender $sender, string $aliasUsed, array $args): void {
        $sender->sendMessage(Manager::getSessionManager()->getSession($sender)->getMessage("must-be-higher-rank"));
    }

    /** @var Player $sender */
    public function isVisibleTo(CommandSender $sender) : bool {
        $faction = Manager::getFactionManager()->getFactionFromName(Manager::getSessionManager()->getSession($sender)->getFaction());
        return !is_null($faction) && $faction->getRank($sender) <= $this->rankNeeded;
    }

}