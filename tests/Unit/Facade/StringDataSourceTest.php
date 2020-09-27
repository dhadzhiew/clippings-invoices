<?php

namespace Tests\Unit\Facade;

use Clippings\Component\Calculator\Facade\StringDataSource;
use PHPUnit\Framework\TestCase;

class StringDataSourceTest extends TestCase
{
    public function testGetLinesWithMultilineInput()
    {
        $expected = [
            'Line 1',
            'Line 2',
            'Line 3',
        ];
        $dataSource = new StringDataSource(implode(PHP_EOL, $expected));

        $actual = [];
        foreach ($dataSource->getLines() as $line) {
            $actual[] = $line;
        }

        $this->assertSame($expected, $actual);
    }

    public function testGetLinesWithSingleLineInput()
    {
        $string = 'Line 1';
        $dataSource = new StringDataSource($string);

        $lines = [];
        foreach ($dataSource->getLines() as $line) {
            $lines[] = $line;
        }

        $this->assertEquals(count($lines), 1);
        $this->assertEquals($lines[0], $string);
    }

    public function testGetLinesEmptyInput()
    {
        $dataSource = new StringDataSource('');

        $lines = [];
        foreach ($dataSource->getLines() as $line) {
            $lines[] = $line;
        }

        $this->assertEquals(count($lines), 1);
        $this->assertEquals($lines[0], '');
    }
}
