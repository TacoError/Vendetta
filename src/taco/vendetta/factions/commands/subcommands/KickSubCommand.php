<?php namespace taco\vendetta\factions\commands\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use CortexPE\Commando\exception\ArgumentOrderException;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use taco\vendetta\commands\constraints\MustBeFactionRankConstraint;
use taco\vendetta\commands\constraints\MustBeInFactionConstraint;
use taco\vendetta\factions\Faction;
use taco\vendetta\Manager;

class KickSubCommand extends BaseSubCommand {

    public function __construct() {
        parent::__construct("kick", "Kick a player from the faction.");
    }

    /*** @throws ArgumentOrderException */
    public function prepare() : void {
        $this->addConstraint(new InGameRequiredConstraint($this));
        $this->addConstraint(new MustBeInFactionConstraint($this));
        $this->addConstraint(new MustBeFactionRankConstraint($this, Faction::RANK_CAPTAIN));
        $this->registerArgument(0, new RawStringArgument("member"));
    }

    /** @var Player $sender */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        $session = Manager::getSessionManager()->getSession($sender);
        $faction = Manager::getFactionManager()->getFactionFromName($session->getFaction());
        if (!$faction->isPlayerInFaction($member = $args["member"])) {
            $sender->sendMessage($session->getMessage("not-in-faction"));
            return;
        }
        if ($faction->getRank($member) < 2) {
            $sender->sendMessage($session->getMessage("cannot-kick"));
            return;
        }
        $faction->removeMember($member);
        if (!is_null($onlineMember = Server::getInstance()->getPlayerExact($member))) {
            $onlineSession = Manager::getSessionManager()->getSession($onlineMember);
            $onlineSession->setFaction("");
            $onlineMember->sendMessage(sprintf($onlineSession->getMessage("kicked"), $faction->getName(), $sender->getName()));
        }
        $sender->sendMessage(sprintf($session->getMessage("kicked-good"), $member));
        $faction->messageEntireFactionTranslated("kicked-faction", $sender->getName(), $member);
    }

}