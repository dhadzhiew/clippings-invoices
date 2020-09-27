<?php

namespace Clippings\Component\Calculator;

use Clippings\Component\Calculator\Contract\CalculatorInterface;
use Clippings\Component\Calculator\Contract\CurrencyConverterInterface;
use Clippings\Component\Calculator\Exception\CalculatorException;
use Clippings\Component\Calculator\Util\Type\Decimal;

class Calculator implements CalculatorInterface
{
    /** @var Invoice[] */
    private $invoices = [];

    /** @var string */
    private $outputCurrencyCode;

    /** @var CurrencyConverterInterface */
    private $currencyConverter;

    /**
     * @param CurrencyConverterInterface $converter
     */
    public function __construct(CurrencyConverterInterface $converter)
    {
        $this->currencyConverter = $converter;
    }

    /**
     * @param string $vat
     * @return Total[]
     */
    public function getTotals($vat = ''): array
    {
        if (!$this->outputCurrencyCode) {
            throw CalculatorException::outputCurrencyNotSet();
        }

        /** @var Total[] $sumsByCustomer */
        $sumsByCustomer = [];
        foreach ($this->invoices as $invoice) {
            if ($vat && $vat !== $invoice->getVat()) {
                continue;
            }

            $customer = $invoice->getCustomer();
            if (!array_key_exists($customer, $sumsByCustomer)) {
                $sumsByCustomer[$customer] = new Total($customer, new Decimal(0), $this->outputCurrencyCode);
            }

            $totalInOutputCurrency = $this->currencyConverter->convert(
                new Decimal($invoice->getTotal()),
                $invoice->getCurrencyCode(),
                $this->outputCurrencyCode
            );

            switch ($invoice->getType()) {
                case InvoiceType::INVOICE:
                case InvoiceType::DEBIT:
                    $sumsByCustomer[$customer]->addToAmount($totalInOutputCurrency);
                    break;
                case InvoiceType::CREDIT:
                    $sumsByCustomer[$customer]->subFromAmount($totalInOutputCurrency);
                    break;
                default:
                    throw CalculatorException::notSupportedInvoiceType($invoice->getType());
            }
        }

        return $sumsByCustomer;
    }

    /**
     * @param Invoice[] $invoices
     */
    public function setInvoices(array $invoices): void
    {
        foreach ($invoices as $invoice) {
            $parentId = $invoice->getParentId();
            if ($parentId && !isset($invoices[$parentId])) {
                throw CalculatorException::missingParentDocument($parentId);
            }
        }

        $this->invoices = $invoices;
    }

    /**
     * @param array $currencies
     */
    public function setCurrencies(array $currencies): void
    {
        $this->currencyConverter->setCurrencies($currencies);
    }

    /**
     * @param string $code
     */
    public function setOutputCurrencyCode(string $code): void
    {
        $this->currencyConverter->validateCurrencyCode($code);

        $this->outputCurrencyCode = $code;
    }
}
