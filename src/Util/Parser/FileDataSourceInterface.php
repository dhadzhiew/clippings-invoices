<?php

namespace Clippings\Component\Calculator\Util\Parser;

interface FileDataSourceInterface extends DataSourceInterface
{
    public function open(string $filePath);

    public function close(): void;
}
