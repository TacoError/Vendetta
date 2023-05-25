<?php namespace taco\vendetta\ranks\commands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use taco\vendetta\commands\arguments\OnlinePlayerArgument;
use taco\vendetta\Main;
use taco\vendetta\Manager;

class RemovePermissionCommand extends BaseCommand {

    public function __construct() {
        parent::__construct(Main::getInstance(), "removepermission", "Remove a permission from a player.");
    }

    /*** @throws ArgumentOrderException */
    public function prepare() : void {
        $this->setPermission("ranks.management");
        $this->registerArgument(0, new OnlinePlayerArgument("player"));
        $this->registerArgument(1, new RawStringArgument("permission"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        Manager::getSessionManager()->getSession($args["player"])->removePermission($perm = $args["permission"]);
        if ($sender instanceof Player) {
            $sender->sendMessage(sprintf(Manager::getSessionManager()->getSession($sender)->getMessage("permission-removed"), $perm));
            return;
        }
        $sender->sendMessage("Permission " . $perm . " was taken from the player.");
    }

}