<?php

namespace Clippings\Component\Calculator\Util\File;

class FileReader
{
    /** @var resource */
    private $fileResource;

    /**
     * @param string $filePath
     * @throws FileReaderException
     */
    public function open(string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw FileReaderException::fileNotExists($filePath);
        }

        if (!is_readable($filePath)) {
            throw FileReaderException::fileNotReadable($filePath);
        }

        $this->fileResource = @fopen($filePath, 'rb');
        if ($this->fileResource === false) {
            throw FileReaderException::cannotOpenFile($filePath);
        }
    }

    /**
     * @return \Generator
     */
    public function getLines(): \Generator
    {
        if (!$this->fileResource) {
            throw FileReaderException::fileNotOpened();
        }

        while (($line = fgets($this->fileResource)) !== false) {
            yield $line;
        }

        rewind($this->fileResource);
    }

    public function close(): void
    {
        if (!$this->fileResource) {
            return;
        }

        fclose($this->fileResource);
    }

    public function __destruct()
    {
        $this->close();
    }
}
