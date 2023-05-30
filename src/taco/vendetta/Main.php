<?php namespace taco\vendetta;

use CortexPE\Commando\exception\HookAlreadyRegistered;
use CortexPE\Commando\PacketHooker;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;

class Main extends PluginBase {

    use SingletonTrait;

    private DataConnector $db;

    private array $messages;

    public function onLoad() : void {
        $this->getLogger()->notice("Loading");
        self::$instance = $this;
    }

    /*** @throws HookAlreadyRegistered */
    public function onEnable() : void {
        if (!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }

        $this->saveDefaultConfig();
        $this->saveResource("messages.yml");
        $this->messages = (new Config($this->getDataFolder() . "messages.yml", Config::YAML))->getAll();

        $this->getLogger()->notice("Connecting to database");
        $this->db = libasynql::create(
            $this,
            [
                "type" => "mysql",
                "worker-limit" => 2,
                "mysql" => [
                    "host" => "51.81.35.193",
                    "username" => "u180_Be8RLiXumH",
                    "password" => "m+i3g08N2^=CmU^1ImnMs220",
                    "schema" => "s180_cosmic"
                ]
            ],
            ["mysql" => "mysql.sql"],
        );
        $this->db->executeGeneric("players.drop");
        $this->db->executeGeneric("factions.drip");
        $this->db->waitAll();
        $this->db->executeGeneric("players.init");
        $this->db->executeGeneric("factions.init");
        $this->db->waitAll();

        new Manager();

        $this->db->waitAll();
        $this->getLogger()->notice("Ready");
    }

    public function onDisable() : void {
        Manager::getFactionManager()->saveAllFactions();
        $this->db->close();
    }

    public function getDB() : DataConnector {
        return $this->db;
    }

    public function getMessages() : array {
        return $this->messages;
    }

}