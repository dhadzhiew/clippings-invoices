<?php

namespace Clippings\Component\Calculator\Exception;

class InvoiceFactoryException extends \Exception
{
    /** @var int */
    public const MISSING_VALUE = 1;

    /** @var int */
    public const UNKNOWN_VALUE = 2;

    /** @var int */
    public const TOTAL_MUST_NOT_BE_NEGATIVE = 3;

    /** @var int */
    public const TOTAL_MUST_BE_NUMERIC = 4;

    /**
     * @param string $name
     * @return InvoiceFactoryException
     */
    public static function missingValue(string $name): InvoiceFactoryException
    {
        return new self('Missing value for: ' . $name, self::MISSING_VALUE);
    }

    /**
     * @param string $column
     * @param string $value
     * @param array $allowed
     * @return InvoiceFactoryException
     */
    public static function unknownValue(string $column, string $value, array $allowed): InvoiceFactoryException
    {
        return new self(
            sprintf(
                'Unknown value "%s" for column "%s". Allowed values "%s"',
                $value,
                $column,
                implode(',', $allowed)
            ),
            self::UNKNOWN_VALUE
        );
    }

    /**
     * @return InvoiceFactoryException
     */
    public static function totalMustNotBeNegative(): InvoiceFactoryException
    {
        return new self('Invoice total value must not be a negative number.');
    }

    /**
     * @return InvoiceFactoryException
     */
    public static function totalMustBeNumeric(): InvoiceFactoryException
    {
        return new self('Invoice total value must be numeric.');
    }
}
