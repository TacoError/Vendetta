<?php namespace taco\vendetta\factions\commands\subcommands;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use pocketmine\command\CommandSender;

class HelpSubCommand extends BaseSubCommand {

    public function __construct() {
        parent::__construct("help", "View all the possible commands for factions.");
    }

    /*** @throws ArgumentOrderException */
    public function prepare() : void {
        $this->registerArgument(0, new IntegerArgument("page", true));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        $page = $args["page"] ?? 0;
        $commands = array_chunk($this->parent->getSubCommands(), 10);
        if ($page > count($commands) - 1) {
            $page = 0;
        }
        // This is not in messages.yml because I said so, cry about it.
        $sender->sendMessage(join("\n", [
            "§dFactions Help §7(§5Page " . $page + 1 . "§7/§5" . count($commands) . "§7)",
            ...array_map(
                fn(BaseSubCommand $command) => "§d" . $command->getName() . "§7: §5" . $command->getDescription(),
                $commands[$page < 1 ? 0 : $page - 1]
            ),
        ]));
    }

}