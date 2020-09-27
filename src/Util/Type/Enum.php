<?php

namespace Clippings\Component\Calculator\Util\Type;

abstract class Enum
{
    private static $constantsCache = null;

    final private function __construct()
    {
    }

    /**
     * @return array|null
     * @throws \ReflectionException
     */
    final public static function toArray(): array
    {
        if (self::$constantsCache === null) {
            self::$constantsCache = (new \ReflectionClass(static::class))->getConstants();
        }

        return self::$constantsCache;
    }

    /**
     * @param $value
     * @return bool
     * @throws \ReflectionException
     */
    final public static function isValid($value): bool
    {
        return in_array($value, static::toArray(), true);
    }

}
