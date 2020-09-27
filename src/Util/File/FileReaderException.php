<?php

namespace Clippings\Component\Calculator\Util\File;

class FileReaderException extends \Exception
{
    /** @var int */
    public const FILE_OPEN_ERROR = 1;

    /** @var int */
    public const FILE_NOT_EXISTS = 2;

    /** @var int */
    public const FILE_NOT_READABLE = 3;

    /** @var int */
    public const FILE_NOT_OPENED = 4;

    /**
     * @param string $filePath
     * @return FileReaderException
     */
    public static function cannotOpenFile(string $filePath): FileReaderException
    {
        return new self('Cannot open file: ' . $filePath, self::FILE_OPEN_ERROR);
    }

    /**
     * @param string $filePath
     * @return FileReaderException
     */
    public static function fileNotExists(string $filePath): FileReaderException
    {
        return new self(sprintf('File "%s" does not exist.', $filePath), self::FILE_NOT_EXISTS);
    }

    /**
     * @param string $filePath
     * @return FileReaderException
     */
    public static function fileNotReadable(string $filePath): FileReaderException
    {
        return new self(sprintf('File "%s" is not readable.', $filePath), self::FILE_NOT_READABLE);
    }

    /**
     * @return FileReaderException
     */
    public static function fileNotOpened(): FileReaderException
    {
        return new self('There is no opened file.', self::FILE_NOT_OPENED);
    }
}
