<?php

namespace Tests\Unit;


use Clippings\Component\Calculator\Exception\InvoiceFactoryException;
use Clippings\Component\Calculator\Facade\StringDataSource;
use Clippings\Component\Calculator\InvoiceFactory;
use Clippings\Component\Calculator\Util\Parser\CSVParser;
use PHPUnit\Framework\TestCase;

class InvoiceFactoryTest extends TestCase
{
    /** @var InvoiceFactory */
    private $factory;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->factory = new InvoiceFactory();
    }

    public function testCreateFromCSVRowMissingCustomer()
    {
        $this->expectException(InvoiceFactoryException::class);
        $this->expectExceptionCode(InvoiceFactoryException::MISSING_VALUE);

        $row = self::getInvoiceCsvRow();
        $row['Customer'] = '';

        $this->factory->createFromCSVRow($row);
    }

    public function testCreateFromCSVRowMissingVatNumber()
    {
        $this->expectException(InvoiceFactoryException::class);
        $this->expectExceptionCode(InvoiceFactoryException::MISSING_VALUE);

        $row = self::getInvoiceCsvRow();
        $row['Vat number'] = '';

        $this->factory->createFromCSVRow($row);
    }

    public function testCreateFromCSVRowMissingDocumentNumber()
    {
        $this->expectException(InvoiceFactoryException::class);
        $this->expectExceptionCode(InvoiceFactoryException::MISSING_VALUE);

        $row = self::getInvoiceCsvRow();
        $row['Document number'] = '';

        $this->factory->createFromCSVRow($row);
    }

    public function testCreateFromCSVRowMissingType()
    {
        $this->expectException(InvoiceFactoryException::class);
        $this->expectExceptionCode(InvoiceFactoryException::MISSING_VALUE);

        $row = self::getInvoiceCsvRow();
        $row['Type'] = '';

        $this->factory->createFromCSVRow($row);
    }

    public function testCreateFromCSVRowMissingCurrency()
    {
        $this->expectException(InvoiceFactoryException::class);
        $this->expectExceptionCode(InvoiceFactoryException::MISSING_VALUE);

        $row = self::getInvoiceCsvRow();
        $row['Currency'] = '';

        $this->factory->createFromCSVRow($row);
    }

    public function testCreateFromCSVRowMissingTotal()
    {
        $this->expectException(InvoiceFactoryException::class);
        $this->expectExceptionCode(InvoiceFactoryException::MISSING_VALUE);

        $row = self::getInvoiceCsvRow();
        $row['Total'] = '';

        $this->factory->createFromCSVRow($row);
    }

    public function testCreateFromCSVRowStringTotal()
    {
        $this->expectException(InvoiceFactoryException::class);
        $this->expectExceptionCode(InvoiceFactoryException::TOTAL_MUST_BE_NUMERIC);

        $row = self::getInvoiceCsvRow();
        $row['Total'] = 'asd';

        $this->factory->createFromCSVRow($row);
    }

    public function testCreateFromCSVRowNegativeTotal()
    {
        $this->expectException(InvoiceFactoryException::class);
        $this->expectExceptionCode(InvoiceFactoryException::TOTAL_MUST_NOT_BE_NEGATIVE);

        $row = self::getInvoiceCsvRow();
        $row['Total'] = '-3';

        $this->factory->createFromCSVRow($row);
    }

    public function testCreateFromCSVRowUnknownType()
    {
        $this->expectException(InvoiceFactoryException::class);
        $this->expectExceptionCode(InvoiceFactoryException::UNKNOWN_VALUE);

        $row = self::getInvoiceCsvRow();
        $row['Type'] = 5;

        $this->factory->createFromCSVRow($row);
    }

    public function testCreateFromCSVRow()
    {
        $row = self::getInvoiceCsvRow();
        $invoice = $this->factory->createFromCSVRow($row);

        $this->assertEquals($row['Customer'], $invoice->getCustomer());
        $this->assertEquals($row['Vat number'], $invoice->getVat());
        $this->assertEquals($row['Document number'], $invoice->getId());
        $this->assertEquals($row['Type'], $invoice->getType());
        $this->assertEquals($row['Parent document'], $invoice->getParentId());
        $this->assertEquals($row['Currency'], $invoice->getCurrencyCode());
        $this->assertEquals($row['Total'], (string)$invoice->getTotal()->scale(0));
    }

    public function testCreateManyFromCSVGenerator()
    {
        $lines = [
            'Customer,Vat number,Document number,Type,Parent document,Currency,Total',
            'Vendor 1,123456789,1000000260,2,1000000257,EUR,100',
            'Vendor 1,123456789,1000000261,3,1000000257,GBP,50'
        ];

        $generator = new StringDataSource(implode(PHP_EOL, $lines));
        $parser = new CSVParser(true);

        $invoice1 = explode(',', $lines[1]);
        $invoice2 = explode(',', $lines[2]);

        $invoices = $this->factory->createManyFromCSVGenerator($parser->parseFromDataSource($generator));

        $this->assertEquals(count($invoices), 2);

        $this->assertEquals($invoice1[0], $invoices[0]->getCustomer());
        $this->assertEquals($invoice1[1], $invoices[0]->getVat());
        $this->assertEquals($invoice1[2], $invoices[0]->getId());
        $this->assertEquals($invoice1[3], $invoices[0]->getType());
        $this->assertEquals($invoice1[4], $invoices[0]->getParentId());
        $this->assertEquals($invoice1[5], $invoices[0]->getCurrencyCode());
        $this->assertEquals($invoice1[6], (string)$invoices[0]->getTotal()->scale(0));

        $this->assertEquals($invoice2[0], $invoices[1]->getCustomer());
        $this->assertEquals($invoice2[1], $invoices[1]->getVat());
        $this->assertEquals($invoice2[2], $invoices[1]->getId());
        $this->assertEquals($invoice2[3], $invoices[1]->getType());
        $this->assertEquals($invoice2[4], $invoices[1]->getParentId());
        $this->assertEquals($invoice2[5], $invoices[1]->getCurrencyCode());
        $this->assertEquals($invoice2[6], (string)$invoices[1]->getTotal()->scale(0));
    }

    /**
     * @return array
     */
    private static function getInvoiceCsvRow(): array
    {
        return [
            'Customer' => 'Vendor 1',
            'Vat number' => '1234',
            'Document number' => '1232',
            'Type' => 1,
            'Parent document' => '22123',
            'Currency' => 'USD',
            'Total' => '123'
        ];
    }
}
