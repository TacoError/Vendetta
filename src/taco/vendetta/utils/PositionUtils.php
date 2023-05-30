<?php namespace taco\vendetta\utils;

use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\world\Position;

class PositionUtils {

    public static function vectorToString(Vector3 $vec) : string {
        return $vec->getFloorX() . ":" . $vec->getFloorY() . ":" . $vec->getFloorZ();
    }

    public static function vectorFromString(string $vec) : Vector3 {
        $vec = explode(":", $vec);
        return new Vector3((int)$vec[0], (int)$vec[1], (int)$vec[2]);
    }

    public static function makeSmallerVector(Vector3 $pos1, Vector3 $pos2) : Vector3 {
        return new Vector3(min($pos1->getX(), $pos2->getX()), min($pos1->getY(), $pos2->getY()), min($pos1->getZ(), $pos2->getZ()));
    }

    public static function makeLargerVector(Vector3 $pos1, Vector3 $pos2) : Vector3 {
        return new Vector3(max($pos1->getX(), $pos2->getX()), max($pos1->getY(), $pos2->getY()), max($pos1->getZ(), $pos2->getZ()));
    }

    public static function makeAABB(Vector3 $pos1, Vector3 $pos2) : AxisAlignedBB {
        $min = self::makeSmallerVector($pos1, $pos2);
        $max = self::makeLargerVector($pos1, $pos2);
        return new AxisAlignedBB($min->getX(), $min->getY(), $min->getZ(), $max->getX(), $max->getY(), $max->getZ());
    }

    public static function positionToString(Position $pos) : string {
        return $pos->getFloorX() . ":" . $pos->getFloorY() . ":" . $pos->getFloorZ() . ":" . $pos->getWorld()->getFolderName();
    }

    public static function positionFromString(string $pos) : Position {
        $pos = explode(":", $pos);
        return new Position((int)$pos[0], (int)$pos[1], (int)$pos[2], Server::getInstance()->getWorldManager()->getWorldByName($pos[3]));
    }

}