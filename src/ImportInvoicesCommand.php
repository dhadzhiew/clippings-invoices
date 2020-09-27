<?php

namespace Clippings\Component\Calculator;

use Clippings\Component\Calculator\Contract\CalculatorInterface;
use Clippings\Component\Calculator\Contract\InvoiceFactoryInterface;
use Clippings\Component\Calculator\Exception\ImportInvoicesCommandException;
use Clippings\Component\Calculator\Util\Command\CommandInterface;
use Clippings\Component\Calculator\Util\Parser\FileParserInterface;
use Clippings\Component\Calculator\Util\Type\Decimal;

class ImportInvoicesCommand implements CommandInterface
{
    /** @var string */
    private const PARAM_FILE_PATH = 'filePath';

    /** @var string */
    private const PARAM_EXCHANGE_RATES = 'exchangeRates';

    /** @var string */
    private const PARAM_OUTPUT_CURRENCY = 'outputCurrency';

    /** @var CalculatorInterface */
    private $calculator;

    /** @var InvoiceFactoryInterface */
    private $invoiceFactory;

    /** @var FileParserInterface */
    private $parser;

    /**
     * @param CalculatorInterface $calculator
     * @param FileParserInterface $parser
     * @param InvoiceFactoryInterface $factory
     */
    public function __construct(
        CalculatorInterface $calculator,
        FileParserInterface $parser,
        InvoiceFactoryInterface $factory
    ) {
        $this->calculator = $calculator;
        $this->parser = $parser;
        $this->invoiceFactory = $factory;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return '<filePath> <exchangeRates ex. EUR:1,USD:0.987,GBP:0.878> <ouputCurrency> <--vat ex. --vat=12345 optional>';
    }

    /**
     * @param array $params
     * @param array $options
     * @throws ImportInvoicesCommandException
     */
    public function exec(array $params, array $options): void
    {
        $normalizedParams = self::normalizeParams($params);

        $currencyConverter = $this->calculator->getCurrencyConverter();
        $currencyConverter->setCurrencies($normalizedParams[self::PARAM_EXCHANGE_RATES]);

        $outputCurrencyCode = $normalizedParams[self::PARAM_OUTPUT_CURRENCY];
        $currencyConverter->validateCurrencyCode($outputCurrencyCode);

        $filePath = $normalizedParams[self::PARAM_FILE_PATH];

        if (!file_exists($filePath)) {
            throw ImportInvoicesCommandException::fileNotExists($filePath);
        }

        if (!is_readable($filePath)) {
            throw ImportInvoicesCommandException::fileNotReadable($filePath);
        }

        $this->parser->open($normalizedParams[self::PARAM_FILE_PATH]);
        $dataGenerator = $this->parser->parse();

        $invoices = $this->invoiceFactory->createManyFromCSVGenerator($dataGenerator);

        $this->calculator->setInvoices($invoices);
        $totals = $this->calculator->getTotals($options['vat'] ?? '');

        if (empty($totals)) {
            echo 'Cannot find any records.';
        }

        foreach ($totals as $total) {
            $amount = $currencyConverter
                ->convert($total->getAmount(), $total->getCurrencyCode(), $outputCurrencyCode)
                ->scale(2);

            echo sprintf('%s - %s %s', $total->getCustomer(), $amount, $outputCurrencyCode) . PHP_EOL;
        }
    }

    /**
     * @param array $params
     * @return array
     * @throws ImportInvoicesCommandException
     */
    private static function normalizeParams(array $params): array
    {
        $keys = [self::PARAM_FILE_PATH, self::PARAM_EXCHANGE_RATES, self::PARAM_OUTPUT_CURRENCY];
        $normalized = [];

        foreach ($keys as $index => $name) {
            $param = $params[$index] ?? null;
            if (!$param) {
                throw ImportInvoicesCommandException::missingParam($name);
            }

            $normalized[$name] = $param;
        }

        $normalized[self::PARAM_EXCHANGE_RATES] = self::parseExchangeRates($normalized[self::PARAM_EXCHANGE_RATES]);

        return $normalized;
    }

    /**
     * @param string $rates
     * @return Currency[]
     * @throws ImportInvoicesCommandException
     */
    private static function parseExchangeRates(string $rates): array
    {
        $rates = explode(',', $rates);
        $currencies = [];

        foreach ($rates as $rate) {
            if (!preg_match('/^(\w+):(\d+(?:\.\d+)?)$/', $rate, $matches)) {
                throw ImportInvoicesCommandException::cannotParseExchangeRate($rate);
            }

            $currencies[] = new Currency($matches[1], new Decimal($matches[2]));
        }

        return $currencies;
    }
}
