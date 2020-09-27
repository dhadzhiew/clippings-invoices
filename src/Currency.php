<?php

namespace Clippings\Component\Calculator;

use Clippings\Component\Calculator\Util\Type\Decimal;

class Currency
{
    /** @var string */
    private $code;

    /** @var Decimal */
    private $rate;

    /**.
     * @param string $code
     * @param Decimal $rate
     */
    public function __construct(string $code, Decimal $rate)
    {
        $this->code = $code;
        $this->rate = $rate;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return Decimal
     */
    public function getRate(): Decimal
    {
        return $this->rate;
    }
}
