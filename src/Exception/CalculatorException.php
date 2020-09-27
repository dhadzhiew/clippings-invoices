<?php

namespace Clippings\Component\Calculator\Exception;

class CalculatorException extends \Exception
{
    /** @var int */
    public const MISSING_INVOICE_PARENT_DOCUMENT = 1;

    /** @var int */
    public const NOT_SUPPORTED_INVOICE_TYPE = 2;

    /** @var int */
    public const OUTPUT_CURRENCY_NOT_SET = 3;

    /**
     * @param string $parentId
     * @return CalculatorException
     */
    public static function missingParentDocument(string $parentId): CalculatorException
    {
        return new self('Missing invoice parent document: ' . $parentId, self::MISSING_INVOICE_PARENT_DOCUMENT);
    }

    /**
     * @param string $type
     * @return CalculatorException
     */
    public static function notSupportedInvoiceType(string $type): CalculatorException
    {
        return new self('Not supported invoice type: ' . $type, self::NOT_SUPPORTED_INVOICE_TYPE);
    }

    /**
     * @return CalculatorException
     */
    public static function outputCurrencyNotSet(): CalculatorException
    {
        return new self('The property outputCurrency is not set.', self::OUTPUT_CURRENCY_NOT_SET);
    }
}
