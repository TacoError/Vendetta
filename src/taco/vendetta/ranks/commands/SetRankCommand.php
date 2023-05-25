<?php namespace taco\vendetta\ranks\commands;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use taco\vendetta\commands\arguments\OnlinePlayerArgument;
use taco\vendetta\commands\arguments\RankArgument;
use taco\vendetta\Main;
use taco\vendetta\Manager;

class SetRankCommand extends BaseCommand {

    public function __construct() {
        parent::__construct(Main::getInstance(), "setrank", "Set a players rank.");
    }

    /*** @throws ArgumentOrderException */
    public function prepare() : void {
        $this->setPermission("ranks.management");
        $this->registerArgument(0, new OnlinePlayerArgument("player"));
        $this->registerArgument(1, new RankArgument("rank"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        if (!$this->testPermissionSilent($sender)) {
            return;
        }
        $session = Manager::getSessionManager()->getSession($args["player"]);
        $session->setRank($name = $args["rank"]->getName());
        $session->getPlayer()->sendMessage(sprintf($session->getMessage("rank-change"), $name));
        $sender->sendMessage(sprintf($sender instanceof Player ? Manager::getSessionManager()->getSession($sender)->getMessage("rank-change-admin") : "Rank changed to %s.", $name));
    }

}