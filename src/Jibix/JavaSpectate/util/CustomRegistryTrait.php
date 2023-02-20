<?php
namespace Jibix\JavaSpectate\util;


/**
 * Class CustomRegistryTrait
 * @package Jibix\JavaSpectate\util
 * @author Jibix
 * @date 20.02.2023 - 01:43
 * @project JavaSpectate
 */
trait CustomRegistryTrait{

    /**
     * @var mixed[]
     * @phpstan-var array<string, mixed>
     */
    private static $members = null;

    private static function verifyName(string $name) : void{
        if(preg_match('/^(?!\d)[A-Za-z\d_]+$/u', $name) === 0){
            throw new \InvalidArgumentException("Invalid member name \"$name\", should only contain letters, numbers and underscores, and must not start with a number");
        }
    }

    /**
     * Adds the given mixed to the registry.
     *
     * @throws \InvalidArgumentException
     */
    private static function _registryRegister(string $name, mixed $member): void{
        self::verifyName($name);
        $upperName = mb_strtoupper($name);
        if(isset(self::$members[$upperName])){
            throw new \InvalidArgumentException("\"$upperName\" is already reserved");
        }
        self::$members[$upperName] = $member;
    }

    /**
     * Inserts default entries into the registry.
     *
     * (This ought to be private, but traits suck too much for that.)
     */
    abstract protected static function setup() : void;

    /**
     * @internal Lazy-inits the enum if necessary.
     *
     * @throws \InvalidArgumentException
     */
    protected static function checkInit() : void{
        if(self::$members === null){
            self::$members = [];
            self::setup();
        }
    }

    /**
     * @throws \InvalidArgumentException
     */
    private static function _registryFromString(string $name) : mixed{
        self::checkInit();
        $upperName = mb_strtoupper($name);
        if(!isset(self::$members[$upperName])){
            throw new \InvalidArgumentException("No such registry member: " . self::class . "::" . $upperName);
        }
        return self::preprocessMember(self::$members[$upperName]);
    }

    /**
     * @param string  $name
     * @param mixed[] $arguments
     * @phpstan-param list<mixed> $arguments
     *
     * @return mixed
     */
    public static function __callStatic($name, $arguments){
        if(count($arguments) > 0){
            throw new \ArgumentCountError("Expected exactly 0 arguments, " . count($arguments) . " passed");
        }
        try{
            return self::_registryFromString($name);
        }catch(\InvalidArgumentException $e){
            throw new \Error($e->getMessage(), 0, $e);
        }
    }

    /**
     * @return mixed[]
     * @phpstan-return array<string, mixed>
     */
    private static function _registryGetAll() : array{
        self::checkInit();
        return array_map(function(mixed $o) : mixed{
            return self::preprocessMember($o);
        }, self::$members);
    }

    protected static function preprocessMember(mixed $member) : mixed{
        return is_object($member) ? clone $member : $member;
    }
}