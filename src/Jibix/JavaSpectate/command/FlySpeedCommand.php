<?php
namespace Jibix\JavaSpectate\command;
use Jibix\JavaSpectate\config\Configuration;
use Jibix\JavaSpectate\Main;
use Jibix\JavaSpectate\session\Session;
use Jibix\JavaSpectate\util\Utils;
use pocketmine\command\CommandSender;
use pocketmine\command\defaults\VanillaCommand;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\player\GameMode;


/**
 * Class FlySpeedCommand
 * @package Jibix\JavaSpectate\command
 * @author Jibix
 * @date 20.02.2023 - 03:43
 * @project JavaSpectate
 */
class FlySpeedCommand extends VanillaCommand{

    public function __construct(){
        $data = Main::getInstance()->getConfig()->get('command',  []);
        parent::__construct($data['name'], $data['description'] ?? "", $data['usage'] ?? "", $data['aliases'] ?? []);

        Utils::registerPermission($self = $data['permission']['self'], "Allows the user to change their fly speed");
        Utils::registerPermission($other = $data['permission']['other'], "Allows the user to change the fly speed of other players");
        $this->setPermission(implode(";", [$self, $other]));
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
        if (!$this->testPermission($sender)) return true;
        if (count($args) < 1) throw new InvalidCommandSyntaxException();
        [$self, $other] = explode(";", $this->getPermission());
        $target = $this->fetchPermittedPlayerTarget($sender, $args[1] ?? null, $self, $other);
        if (is_null($target)) return true;
        if (!$target->getGamemode()->equals(GameMode::SPECTATOR())) {
            $sender->sendMessage(str_replace("{target}", $target->getDisplayName(), Main::getInstance()->getConfig()->getNested("command.error", "")));
            return true;
        }

        $session = Session::get($target);
        $session->setFlySpeed($sender, $speed = $this->getDouble($sender, is_numeric($args[0]) ? $args[0] : Session::FLY_SPEED, 0.001, Configuration::MAX_FLY_SPEED()));
        $sender->sendMessage(str_replace(["{target}", "{speed}"], [$target->getDisplayName(), $speed], Main::getInstance()->getConfig()->getNested("command.success", "")));
        return true;
    }
}