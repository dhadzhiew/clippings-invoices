<?php

namespace Clippings\Component\Calculator\Util\Parser;

class CSVParser
{
    /** @var bool */
    private $hasHeaders;

    /**
     * @param bool $hasHeaders
     */
    public function __construct($hasHeaders = true)
    {
        $this->hasHeaders = $hasHeaders;
    }

    /**
     * @param DataSourceInterface $dataSource
     * @return \Generator
     */
    public function parseFromDataSource(DataSourceInterface $dataSource): \Generator
    {
        $headers = [];
        foreach ($dataSource->getLines() as $line) {
            $parsedLine = str_getcsv($line);

            if ($this->hasHeaders) {
                if (empty($headers)) {
                    $headers = $parsedLine;
                } else {
                    yield self::mapKeys($parsedLine, $headers);
                }
            } else {
                yield $parsedLine;
            }
        }
    }

    /**
     * @param array $data
     * @param array $map
     * @return array
     */
    private static function mapKeys(array $data, array $map): array
    {
        $result = [];

        foreach ($data as $index => $value) {
            $key = $map[$index] ?? $index;
            $result[$key] = $value;
        }

        return $result;
    }
}
