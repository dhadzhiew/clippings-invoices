<?php

namespace Clippings\Component\Calculator\Contract;

use Clippings\Component\Calculator\Invoice;

interface InvoiceFactoryInterface
{
    /**
     * @param \Generator $dataGenerator
     * @return Invoice[]
     */
    public function createManyFromCSVGenerator(\Generator $dataGenerator): array;

    /**
     * @param array $data
     * @return Invoice
     */
    public function createFromCSVRow(array $data): Invoice;
}
