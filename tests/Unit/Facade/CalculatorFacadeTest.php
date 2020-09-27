<?php

namespace Tests\Unit\Facade;


use Clippings\Component\Calculator\Currency;
use Clippings\Component\Calculator\Facade\CalculatorFacade;
use Clippings\Component\Calculator\Util\Type\Decimal;
use PHPUnit\Framework\TestCase;

class CalculatorFacadeTest extends TestCase
{
    /** @var string */
    private const CSV_FILE_PATH = __DIR__ . '/../../data.csv';

    /** @var CalculatorFacade */
    private $instance;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->instance = new CalculatorFacade(['GBP', 'USD', 'EUR']);
    }

    public function testGetTotals()
    {
        $this->instance->setData(file_get_contents(self::CSV_FILE_PATH));
        $this->instance->setCurrencies([
            new Currency('EUR', new Decimal(1)),
            new Currency('USD', new Decimal('1.16')),
            new Currency('GBP', new Decimal('0.91'))
        ]);
        $totals = $this->instance->getTotals();

        $this->assertEquals(count($totals), 3);
        $this->assertEquals('1899.77', (string)$totals['Vendor 1']->getAmount()->scale(2));
        $this->assertEquals('727.58', (string)$totals['Vendor 2']->getAmount()->scale(2));
        $this->assertEquals('1528.57', (string)$totals['Vendor 3']->getAmount()->scale(2));
    }

    public function testGetTotalsVat()
    {
        $this->instance->setData(file_get_contents(self::CSV_FILE_PATH));
        $this->instance->setCurrencies([
            new Currency('EUR', new Decimal(1)),
            new Currency('USD', new Decimal('1.16')),
            new Currency('GBP', new Decimal('0.91'))
        ]);
        $totals = $this->instance->getTotals('123465123');

        $this->assertEquals(count($totals), 1);
        $this->assertEquals('1528.57', (string)$totals['Vendor 3']->getAmount()->scale(2));
    }

    public function testGetTotalsUnknownVat()
    {
        $this->instance->setData(file_get_contents(self::CSV_FILE_PATH));
        $this->instance->setCurrencies([
            new Currency('EUR', new Decimal(1)),
            new Currency('USD', new Decimal('1.16')),
            new Currency('GBP', new Decimal('0.91'))
        ]);
        $totals = $this->instance->getTotals('00000');

        $this->assertEquals(count($totals), 0);
    }
}