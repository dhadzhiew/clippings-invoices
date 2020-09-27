<?php

namespace Tests\Unit;


use Clippings\Component\Calculator\Calculator;
use Clippings\Component\Calculator\CurrencyConverter;
use Clippings\Component\Calculator\Exception\ImportInvoicesCommandException;
use Clippings\Component\Calculator\ImportInvoicesCommand;
use Clippings\Component\Calculator\InvoiceFactory;
use Clippings\Component\Calculator\Util\Parser\CSVFileParser;
use Clippings\Component\Calculator\Util\Parser\FileDataSource;
use PHPUnit\Framework\TestCase;

class ImportInvoicesCommandTest extends TestCase
{
    /** @var string */
    private const CSV_FILE_PATH = __DIR__ . '/../data.csv';

    /** @var ImportInvoicesCommand */
    private $command;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $fileParser = new CSVFileParser(new FileDataSource());
        $calculator = new Calculator(new CurrencyConverter(['GBP', 'USD', 'EUR']));
        $factory = new InvoiceFactory();

        $this->command = new ImportInvoicesCommand($calculator, $fileParser, $factory);
    }

    public function testExecMissingFilePath()
    {
        $this->expectException(ImportInvoicesCommandException::class);
        $this->expectExceptionCode(ImportInvoicesCommandException::MISSING_PARAM);

        $this->command->exec([], []);
    }

    public function testExecFileMissingExchangeRates()
    {
        $this->expectException(ImportInvoicesCommandException::class);
        $this->expectExceptionCode(ImportInvoicesCommandException::MISSING_PARAM);

        $this->command->exec([self::CSV_FILE_PATH], []);
    }

    public function testExecFileMissingOutputCurrency()
    {
        $this->expectException(ImportInvoicesCommandException::class);
        $this->expectExceptionCode(ImportInvoicesCommandException::MISSING_PARAM);

        $this->command->exec([self::CSV_FILE_PATH, 'EUR:1,USD:0.987,GBP:0.878'], []);
    }

    public function testExecFileInvalidExchangeRates()
    {
        $this->expectException(ImportInvoicesCommandException::class);
        $this->expectExceptionCode(ImportInvoicesCommandException::CANNOT_PARSE_EXCHANGE_RATE);

        $this->command->exec([self::CSV_FILE_PATH, 'fake', 'GBP'], []);
    }

    public function testExecFileInvalidExchangeRatesItem()
    {
        $this->expectException(ImportInvoicesCommandException::class);
        $this->expectExceptionCode(ImportInvoicesCommandException::CANNOT_PARSE_EXCHANGE_RATE);

        $this->command->exec([self::CSV_FILE_PATH, 'EUR:1,USD:0.987,GBP:d0.878', 'GBP'], []);
    }

    public function testExecFileNotExists()
    {
        $this->expectException(ImportInvoicesCommandException::class);
        $this->expectExceptionCode(ImportInvoicesCommandException::FILE_NOT_EXISTS);

        $this->command->exec([__DIR__ . '/wq/we/e/sa/d/asd/q/wew/weq/file', 'EUR:1,USD:0.987,GBP:0.878', 'GBP'], []);
    }

    public function testExecNoRecords()
    {
        $tmpFile = tempnam(__DIR__, 'tmp');

        ob_start();
        try {
            $this->command->exec([$tmpFile, 'EUR:1,USD:0.987,GBP:0.878', 'GBP'], []);
        } finally {
            unlink($tmpFile);
        }

        $actual = ob_get_clean();

        $this->assertEquals('Cannot find any records.', $actual);
    }

    public function testExecFilterVat()
    {
        ob_start();
        $this->command->exec([self::CSV_FILE_PATH, 'EUR:1,USD:0.987,GBP:0.878', 'GBP'], ['vat' => '987654321']);
        $actual = ob_get_clean();
        $expected = 'Vendor 2 - 612.28 GBP' . PHP_EOL;

        $this->assertEquals($expected, $actual);
    }

    public function testExecFilterUnknownVat()
    {
        ob_start();
        $this->command->exec([self::CSV_FILE_PATH, 'EUR:1,USD:0.987,GBP:0.878', 'GBP'], ['vat' => '000000']);
        $actual = ob_get_clean();

        $this->assertEquals('Cannot find any records.', $actual);
    }

    public function testExec()
    {
        ob_start();
        $this->command->exec([self::CSV_FILE_PATH, 'EUR:1,USD:0.987,GBP:0.878', 'GBP'], []);
        $actual = ob_get_clean();
        $expected = 'Vendor 1 - 1722.82 GBP' . PHP_EOL . 'Vendor 2 - 612.28 GBP' . PHP_EOL . 'Vendor 3 - 1387.79 GBP' . PHP_EOL;

        $this->assertEquals($expected, $actual);
    }
}
