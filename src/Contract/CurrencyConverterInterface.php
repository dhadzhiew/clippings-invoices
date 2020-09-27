<?php

namespace Clippings\Component\Calculator\Contract;

use Clippings\Component\Calculator\Util\Type\Decimal;

interface CurrencyConverterInterface
{
    public function setCurrencies(array $currencies): void;

    public function validateCurrencyCode(string $code): void;

    public function convert(Decimal $amount, string $fromCode, string $toCode): Decimal;

    public function getBaseCurrencyCode(): string;
}
