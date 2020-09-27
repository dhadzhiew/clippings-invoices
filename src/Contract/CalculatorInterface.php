<?php

namespace Clippings\Component\Calculator\Contract;

use Clippings\Component\Calculator\Currency;
use Clippings\Component\Calculator\Total;

interface CalculatorInterface
{
    /**
     * @param array $data
     */
    public function setInvoices(array $data): void;

    /**
     * @param string $vat
     * @return Total[]
     */
    public function getTotals($vat = ''): array;

    /**
     * @return CurrencyConverterInterface
     */
    public function getCurrencyConverter(): CurrencyConverterInterface;
}
