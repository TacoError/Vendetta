<?php namespace taco\vendetta;

use taco\vendetta\factions\FactionManager;
use taco\vendetta\ranks\RankManager;
use taco\vendetta\sessions\SessionManager;

class Manager {

    private static SessionManager $sessionManager;

    private static FactionManager $factionManager;

    private static RankManager $rankManager;

    public function __construct() {
        self::$sessionManager = new SessionManager();
        self::$factionManager = new FactionManager();
        self::$rankManager = new RankManager();
    }

    public static function getSessionManager() : SessionManager {
        return self::$sessionManager;
    }

    public static function getFactionManager() : FactionManager {
        return self::$factionManager;
    }

    public static function getRankManager() : RankManager {
        return self::$rankManager;
    }

}