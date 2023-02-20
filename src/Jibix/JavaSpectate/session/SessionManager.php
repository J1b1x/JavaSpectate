<?php
namespace Jibix\JavaSpectate\session;
use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;


/**
 * Class SessionManager
 * @package Jibix\JavaSpectate\session
 * @author Jibix
 * @date 20.02.2023 - 00:14
 * @project JavaSpectate
 */
final class SessionManager{
    use SingletonTrait{
        setInstance as private;
        reset as private;
    }

    /** @var Session[] */
    private array $sessions = [];

    public function getSession(Player $player): Session{
        return $this->sessions[$player->getName()] ?? $this->sessions[$player->getName()] = new Session($player);
    }

    public function removeSession(Player|string $player): void{
        unset($this->sessions[is_string($player) ? $player : $player->getName()]);
    }

    public function getSessions(): array{
        return $this->sessions;
    }
}