<?php

namespace Clippings\Component\Calculator\Util\Parser;

class CSVFileParser extends CSVParser implements FileParserInterface
{
    /** @var FileDataSourceInterface */
    private $dataSource;

    public function __construct(FileDataSourceInterface $dataSource, bool $hasHeaders = true)
    {
        parent::__construct($hasHeaders);

        $this->dataSource = $dataSource;
    }

    /**
     * @param string $filePath
     */
    public function open(string $filePath): void
    {
        $this->dataSource->open($filePath);
    }

    public function close(): void
    {
        $this->dataSource->close();
    }

    public function parse(): \Generator
    {
        return $this->parseFromDataSource($this->dataSource);
    }
}
