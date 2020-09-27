<?php

namespace Clippings\Component\Calculator\Facade;

use Clippings\Component\Calculator\Calculator;
use Clippings\Component\Calculator\Currency;
use Clippings\Component\Calculator\CurrencyConverter;
use Clippings\Component\Calculator\InvoiceFactory;
use Clippings\Component\Calculator\Total;
use Clippings\Component\Calculator\Util\Parser\CSVParser;

class CalculatorFacade
{
    /** @var Calculator */
    private $calculator;

    /**
     * @param string[] $allowedCurrencyCodes
     */
    public function __construct(array $allowedCurrencyCodes)
    {
        $this->calculator = new Calculator(new CurrencyConverter($allowedCurrencyCodes));
    }

    /**
     * @param string $vat
     * @return Total[]
     */
    public function getTotals($vat = ''): array
    {
        return $this->calculator->getTotals($vat);
    }

    /**
     * @param string $fileData
     */
    public function setData(string $fileData): void
    {
        $parser = new CSVParser(true);
        $data = $parser->parseFromDataSource(new StringDataSource($fileData));

        $invoiceFactory = new InvoiceFactory();
        $invoices = $invoiceFactory->createManyFromCSVGenerator($data);

        $this->calculator->setInvoices($invoices);
    }

    /**
     * @param Currency[] $currencies
     */
    public function setCurrencies(array $currencies): void
    {
        $this->calculator->setCurrencies($currencies);
    }

    /**
     * @param string $code
     */
    public function setOutputCurrencyCode(string $code): void
    {
        $this->calculator->setOutputCurrencyCode($code);
    }
}
