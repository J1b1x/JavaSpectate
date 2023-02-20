<?php
namespace Jibix\JavaSpectate\util;
use Jibix\JavaSpectate\config\Configuration;
use pocketmine\math\Facing;
use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;


/**
 * Class Utils
 * @package Jibix\JavaSpectate\util
 * @author Jibix
 * @date 20.02.2023 - 02:25
 * @project JavaSpectate
 */
final class Utils{

    public static function checkDirection(int $oldSlot, int $newSlot): bool{
        return max($oldSlot, $newSlot) - min($oldSlot, $newSlot) == 1 &&
            (Configuration::DIRECTION() == Facing::DOWN && $oldSlot > $newSlot && !($newSlot == 0 && $oldSlot == 8)) ||
            (Configuration::DIRECTION() == Facing::UP && $oldSlot < $newSlot && !($oldSlot == 0 && $newSlot == 8));
    }

    public static function registerPermission(string $permission, ?string $description = null): void{
        $opRoot = PermissionManager::getInstance()->getPermission(DefaultPermissions::ROOT_OPERATOR);
        if (is_null(PermissionManager::getInstance()->getPermission($permission))) {
            PermissionManager::getInstance()->addPermission(new Permission($permission, $description));
            $opRoot->addChild($permission, true);
        }
    }
}