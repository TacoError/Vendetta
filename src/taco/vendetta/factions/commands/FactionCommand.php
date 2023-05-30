<?php namespace taco\vendetta\factions\commands;

use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use taco\vendetta\factions\commands\subcommands\ChatSubCommand;
use taco\vendetta\factions\commands\subcommands\CreateSubCommand;
use taco\vendetta\factions\commands\subcommands\DisbandSubCommand;
use taco\vendetta\factions\commands\subcommands\HelpSubCommand;
use taco\vendetta\factions\commands\subcommands\InviteSubCommand;
use taco\vendetta\factions\commands\subcommands\JoinSubCommand;
use taco\vendetta\factions\commands\subcommands\KickSubCommand;
use taco\vendetta\factions\commands\subcommands\LeaveSubCommand;
use taco\vendetta\Main;
use taco\vendetta\Manager;

class FactionCommand extends BaseCommand {

    public function __construct() {
        parent::__construct(Main::getInstance(), "f", "Base factions command.");
    }

    public function prepare() : void {
        $this->registerSubCommand(new CreateSubCommand());
        $this->registerSubCommand(new InviteSubCommand());
        $this->registerSubCommand(new DisbandSubCommand());
        $this->registerSubCommand(new JoinSubCommand());
        $this->registerSubCommand(new ChatSubCommand());
        $this->registerSubCommand(new KickSubCommand());
        $this->registerSubCommand(new LeaveSubCommand());
        $this->registerSubCommand(new HelpSubCommand());
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        if (!$sender instanceof Player) {
            return;
        }
        $sender->sendMessage(Manager::getSessionManager()->getSession($sender)->getMessage("use-f-help"));
    }

}