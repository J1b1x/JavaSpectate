<?php
namespace Jibix\JavaSpectate\util;
use Jibix\JavaSpectate\config\Configuration;
use pocketmine\math\Facing;


/**
 * Class Utils
 * @package Jibix\JavaSpectate\util
 * @author Jibix
 * @date 20.02.2023 - 02:25
 * @project JavaSpectate
 */
final class Utils{

    public static function checkDirection(int $oldSlot, int $newSlot): bool{
        return $oldSlot !== $newSlot &&
            (Configuration::DIRECTION() == Facing::DOWN && $oldSlot > $newSlot && !($newSlot == 0 && $oldSlot == 8)) ||
            (Configuration::DIRECTION() == Facing::UP && $oldSlot < $newSlot && !($oldSlot == 0 && $newSlot == 8));
    }
}