<?php

namespace Clippings\Component\Calculator\Util\Command;

class CommandRunner
{
    /** @var array */
    private $params = [];

    /** @var array */
    private $options = [];

    /** @var string */
    private $command;

    /** @var CommandInterface[] */
    private $commands = [];

    public function __construct(array $argv)
    {
        $this->parseArgv($argv);
    }

    /**
     * @param string $name
     * @param CommandInterface $command
     */
    public function addCommand(string $name, CommandInterface $command): void
    {
        $this->commands[$name] = $command;
    }

    public function run(): void
    {
        if ($this->command === null) {
            $this->printAllCommands();
        } else {
            if (array_key_exists($this->command, $this->commands)) {
                $this->commands[$this->command]->exec($this->params, $this->options);
            } else {
                throw CommandException::unknownCommand($this->command);
            }
        }
    }

    private function printAllCommands(): void
    {
        echo PHP_EOL . ' List of commands: ' . PHP_EOL . PHP_EOL;

        if (empty($this->commands)) {
            echo '  There are no commands.' . PHP_EOL;
            return;
        }

        foreach ($this->commands as $name => $command) {
            echo '  - ' . $name . ' ' . $command->getDescription() . PHP_EOL;
        }
    }

    /**
     * @param array $argv
     */
    private function parseArgv(array $argv): void
    {
        unset($argv[0]);

        foreach ($argv as $arg) {
            if (strpos($arg, '--') === 0) {
                $parts = explode('=', $arg);
                $this->options[substr($parts[0], 2)] = $parts[1] ?? true;
            } else {
                if ($this->command === null) {
                    $this->command = $arg;
                } else {
                    $this->params[] = $arg;
                }
            }
        }
    }
}
