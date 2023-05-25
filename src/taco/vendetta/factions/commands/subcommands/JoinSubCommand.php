<?php namespace taco\vendetta\factions\commands\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use CortexPE\Commando\exception\ArgumentOrderException;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use taco\vendetta\commands\constraints\CannotBeInFactionConstraint;
use taco\vendetta\Main;
use taco\vendetta\Manager;

class JoinSubCommand extends BaseSubCommand {

    public function __construct() {
        parent::__construct("join", "Join a faction.");
    }

    /*** @throws ArgumentOrderException */
    public function prepare() : void {
        $this->addConstraint(new InGameRequiredConstraint($this));
        $this->addConstraint(new CannotBeInFactionConstraint($this));
        $this->registerArgument(0, new RawStringArgument("faction"));
    }

    /** @var Player $sender */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        $faction = $args["faction"];
        $session = Manager::getSessionManager()->getSession($sender);
        if (is_null($faction = Manager::getFactionManager()->getFactionFromName($faction))) {
            $sender->sendMessage($session->getMessage("no-faction"));
            return;
        }
        if (!$faction->isInvited($sender->getName())) {
            $sender->sendMessage($session->getMessage("no-invite"));
            return;
        }
        if (count($faction->getFullMemberList()) > Main::getInstance()->getConfig()->getNested("factions.max-members")) {
            $sender->sendMessage($session->getMessage("faction-full"));
            return;
        }
        $session->setFaction($faction->getName());
        $faction->removeInvite($sender->getName());
        $faction->addMember($sender->getName());
    }

}