<?php

namespace Clippings\Component\Calculator;

use Clippings\Component\Calculator\Contract\CurrencyConverterInterface;
use Clippings\Component\Calculator\Exception\CurrencyConverterException;
use Clippings\Component\Calculator\Util\Type\Decimal;

class CurrencyConverter implements CurrencyConverterInterface
{
    /** @var int */
    private const DECIMAL_SCALE = 14;

    /** @var string[] */
    private $allowedCurrencyCodes = [];

    /** @var string */
    private $baseCurrencyCode;

    /** @var Currency[] */
    private $currencies = [];

    /**
     * @param array $allowedCurrencyCodes
     */
    public function __construct(array $allowedCurrencyCodes)
    {
        $this->allowedCurrencyCodes = $allowedCurrencyCodes;
    }

    /**
     * @param Currency[] $currencies
     */
    public function setCurrencies(array $currencies): void
    {
        $this->currencies = [];

        foreach ($currencies as $currency) {
            if (!($currency instanceof Currency)) {
                throw new \InvalidArgumentException('Invalid currency type.');
            }

            $this->addCurrency($currency);

            if ($currency->getRate()->isEqual(1)) {
                $this->setBaseCurrencyCode($currency->getCode());
            }
        }

        if ($this->baseCurrencyCode === null) {
            throw CurrencyConverterException::missingBaseCurrency();
        }
    }

    /**
     * @param Decimal $amount
     * @param string $fromCode
     * @param string $toCode
     * @return Decimal
     * @throws CurrencyConverterException
     */
    public function convert(Decimal $amount, string $fromCode, string $toCode): Decimal
    {
        $from = $this->currencies[$fromCode] ?? null;
        if (!$from) {
            throw CurrencyConverterException::missingCurrency($fromCode);
        }

        $to = $this->currencies[$toCode] ?? null;
        if (!$to) {
            throw CurrencyConverterException::missingCurrency($toCode);
        }

        return $amount
            ->scale(self::DECIMAL_SCALE)
            ->mul(
                $to
                    ->getRate()
                    ->scale(self::DECIMAL_SCALE)
                    ->div((string)$from->getRate())
            );
    }

    /**
     * @param string $code
     * @throws CurrencyConverterException
     */
    public function validateCurrencyCode(string $code): void
    {
        if (!in_array($code, $this->allowedCurrencyCodes, true)) {
            throw CurrencyConverterException::currencyNotAllowed($code);
        }
    }

    /**
     * @return string
     */
    public function getBaseCurrencyCode(): string
    {
        return $this->baseCurrencyCode;
    }

    /**
     * @param Currency $currency
     */
    private function addCurrency(Currency $currency): void
    {
        $this->validateCurrencyCode($currency->getCode());

        $this->currencies[$currency->getCode()] = $currency;
    }

    /**
     * @param string $code
     * @throws \Exception
     */
    private function setBaseCurrencyCode(string $code): void
    {
        if ($this->baseCurrencyCode !== null) {
            throw CurrencyConverterException::baseCurrencyAlreadySet();
        }

        $this->validateCurrencyCode($code);

        $this->baseCurrencyCode = $code;
    }
}
