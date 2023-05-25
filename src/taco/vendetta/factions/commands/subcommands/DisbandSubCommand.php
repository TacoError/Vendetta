<?php namespace taco\vendetta\factions\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use taco\vendetta\commands\constraints\MustBeFactionRankConstraint;
use taco\vendetta\commands\constraints\MustBeInFactionConstraint;
use taco\vendetta\factions\Faction;
use taco\vendetta\Manager;
use taco\vendetta\utils\BroadcastUtils;

class DisbandSubCommand extends BaseSubCommand {

    public function __construct() {
        parent::__construct("disband", "Disband your faction.");
    }

    public function prepare() : void {
        $this->addConstraint(new InGameRequiredConstraint($this));
        $this->addConstraint(new MustBeInFactionConstraint($this));
        $this->addConstraint(new MustBeFactionRankConstraint($this, Faction::RANK_OWNER));
    }

    /** @var Player $sender */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        $session = Manager::getSessionManager()->getSession($sender);
        $faction = Manager::getFactionManager()->getFactionFromName($session->getFaction());
        foreach($faction->getOnlineMembers() as $member) {
            Manager::getSessionManager()->getSession($member)->setFaction("");
        }
        Manager::getFactionManager()->unsetFaction($faction->getName());
        $sender->sendMessage($session->getMessage("faction-disbanded"));
        BroadcastUtils::broadcastMessage("faction-disband-broadcast", $faction->getName());
    }

}