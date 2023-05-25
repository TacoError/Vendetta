<?php namespace taco\vendetta\commands\arguments;

use CortexPE\Commando\args\StringEnumArgument;
use pocketmine\command\CommandSender;
use taco\vendetta\Manager;
use taco\vendetta\ranks\Rank;

class RankArgument extends StringEnumArgument {

    public function getTypeName() : string {
        return "rank";
    }

    public function getEnumValues() : array {
        return Manager::getRankManager()->getRankList() ?? ["Guest"];
    }

    public function canParse(string $testString, CommandSender $sender) : bool {
        return in_array($testString, $this->getEnumValues());
    }

    public function parse(string $argument, CommandSender $sender) : Rank {
        return Manager::getRankManager()->getRank($argument);
    }

}