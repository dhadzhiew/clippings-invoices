<?php

namespace Clippings\Component\Calculator;

use Clippings\Component\Calculator\Util\Type\Decimal;

class Invoice
{
    /** @var string */
    private $id;

    /** @var string */
    private $customer;

    /** @var string */
    private $vat;

    /** @var int */
    private $type;

    /** @var string */
    private $parentId;

    /** @var string */
    private $currencyCode;

    /** @var Decimal */
    private $total;

    /**
     * @param string $id
     * @param string $customer
     * @param string $vat
     * @param int $type
     * @param string $parentId
     * @param string $currencyCode
     * @param Decimal $total
     */
    public function __construct(
        string $id,
        string $customer,
        string $vat,
        int $type,
        string $parentId,
        string $currencyCode,
        Decimal $total
    ) {
        $this->id = $id;
        $this->customer = $customer;
        $this->vat = $vat;
        $this->type = $type;
        $this->parentId = $parentId;
        $this->currencyCode = $currencyCode;
        $this->total = $total;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCustomer(): string
    {
        return $this->customer;
    }

    /**
     * @return string
     */
    public function getVat(): string
    {
        return $this->vat;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return Invoice
     */
    public function getParentId(): string
    {
        return $this->parentId;
    }

    /**
     * @return string
     */
    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    /**
     * @return Decimal
     */
    public function getTotal(): Decimal
    {
        return $this->total;
    }
}
