<?php
namespace Jibix\JavaSpectate\config;
use Jibix\JavaSpectate\Main;
use Jibix\JavaSpectate\util\CustomRegistryTrait;
use pocketmine\math\Facing;


/**
 * Class Configuration
 * @package Jibix\JavaSpectate\config
 * @author Jibix
 * @date 20.02.2023 - 01:40
 * @project JavaSpectate
 *
 * @method static float MAX_FLY_SPEED()
 * @method static float SPEED_MULTIPLIER()
 * @method static bool BELOW_DEFAULT()
 * @method static int DIRECTION()
 */
class Configuration{
    use CustomRegistryTrait;

    private static function register(string $name, mixed $member): void{
        self::_registryRegister($name, $member);
    }

    protected static function setup(): void{
        $data = Main::getInstance()->getConfig()->getAll();
        self::register("max_fly_speed", max(floatval($data['max-fly-speed'] ?? 0.7), 1));
        self::register("speed_multiplier", max(floatval($data['speed-multiplier'] ?? 0.05), 0.001));
        self::register("below_default", boolval($data['below-default'] ?? false));
        self::register("direction", in_array(strtolower(strval($data['direction'] ?? "left")), ["right", "up", "east"]) ? Facing::UP : Facing::DOWN);
    }
}