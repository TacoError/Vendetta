<?php namespace taco\vendetta\factions\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use taco\vendetta\commands\constraints\MustBeInFactionConstraint;
use taco\vendetta\factions\Faction;
use taco\vendetta\Manager;

class LeaveSubCommand extends BaseSubCommand {

    public function __construct() {
        parent::__construct("leave", "Leave your faction.");
    }

    public function prepare() : void {
        $this->addConstraint(new InGameRequiredConstraint($this));
        $this->addConstraint(new MustBeInFactionConstraint($this));
    }

    /** @var Player $sender */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        $session = Manager::getSessionManager()->getSession($sender);
        $faction = Manager::getFactionManager()->getFactionFromName($session->getFaction());
        if ($faction->getRank($sender) == Faction::RANK_OWNER) {
            $sender->sendMessage($session->getMessage("must-not-be-owner-to-leave"));
            return;
        }
        $sender->sendMessage($session->getMessage("left-faction"));
        $session->setFaction("");
        $faction->removeMember($sender);
        $faction->messageEntireFactionTranslated("left-faction-broadcast", $sender->getName());
    }


}