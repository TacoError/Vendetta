<?php namespace taco\vendetta\factions\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use pocketmine\command\CommandSender;
use taco\vendetta\commands\constraints\MustBeFactionRankConstraint;
use taco\vendetta\commands\constraints\MustBeInFactionConstraint;
use taco\vendetta\factions\Faction;

class DisbandSubCommand extends BaseSubCommand {

    public function __construct() {
        parent::__construct("disband", "Disband your faction.");
    }

    public function prepare() : void {
        $this->addConstraint(new InGameRequiredConstraint($this));
        $this->addConstraint(new MustBeInFactionConstraint($this));
        $this->addConstraint(new MustBeFactionRankConstraint($this, Faction::RANK_OWNER));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {

    }

}