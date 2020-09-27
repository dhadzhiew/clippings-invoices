<?php

namespace Clippings\Component\Calculator\Util\Parser;

interface FileParserInterface
{
    public function open(string $filePath);

    public function parse();
}
