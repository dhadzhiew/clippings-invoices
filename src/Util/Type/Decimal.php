<?php

namespace Clippings\Component\Calculator\Util\Type;

class Decimal
{
    /** @var int */
    private $scale;

    /** @var string */
    private $value;

    public function __construct(string $value, int $scale = 3)
    {
        if (!is_numeric($value)) {
            throw new \InvalidArgumentException('The provided value must be numeric.');
        }

        $this->scale = $scale;
        $this->value = bcadd($value, 0, $this->scale);
    }

    /**
     * @param string $num
     * @return Decimal
     */
    public function add(string $num): Decimal
    {
        return new self(bcadd($this->value, $num, $this->scale), $this->scale);
    }


    /**
     * @param string $num
     * @return Decimal
     */
    public function sub(string $num): Decimal
    {
        return new self(bcsub($this->value, $num, $this->scale), $this->scale);
    }

    /**
     * @param string $num
     * @return Decimal
     */
    public function mul(string $num): Decimal
    {
        return new self(bcmul($this->value, $num, $this->scale), $this->scale);
    }

    /**
     * @param string $num
     * @return Decimal
     */
    public function div(string $num): Decimal
    {
        return new self(bcdiv($this->value, $num, $this->scale), $this->scale);
    }

    /**
     * @param int $scale
     * @return Decimal
     */
    public function scale(int $scale): Decimal
    {
        return new self(bcadd($this->value, 0, $scale), $scale);
    }

    /**
     * @param string $num
     * @return int
     */
    public function cmp(string $num)
    {
        return bccomp($this->value, $num, $this->scale);
    }

    /**
     * @param string $num
     * @return bool
     */
    public function isEqual(string $num)
    {
        return $this->cmp($num) === 0;
    }

    /**
     * @param string $num
     * @return bool
     */
    public function isSmaller(string $num)
    {
        return $this->cmp($num) === -1;
    }

    /**
     * @param string $num
     * @return bool
     */
    public function isGreater(string $num)
    {
        return $this->cmp($num) === 1;
    }

    /**
     * @return int
     */
    public function getScale(): int
    {
        return $this->getScale();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
