<?php

namespace Clippings\Component\Calculator\Exception;

class ImportCommandException extends \Exception
{
    /** @var int */
    public const MISSING_PARAM = 1;

    /** @var int */
    public const CANNOT_PARSE_EXCHANGE_RATE = 2;

    /**
     * @param string $name
     * @return ImportCommandException
     */
    public static function missingParam(string $name): ImportCommandException
    {
        return new self('Missing param ' . $name, self::MISSING_PARAM);
    }

    /**
     * @param string $string
     * @return ImportCommandException
     */
    public static function cannotParseExchangeRate(string $string): ImportCommandException
    {
        return new self(sprintf('Cannot parse exchange rate "%s"', $string), self::CANNOT_PARSE_EXCHANGE_RATE);
    }
}
