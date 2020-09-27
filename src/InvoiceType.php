<?php

namespace Clippings\Component\Calculator;

use Clippings\Component\Calculator\Util\Type\Enum;

abstract class InvoiceType extends Enum
{
    /** @var int */
    public const INVOICE = 1;

    /** @var int */
    public const CREDIT = 2;

    /** @var int */
    public const DEBIT = 3;
}
