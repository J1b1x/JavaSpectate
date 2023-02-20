<?php
namespace Jibix\JavaSpectate\session;
use pocketmine\network\mcpe\protocol\types\AbilitiesData;
use pocketmine\network\mcpe\protocol\types\AbilitiesLayer;
use pocketmine\network\mcpe\protocol\types\command\CommandPermissions;
use pocketmine\network\mcpe\protocol\types\PlayerPermissions;
use pocketmine\network\mcpe\protocol\UpdateAbilitiesPacket;
use pocketmine\permission\DefaultPermissionNames;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;


/**
 * Class Session
 * @package Jibix\JavaSpectate\session
 * @author Jibix
 * @date 20.02.2023 - 00:13
 * @project JavaSpectate
 */
class Session{

    public const FLY_SPEED = 0.05;

    private float $flySpeed = self::FLY_SPEED;

    public function __construct(private Player $player){}

    public function getPlayer(): Player{
        return $this->player;
    }

    public function setFlySpeed(float $value): void{
        $this->flySpeed = $value;
        $player = $this->getPlayer();

        $isOp = $player->hasPermission(DefaultPermissions::ROOT_OPERATOR);
        $player->getNetworkSession()->sendDataPacket(UpdateAbilitiesPacket::create(new AbilitiesData(
            $isOp ? CommandPermissions::OPERATOR : CommandPermissions::NORMAL,
            $isOp ? PlayerPermissions::OPERATOR : PlayerPermissions::MEMBER,
            $player->getId(),
            [new AbilitiesLayer(AbilitiesLayer::LAYER_BASE, [
                AbilitiesLayer::ABILITY_ALLOW_FLIGHT => $player->getAllowFlight(),
                AbilitiesLayer::ABILITY_FLYING => $player->isFlying(),
                AbilitiesLayer::ABILITY_NO_CLIP => !$player->hasBlockCollision(),
                AbilitiesLayer::ABILITY_OPERATOR => $isOp,
                AbilitiesLayer::ABILITY_TELEPORT => $player->hasPermission(DefaultPermissionNames::COMMAND_TELEPORT_SELF),
                AbilitiesLayer::ABILITY_INVULNERABLE => $player->isCreative(),
                AbilitiesLayer::ABILITY_MUTED => false,
                AbilitiesLayer::ABILITY_WORLD_BUILDER => false,
                AbilitiesLayer::ABILITY_INFINITE_RESOURCES => !$player->hasFiniteResources(),
                AbilitiesLayer::ABILITY_LIGHTNING => false,
                AbilitiesLayer::ABILITY_BUILD => !$player->isSpectator(),
                AbilitiesLayer::ABILITY_MINE => !$player->isSpectator(),
                AbilitiesLayer::ABILITY_DOORS_AND_SWITCHES => !$player->isSpectator(),
                AbilitiesLayer::ABILITY_OPEN_CONTAINERS => !$player->isSpectator(),
                AbilitiesLayer::ABILITY_ATTACK_PLAYERS => !$player->isSpectator(),
                AbilitiesLayer::ABILITY_ATTACK_MOBS => !$player->isSpectator(),
            ], $this->getFlySpeed(), 0.1)]
        )));
    }

    public function getFlySpeed(): float{
        return $this->flySpeed;
    }
}