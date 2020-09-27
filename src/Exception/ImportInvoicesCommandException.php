<?php

namespace Clippings\Component\Calculator\Exception;

class ImportInvoicesCommandException extends \Exception
{
    /** @var int */
    public const MISSING_PARAM = 1;

    /** @var int */
    public const CANNOT_PARSE_EXCHANGE_RATE = 2;

    /** @var int */
    public const FILE_NOT_EXISTS = 3;

    /** @var int */
    public const FILE_NOT_READABLE = 4;

    /**
     * @param string $name
     * @return ImportInvoicesCommandException
     */
    public static function missingParam(string $name): ImportInvoicesCommandException
    {
        return new self('Missing param ' . $name, self::MISSING_PARAM);
    }

    /**
     * @param string $string
     * @return ImportInvoicesCommandException
     */
    public static function cannotParseExchangeRate(string $string): ImportInvoicesCommandException
    {
        return new self(sprintf('Cannot parse exchange rate "%s"', $string), self::CANNOT_PARSE_EXCHANGE_RATE);
    }

    public static function fileNotExists(string $filePath): ImportInvoicesCommandException
    {
        return new self(sprintf('File "%s" does not exist.', $filePath), self::FILE_NOT_EXISTS);
    }

    public static function fileNotReadable(string $filePath): ImportInvoicesCommandException
    {
        return new self(sprintf('File "%s" is not readable.', $filePath), self::FILE_NOT_READABLE);
    }
}
