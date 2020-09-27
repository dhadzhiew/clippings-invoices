<?php

namespace Tests\Unit\Facade;

use Clippings\Component\Calculator\Util\Command\CommandException;
use Clippings\Component\Calculator\Util\Command\CommandInterface;
use Clippings\Component\Calculator\Util\Command\CommandRunner;
use PHPUnit\Framework\TestCase;

class CommandRunnerTest extends TestCase
{
    public function testWithoutCommandParam()
    {
        ob_start();
        $runner = new CommandRunner(['file']);
        $runner->run();

        $output = ob_get_clean();
        $expected = PHP_EOL . ' List of commands: ' . PHP_EOL . PHP_EOL . '  There are no commands.' . PHP_EOL;

        $this->assertEquals($expected, $output);
    }

    public function testWithoutCommandParamWithAddedCommands()
    {
        $runner = new CommandRunner(['file']);
        $commandName = 'test-command';
        $commandDescription = 'command description';
        $command = $this->createMock(CommandInterface::class);
        $command->method('getDescription')
            ->willReturn($commandDescription);
        $runner->addCommand($commandName, $command);
        ob_start();
        $runner->run();

        $expected = PHP_EOL . ' List of commands: ' . PHP_EOL . PHP_EOL . '  - ' . $commandName . ' ' . $commandDescription . PHP_EOL;
        $output = ob_get_clean();

        $this->assertEquals($expected, $output);
    }

    public function testUnknownCommand()
    {
        $this->expectException(CommandException::class);
        $this->expectExceptionCode(CommandException::UNKNOWN_COMMAND);

        $runner = new CommandRunner(['file', 'command']);
        $runner->run();
    }

    public function testParams()
    {
        $runner = new CommandRunner(['file', 'command', 'param1', 'param2', '--option1=2', '--option2=3']);
        $command = $this->createMock(CommandInterface::class);
        $command
            ->expects($this->once())
            ->method('exec')
            ->with(['param1', 'param2'], [
                'option1' => 2,
                'option2' => 3
            ]);

        $runner->addCommand('command', $command);
        $runner->run();
    }
}