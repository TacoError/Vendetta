<?php namespace taco\vendetta\commands\arguments;

use CortexPE\Commando\args\BaseArgument;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\player\Player;
use pocketmine\Server;

class OnlinePlayerArgument extends BaseArgument {

    public function getNetworkType(): int {
        return AvailableCommandsPacket::ARG_TYPE_TARGET;
    }

    public function canParse(string $testString, CommandSender $sender): bool {
        return !is_null(Server::getInstance()->getPlayerExact($testString));
    }

    public function parse(string $argument, CommandSender $sender) : Player {
        return Server::getInstance()->getPlayerExact($argument);
    }

    public function getTypeName(): string {
        return "player";
    }

}