<?php

namespace Clippings\Component\Calculator\Exception;

class CurrencyConverterException extends \Exception
{
    /** @var int */
    public const MISSING_BASE_CURRENCY = 1;

    /** @var int */
    public const CURRENCY_NOT_ALLOWED = 2;

    /** @var int */
    public const BASE_CURRENCY_ALREADY_SET = 3;

    /** @var int */
    public const MISSING_CURRENCY = 4;

    /**
     * @return CurrencyConverterException
     */
    public static function missingBaseCurrency(): CurrencyConverterException
    {
        return new self(
            'Cannot find a base currency. At least one currency exchange rate must be 1.',
            self::MISSING_BASE_CURRENCY
        );
    }

    /**
     * @param string $currencyCode
     * @return CurrencyConverterException
     */
    public static function currencyNotAllowed(string $currencyCode): CurrencyConverterException
    {
        return new self(sprintf('Currency code "%s" is not allowed.', $currencyCode), self::CURRENCY_NOT_ALLOWED);
    }

    /**
     * @return CurrencyConverterException
     */
    public static function baseCurrencyAlreadySet(): CurrencyConverterException
    {
        return new self('Base currency is already set', self::BASE_CURRENCY_ALREADY_SET);
    }

    /**
     * @param string $currencyCode
     * @return CurrencyConverterException
     */
    public static function missingCurrency(string $currencyCode): CurrencyConverterException
    {
        return new self('Cannot find currency: ' . $currencyCode, self::MISSING_CURRENCY);
    }
}
