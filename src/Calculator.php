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
        $resultCurrencyCode = $this->currencyConverter->getBaseCurrencyCode();
        /** @var Total[] $sumsByCustomer */
        $sumsByCustomer = [];
        foreach ($this->invoices as $invoice) {
            if ($vat && $vat !== $invoice->getVat()) {
                continue;
            }

            $customer = $invoice->getCustomer();
            if (!array_key_exists($customer, $sumsByCustomer)) {
                $sumsByCustomer[$customer] = new Total($customer, new Decimal(0), $resultCurrencyCode);
            }

            $totalInResultCurrency = $this->currencyConverter->convert(
                new Decimal($invoice->getTotal()),
                $invoice->getCurrencyCode(),
                $resultCurrencyCode
            );

            switch ($invoice->getType()) {
                case InvoiceType::INVOICE:
                case InvoiceType::DEBIT:
                    $sumsByCustomer[$customer]->addToAmount($totalInResultCurrency);
                    break;
                case InvoiceType::CREDIT:
                    $sumsByCustomer[$customer]->subFromAmount($totalInResultCurrency);
                    break;
                default:
                    throw CalculatorException::notSupportedInvoiceType($invoice->getType());
            }
        }

        return $sumsByCustomer;
    }

    /**
     * @return CurrencyConverterInterface
     */
    public function getCurrencyConverter(): CurrencyConverterInterface
    {
        return $this->currencyConverter;
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
}
