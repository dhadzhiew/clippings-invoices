<?php

namespace Clippings\Component\Calculator\Util\Parser;

interface DataSourceInterface
{
    public function getLines(): \Generator;
}
