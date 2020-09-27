<?php

namespace Clippings\Component\Calculator;

use Clippings\Component\Calculator\Contract\InvoiceFactoryInterface;
use Clippings\Component\Calculator\Exception\InvoiceFactoryException;
use Clippings\Component\Calculator\Util\Type\Decimal;

class InvoiceFactory implements InvoiceFactoryInterface
{
    /** @var string */
    private const CSV_COLUMN_CUSTOMER = 'Customer';

    /** @var string */
    private const CSV_COLUMN_VAT_NUMBER = 'Vat number';

    /** @var string */
    private const CSV_COLUMN_DOCUMENT_NUMBER = 'Document number';

    /** @var string */
    private const CSV_COLUMN_TYPE = 'Type';

    /** @var string */
    private const CSV_COLUMN_PARENT_DOCUMENT = 'Parent document';

    /** @var string */
    private const CSV_COLUMN_CURRENCY = 'Currency';

    /** @var string */
    private const CSV_COLUMN_TOTAL = 'Total';

    /**
     * @param \Generator $dataGenerator
     * @return Invoice[]
     */
    public function createManyFromCSVGenerator(\Generator $dataGenerator): array
    {
        $invoices = [];
        foreach ($dataGenerator as $data) {
            $invoice = $this->createFromCSVRow($data);

            $invoices[$invoice->getId()] = $invoice;
        }

        return $invoices;
    }

    /**
     * @param array $data
     * @return Invoice
     */
    public function createFromCSVRow(array $data): Invoice
    {
        $parentDocument = $data[self::CSV_COLUMN_PARENT_DOCUMENT] ?? null;
        $customer = $data[self::CSV_COLUMN_CUSTOMER] ?? null;
        if (!$customer) {
            throw InvoiceFactoryException::missingValue(self::CSV_COLUMN_CUSTOMER);
        }

        $vat = $data[self::CSV_COLUMN_VAT_NUMBER] ?? null;
        if (!$vat) {
            throw InvoiceFactoryException::missingValue(self::CSV_COLUMN_VAT_NUMBER);
        }

        $documentNumber = $data[self::CSV_COLUMN_DOCUMENT_NUMBER] ?? null;
        if (!$documentNumber) {
            throw InvoiceFactoryException::missingValue(self::CSV_COLUMN_DOCUMENT_NUMBER);
        }

        $type = (int)($data[self::CSV_COLUMN_TYPE] ?? 0);
        if (!$type) {
            throw InvoiceFactoryException::missingValue(self::CSV_COLUMN_TYPE);
        }
        if (!InvoiceType::isValid($type)) {
            throw InvoiceFactoryException::unknownValue(self::CSV_COLUMN_TYPE, $type, InvoiceType::toArray());
        }

        $currency = $data[self::CSV_COLUMN_CURRENCY] ?? null;
        if (!$currency) {
            throw InvoiceFactoryException::missingValue(self::CSV_COLUMN_CURRENCY);
        }

        $total = $data[self::CSV_COLUMN_TOTAL] ?? null;
        if ($total === null || $total === '') {
            throw InvoiceFactoryException::missingValue(self::CSV_COLUMN_TOTAL);
        }

        if (!is_numeric($total)) {
            throw InvoiceFactoryException::totalMustBeNumeric();
        }

        $total = new Decimal($total);

        if ($total->isSmaller(0)) {
            throw InvoiceFactoryException::totalMustNotBeNegative();
        }

        return new Invoice(
            $documentNumber,
            $customer,
            $vat,
            $type,
            $parentDocument,
            $currency,
            $total
        );
    }
}
