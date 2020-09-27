<?php

namespace Clippings\Component\Calculator;

use Clippings\Component\Calculator\Contract\CalculatorInterface;
use Clippings\Component\Calculator\Contract\InvoiceFactoryInterface;
use Clippings\Component\Calculator\Exception\ImportCommandException;
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
     * @throws ImportCommandException
     */
    public function exec(array $params, array $options)
    {
        $normalizedParams = self::normalizeParams($params);

        $this->calculator->setOutputCurrencyCode($normalizedParams[self::PARAM_OUTPUT_CURRENCY]);
        $this->calculator->setCurrencies($normalizedParams[self::PARAM_EXCHANGE_RATES]);

        $this->parser->open($normalizedParams[self::PARAM_FILE_PATH]);
        $dataGenerator = $this->parser->parse();

        $invoices = $this->invoiceFactory->createManyFromCSVGenerator($dataGenerator);

        $this->calculator->setInvoices($invoices);
        $totals = $this->calculator->getTotals($options['vat'] ?? '');

        if (empty($totals)) {
            echo 'Cannot find any records.';
        }

        foreach ($totals as $total) {
            $amount = new Decimal($total->getAmount(), 2);
            echo sprintf('%s - %s %s', $total->getCustomer(), $amount, $total->getCurrencyCode()) . PHP_EOL;
        }
    }

    /**
     * @param array $params
     * @return array
     * @throws ImportCommandException
     */
    private static function normalizeParams(array $params): array
    {
        $keys = [self::PARAM_FILE_PATH, self::PARAM_EXCHANGE_RATES, self::PARAM_OUTPUT_CURRENCY];
        $normalized = [];

        foreach ($keys as $index => $name) {
            $param = $params[$index] ?? null;
            if (!$param) {
                throw ImportCommandException::missingParam($name);
            }

            $normalized[$name] = $param;
        }

        $normalized[self::PARAM_EXCHANGE_RATES] = self::parseExchangeRates($normalized[self::PARAM_EXCHANGE_RATES]);

        return $normalized;
    }

    /**
     * @param string $rates
     * @return Currency[]
     * @throws ImportCommandException
     */
    private static function parseExchangeRates(string $rates)
    {
        $rates = explode(',', $rates);
        $currencies = [];

        foreach ($rates as $rate) {
            if (!preg_match('/^(\w+):(\d+(?:\.\d+)?)$/', $rate, $matches)) {
                throw ImportCommandException::cannotParseExchangeRate($rate);
            }

            $currencies[] = new Currency($matches[1], new Decimal($matches[2]));
        }

        return $currencies;
    }
}
