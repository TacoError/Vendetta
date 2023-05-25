<?php namespace taco\vendetta\factions\commands\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use CortexPE\Commando\exception\ArgumentOrderException;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use taco\vendetta\commands\constraints\CannotBeInFactionConstraint;
use taco\vendetta\Manager;
use taco\vendetta\utils\BroadcastUtils;

class CreateSubCommand extends BaseSubCommand {

    public function __construct() {
        parent::__construct("create", "Create a faction.");
    }

    /*** @throws ArgumentOrderException */
    public function prepare() : void {
        $this->addConstraint(new InGameRequiredConstraint($this));
        $this->addConstraint(new CannotBeInFactionConstraint($this));
        $this->registerArgument(0, new RawStringArgument("name"));
    }

    /** @var Player $sender */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        $session = Manager::getSessionManager()->getSession($sender);
        $name = $args["name"] ?? null;
        if (is_null($name)) {
            $sender->sendMessage($session->getMessage("must-provide-faction-name"));
            return;
        }
        $factions = Manager::getFactionManager();
        if ($factions->factionExists($name)) {
            $sender->sendMessage($session->getMessage("already-faction-with-name"));
            return;
        }
        if (strlen($name) < 3) {
            $sender->sendMessage($session->getMessage("must-be-three"));
            return;
        }
        if (strlen($name) > 12) {
            $sender->sendMessage($session->getMessage("no-more-than-twelve"));
            return;
        }
        if (!ctype_alpha($name)) {
            $sender->sendMessage($session->getMessage("invalid-name"));
            return;
        }
        $factions->createFaction($sender->getName(), $name);
        $session->setFaction($name);
        $sender->sendMessage($session->getMessage("create-faction-success"));
        BroadcastUtils::broadcastMessage("faction-created-broadcast", $name, $sender->getName());
    }

}