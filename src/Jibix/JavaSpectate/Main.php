<?php
namespace Jibix\JavaSpectate;
use Jibix\JavaSpectate\command\FlySpeedCommand;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;


/**
 * Class Main
 * @package Jibix\JavaSpectate
 * @author Jibix
 * @date 20.02.2023 - 00:11
 * @project JavaSpectate
 */
final class Main extends PluginBase{
    use SingletonTrait{
        setInstance as private;
        reset as private;
    }

    protected function onLoad(): void{
        self::setInstance($this);
        $this->saveDefaultConfig();
    }

    protected function onEnable(): void{
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        $this->getServer()->getCommandMap()->register($this->getName(), new FlySpeedCommand());
    }
}