<?php

namespace Clippings\Component\Calculator\Util\Command;

class CommandException extends \Exception
{
    /** @var int */
    public const UNKNOWN_COMMAND = 1;

    /**
     * @param string $command
     * @return CommandException
     */
    public static function unknownCommand(string $command): CommandException
    {
        return new self('Unknown command: ' . $command, self::UNKNOWN_COMMAND);
    }
}
