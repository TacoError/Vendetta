<?php namespace taco\vendetta\factions\tasks;

use pocketmine\scheduler\Task;
use taco\vendetta\factions\Faction;
use taco\vendetta\Manager;

class InviteTask extends Task {

    public function onRun() : void {
        /** @var Faction $faction */
        foreach(Manager::getFactionManager()->getAllFactions() as $faction) {
            $faction->tickInvites();
        }
    }

}