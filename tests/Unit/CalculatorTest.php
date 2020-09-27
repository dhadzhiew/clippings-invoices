<?php

namespace Tests\Unit;

use Clippings\Component\Calculator\Calculator;
use Clippings\Component\Calculator\Contract\CurrencyConverterInterface;
use Clippings\Component\Calculator\Currency;
use Clippings\Component\Calculator\CurrencyConverter;
use Clippings\Component\Calculator\Exception\CalculatorException;
use Clippings\Component\Calculator\Invoice;
use Clippings\Component\Calculator\Util\Type\Decimal;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    public function testSetInvoicesMissingParentDocument()
    {
        $converter = $this->createMock(CurrencyConverterInterface::class);
        $calculator = new Calculator($converter);

        $this->expectException(CalculatorException::class);
        $this->expectExceptionCode(CalculatorException::MISSING_INVOICE_PARENT_DOCUMENT);

        $calculator->setInvoices([
            new Invoice(
                '12345',
                'Customer 1',
                '123',
                1,
                '33',
                'USD',
                new Decimal(123)
            )
        ]);
    }

    public function testGetTotals()
    {
        $totals = self::getCalculator()->getTotals();

        $this->assertEquals('8.26973474801057', (string)$totals['C1']->getAmount());
        $this->assertEquals('139.26733611216288', (string)$totals['C2']->getAmount());
    }

    public function testGetTotalsFilter()
    {
        $totals = self::getCalculator()->getTotals('113');

        $this->assertEquals(count($totals), 1);
        $this->assertEquals('139.26733611216288', (string)$totals['C2']->getAmount());
    }

    /**
     * @return Calculator
     * @throws CalculatorException
     * @throws \Clippings\Component\Calculator\Exception\CurrencyConverterException
     */
    private static function getCalculator()
    {
        $converter = new CurrencyConverter(['USD', 'EUR', 'GBP']);
        $converter->setCurrencies([
            new Currency('EUR', new Decimal(1)),
            new Currency('USD', new Decimal('1.16')),
            new Currency('GBP', new Decimal('0.91'))
        ]);

        $calculator = new Calculator($converter);
        $calculator->setInvoices([
            new Invoice(1, 'C1', '123', 2, '2', 'USD', new Decimal('2.24')), // -1.9310... EUR
            new Invoice(2, 'C1', '123', 1, '', 'GBP', new Decimal('5.67')), //  +6.2307... EUR
            new Invoice(3, 'C1', '123', 3, '2', 'EUR', new Decimal('3.97')), // +3.97      EUR

            new Invoice('89345', 'C2', '113', 3, '89395', 'GBP', new Decimal('83.59')), // +91.8571... EUR
            new Invoice('89349', 'C2', '113', 2, '89395', 'GBP', new Decimal('0.27')), //  -0.2967...  EUR
            new Invoice('89395', 'C2', '113', 1, '', 'USD', new Decimal('55.34')), //      +47.7068... EUR
        ]);

        return $calculator;
    }
}