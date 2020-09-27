<?php

namespace Tests\Unit;

use Clippings\Component\Calculator\Currency;
use Clippings\Component\Calculator\CurrencyConverter;
use Clippings\Component\Calculator\Exception\CurrencyConverterException;
use Clippings\Component\Calculator\Util\Type\Decimal;
use PHPUnit\Framework\TestCase;

class CurrencyConverterTest extends TestCase
{
    /** @var CurrencyConverter */
    private $converter;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->converter = new CurrencyConverter(['EUR', 'USD', 'GBP']);
    }

    public function testValidateCurrencyCodeWithInvalidCode()
    {
        $this->expectException(CurrencyConverterException::class);
        $this->expectExceptionCode(CurrencyConverterException::CURRENCY_NOT_ALLOWED);

        $this->converter->validateCurrencyCode('test');
    }

    public function testValidateCurrencyCodeWithValidCode()
    {
        $this->converter->validateCurrencyCode('USD');

        $this->assertTrue(true);
    }

    public function testSetCurrencyMissingBaseCurrency()
    {
        $this->expectException(CurrencyConverterException::class);
        $this->expectExceptionCode(CurrencyConverterException::MISSING_BASE_CURRENCY);

        $this->converter->setCurrencies([
            new Currency('EUR', new Decimal(2)),
            new Currency('USD', new Decimal(3))
        ]);
    }

    public function testSetCurrencyNotAllowedCurrencyCode()
    {
        $this->expectException(CurrencyConverterException::class);
        $this->expectExceptionCode(CurrencyConverterException::CURRENCY_NOT_ALLOWED);

        $this->converter->setCurrencies([
            new Currency('EUR', new Decimal(1)),
            new Currency('fake', new Decimal(2))
        ]);
    }

    public function testSetCurrencyTooManyBaseCurrencies()
    {
        $this->expectException(CurrencyConverterException::class);
        $this->expectExceptionCode(CurrencyConverterException::BASE_CURRENCY_ALREADY_SET);

        $this->converter->setCurrencies([
            new Currency('EUR', new Decimal(1)),
            new Currency('USD', new Decimal(1))
        ]);
    }

    public function testSetCurrencyWithInvalidItem()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->converter->setCurrencies(['USD', 'EUR']);
    }

    public function testGetBaseCurrencyCode()
    {
        $this->converter->setCurrencies([
            new Currency('EUR', new Decimal(1)),
            new Currency('USD', new Decimal(2))
        ]);

        $this->assertEquals('EUR', $this->converter->getBaseCurrencyCode());
    }

    public function testConvertWithInvalidCurrencyFrom()
    {
        $this->converter->setCurrencies([
            new Currency('EUR', new Decimal(1)),
            new Currency('USD', new Decimal('1.16')),
            new Currency('GBP', new Decimal('0.91'))
        ]);

        $this->expectException(CurrencyConverterException::class);
        $this->expectExceptionCode(CurrencyConverterException::MISSING_CURRENCY);

        $this->converter->convert(new Decimal('2.22'), 'fake', 'USD');
    }

    public function testConvertWithInvalidCurrencyTo()
    {
        $this->converter->setCurrencies([
            new Currency('EUR', new Decimal(1)),
            new Currency('USD', new Decimal('1.16')),
            new Currency('GBP', new Decimal('0.91'))
        ]);

        $this->expectException(CurrencyConverterException::class);
        $this->expectExceptionCode(CurrencyConverterException::MISSING_CURRENCY);

        $this->converter->convert(new Decimal('2.22'), 'USD', 'fake');
    }

    public function testConvert()
    {
        $this->converter->setCurrencies([
            new Currency('EUR', new Decimal(1)),
            new Currency('USD', new Decimal('1.16')),
            new Currency('GBP', new Decimal('0.91'))
        ]);

        $gbpToUsd = $this->converter->convert(new Decimal('2.22'), 'GBP', 'USD');
        $this->assertEquals('2.82989010989009', (string) $gbpToUsd);

        $eurToGbp = $this->converter->convert(new Decimal(1), 'EUR', 'GBP');
        $this->assertEquals('0.91000000000000', (string) $eurToGbp);

        $usdToEur = $this->converter->convert(new Decimal(1), 'USD', 'EUR');
        $this->assertEquals((string) $usdToEur, '0.86206896551724');
    }
}