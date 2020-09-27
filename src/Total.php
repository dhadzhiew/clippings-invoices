<?php

namespace Clippings\Component\Calculator;

use Clippings\Component\Calculator\Util\Type\Decimal;

class Total
{
    /** @var string */
    private $customer;

    /** @var Decimal */
    private $amount;

    /** @var string */
    private $currencyCode;

    public function __construct(string $customer, Decimal $amount, string $currencyCode)
    {
        $this->customer = $customer;
        $this->amount = $amount;
        $this->currencyCode = $currencyCode;
    }

    /**
     * @return string
     */
    public function getCustomer(): string
    {
        return $this->customer;
    }

    /**
     * @return Decimal
     */
    public function getAmount(): Decimal
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    /**
     * @param string $amount
     */
    public function addToAmount(string $amount): void
    {
        $this->amount = $this->amount->add($amount);
    }

    /**
     * @param string $amount
     */
    public function subFromAmount(string $amount): void
    {
        $this->amount = $this->amount->sub($amount);
    }
}
