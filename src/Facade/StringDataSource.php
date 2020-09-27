<?php

namespace Clippings\Component\Calculator\Facade;

use Clippings\Component\Calculator\Util\Parser\DataSourceInterface;

class StringDataSource implements DataSourceInterface
{
    /** @var string */
    private $data;

    /**
     * @param string $data
     */
    public function __construct(string $data)
    {
        $this->data = $data;
    }

    /**
     * @return \Generator
     */
    public function getLines(): \Generator
    {
        $lines = explode(PHP_EOL, $this->data);
        foreach ($lines as $line) {
            yield $line;
        }
    }
}
