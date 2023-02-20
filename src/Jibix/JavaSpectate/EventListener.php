<?php
namespace Jibix\JavaSpectate;
use Jibix\JavaSpectate\config\Configuration;
use Jibix\JavaSpectate\session\Session;
use Jibix\JavaSpectate\session\SessionManager;
use Jibix\JavaSpectate\util\Utils;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerGameModeChangeEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\math\Facing;
use pocketmine\network\mcpe\protocol\UpdateAbilitiesPacket;
use pocketmine\player\GameMode;


/**
 * Class EventListener
 * @package Jibix\JavaSpectate
 * @author Jibix
 * @date 20.02.2023 - 00:12
 * @project JavaSpectate
 */
class EventListener implements Listener{

    public function onQuit(PlayerQuitEvent $event): void{
        SessionManager::getInstance()->removeSession($event->getPlayer());
    }

    public function onGameModeChange(PlayerGameModeChangeEvent $event): void{
        $player = $event->getPlayer();
        if ($player->getGamemode()->equals(GameMode::SPECTATOR()) && !$event->getNewGamemode()->equals(GameMode::SPECTATOR())) {
            SessionManager::getInstance()->getSession($player)->setFlySpeed(Session::FLY_SPEED);
        }
    }

    public function onItemHeld(PlayerItemHeldEvent $event): void{
        $player = $event->getPlayer();
        if ($player->getGamemode()->equals(GameMode::SPECTATOR())) {
            $session = SessionManager::getInstance()->getSession($player);
            $flySpeed = $session->getFlySpeed() + (Utils::checkDirection($player->getInventory()->getHeldItemIndex(), $event->getSlot()) ? 1 : -1) * Configuration::SPEED_MULTIPLIER();
            if ($flySpeed > Configuration::MAX_FLY_SPEED() || ($flySpeed <= Session::FLY_SPEED && (!Configuration::BELOW_DEFAULT() || $flySpeed <= 0))) return;
            $session->setFlySpeed($flySpeed);
        }
    }

    public function onPacketSend(DataPacketSendEvent $event): void{
        foreach ($event->getPackets() as $packet) {
            foreach ($event->getTargets() as $target) {
                if (is_null($player = $target->getPlayer())) continue;
                if ($packet instanceof UpdateAbilitiesPacket && $player->getGamemode()->equals(GameMode::SPECTATOR())) {
                    foreach ($packet->getData()->getAbilityLayers() as $layer) {
                        //NOTE: Thanks for making this shit private dylan :)
                        $property = (new \ReflectionClass($layer))->getProperty("flySpeed");
                        $property->setAccessible(true);
                        $property->setValue($layer, SessionManager::getInstance()->getSession($player)->getFlySpeed());
                    }
                }
            }
        }
    }
}