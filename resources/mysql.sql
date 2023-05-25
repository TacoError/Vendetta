-- #!mysql
-- #{ init
CREATE TABLE IF NOT EXISTS faction_players (
    player VARCHAR(20) PRIMARY KEY,
    playerName TEXT,
    kills INT,
    deaths INT,
    killStreak INT,
    faction TEXT,
    language TEXT,
    rank TEXT,
    permissions TEXT
);
-- #}
-- #{ init_factions
CREATE TABLE IF NOT EXISTS factions (
    name VARCHAR(20) PRIMARY KEY,
    description TEXT,
    creationTimeStamp INT,
    owner TEXT,
    officers TEXT,
    members TEXT,
    recruits TEXT,
    balance INT,
    value INT,
    power INT,
    allies TEXT,
    invites TEXT,
    allyRequests TEXT
);
-- #}
-- #{ get_data
-- #    :xuid string
SELECT * FROM faction_players WHERE player = :xuid;
-- #}
-- #{ set_data
-- #    :xuid string
-- #    :playerName string
-- #    :kills int
-- #    :deaths int
-- #    :killStreak int
-- #    :faction string
-- #    :language string
-- #    :rank string
-- #    :permissions string
INSERT INTO faction_players(player, playerName, kills, deaths, killStreak, faction, language, rank, permissions)
VALUES (:xuid, :playerName, :kills, :deaths, :killStreak, :faction, :language, :rank, :permissions)
ON DUPLICATE KEY UPDATE player = :xuid, playerName = :playerName, kills = :kills, deaths = :deaths,
    killStreak = :killStreak, faction = :faction, language = :language, rank = :rank, permissions = :permissions;
-- #}
-- #{ get_all_factions
SELECT * from factions;
-- #}
-- #{ set_data_faction
-- #    :name string
-- #    :description string
-- #    :creationTimeStamp int
-- #    :owner string
-- #    :officers string
-- #    :members string
-- #    :recruits string
-- #    :balance int
-- #    :value int
-- #    :power int
-- #    :allies string
-- #    :invites string
-- #    :allyRequests string
INSERT INTO factions(name, description, creationTimeStamp, owner, officers, members, recruits, balance, value, power, allies, invites, allyRequests)
VALUES (:name, :description, :creationTimeStamp, :owner, :officers, :members, :recruits, :balance, :value, :power, :allies, :invites, :allyRequests)
ON DUPLICATE KEY UPDATE name = :name, description = :description, creationTimeStamp = :creationTimeStamp, owner = :owner,
    officers = :officers, members = :members, recruits = :recruits, balance = :balance, value = :value, power = :power,
    allies = :allies, invites = :invites, allyRequests = :allyRequests;
-- #}
-- #{ drop_players
DROP TABLE faction_players;
-- #}
-- #{ drop_factions
DROP TABLE factions;
-- #}
-- #{ delete_faction
-- #    :name string
DELETE FROM factions WHERE name = :name;
-- #}

