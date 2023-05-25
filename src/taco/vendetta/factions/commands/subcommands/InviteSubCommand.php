<?php namespace taco\vendetta\factions\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use CortexPE\Commando\exception\ArgumentOrderException;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use taco\vendetta\commands\arguments\OnlinePlayerArgument;
use taco\vendetta\commands\constraints\MustBeFactionRankConstraint;
use taco\vendetta\commands\constraints\MustBeInFactionConstraint;
use taco\vendetta\factions\Faction;
use taco\vendetta\Manager;

class InviteSubCommand extends BaseSubCommand {

    public function __construct() {
        parent::__construct("invite", "Invite a player to your faction.");
    }

    /*** @throws ArgumentOrderException */
    public function prepare() : void {
        $this->addConstraint(new InGameRequiredConstraint($this));
        $this->addConstraint(new MustBeInFactionConstraint($this));
        $this->addConstraint(new MustBeFactionRankConstraint($this, Faction::RANK_CAPTAIN));
        $this->registerArgument(0, new OnlinePlayerArgument("player"));
    }

    /** @var Player $sender */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        $session = Manager::getSessionManager()->getSession($sender);
        $invited = Manager::getSessionManager()->getSession($args["player"]);
        $name = $invited->getPlayer()->getName();
        if ($name == $sender->getName()) {
            $sender->sendMessage($session->getMessage("cannot-invite-self"));
            return;
        }
        if ($invited->isInFaction()) {
            $sender->sendMessage($session->getMessage("cannot-invite-players-in-faction"));
            return;
        }
        $faction = Manager::getFactionManager()->getFactionFromName($session->getFaction());
        if ($faction->isInvited($name)) {
            $sender->sendMessage($session->getMessage("already-invited"));
            return;
        }
        $faction->invite($invited->getPlayer()->getName());
        $sender->sendMessage(sprintf($session->getMessage("successful-invite"), $name));
        $invited->getPlayer()->sendMessage(sprintf($invited->getMessage("invited"), $faction->getName(), $faction->getName()));
    }

}