<?php

namespace Clippings\Component\Calculator\Util\Command;

interface CommandInterface
{
    public function getDescription(): string;

    public function exec(array $params, array $options);
}
