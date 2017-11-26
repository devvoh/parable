<?php

namespace Parable\Tests\Components\Console;

class OutputTest extends \Parable\Tests\Base
{
    /** @var \Parable\Console\Output */
    protected $output;

    /** @var string */
    protected $defaultTag = "\e[0m";

    protected function setUp()
    {
        parent::setUp();

        // We mock out parseTags, because it adds too many escape codes. We'll test parseTags concretely later.
        $this->output = $this->createPartialMock(\Parable\Console\Output::class, ['parseTags']);

        $this->output
            ->method('parseTags')
            ->withAnyParameters()
            ->willReturnCallback(function ($string) {
                return $string . $this->defaultTag;
            });
    }

    /**
     * NOTE: All write actions are appended with the default Tag from Output's tags array.
     *
     * Use only this method to compare actual output to string values written.
     *
     * @param string $expected
     * @param string $actual
     */
    protected function assertSameWithTag($expected, $actual)
    {
        $expected = $this->addTag($expected);
        $this->assertSame($expected, $actual);
    }

    /**
     * Add the default tag where it's supposed to go.
     *
     * @param string $value
     * @param int    $amount
     * @return mixed|string
     */
    protected function addTag($value, $amount = 1)
    {
        $defaultTag = str_repeat($this->defaultTag, $amount);
        if (strpos($value, "\n") !== false) {
            // If there's new lines, the default tag is placed just before the newline.
            // At the end of the string, there won't be another default tag.
            $value = str_replace("\n", "{$defaultTag}\n", $value);
        } else {
            // If this is just a line with no newline, there will be a default tag at the end
            $value = $value . $defaultTag;
        }
        return $value;
    }

    public function testWrite()
    {
        $this->output->write('OK');
        $content = $this->getActualOutputAndClean();

        $this->assertSameWithTag("OK", $content);
    }

    public function testWriteln()
    {
        $this->output->writeln('OK');
        $content = $this->getActualOutputAndClean();

        $this->assertSameWithTag("OK\n", $content);
    }

    public function testWritelnWithArray()
    {
        $this->output->writeln([
            'line1',
            'line2'
        ]);
        $content = $this->getActualOutputAndClean();

        $this->assertSameWithTag("line1\nline2\n", $content);
    }

    public function testNewline()
    {
        // Just one.
        $this->output->newline();
        $this->assertSame("\n", $this->getActualOutputAndClean());

        // Now multiple
        $this->output->newline(3);
        $this->assertSame("\n\n\n", $this->getActualOutputAndClean());
    }

    public function testCursorForward()
    {
        $this->output->cursorForward(1);
        $this->assertSameWithTag("\e[1C", $this->getActualOutputAndClean());
    }

    public function testCursorBackward()
    {
        $this->output->cursorBackward(1);
        $this->assertSameWithTag("\e[1D", $this->getActualOutputAndClean());
    }

    public function testCursorUp()
    {
        $this->output->cursorUp(1);
        $this->assertSameWithTag("\e[1A", $this->getActualOutputAndClean());
    }

    public function testCursorDown()
    {
        $this->output->cursorDown(1);
        $this->assertSameWithTag("\e[1B", $this->getActualOutputAndClean());
    }

    public function testCursorPlace()
    {
        $this->output->cursorPlace(4, 8);
        $this->assertSameWithTag("\e[4;8H", $this->getActualOutputAndClean());
    }

    public function testCls()
    {
        $this->output->cls();
        $this->assertSameWithTag("\ec", $this->getActualOutputAndClean());
    }

    /**
     * This was a surprisingly hard test to do -_-
     */
    public function testClearLine()
    {
        $expectedLineLength = strlen($this->addTag('12345'));

        $this->output->write('12345');
        $this->assertSame($expectedLineLength, $this->output->getLineLength());

        // Clearing the line does multiple things. Moves the cursor back, overwrites the old text with spaces,
        // and moves the cursor back again, then resets the line length to 0.
        $this->output->clearLine();

        // Line length should have reset
        $this->assertSame(0, $this->output->getLineLength());

        $spaces = str_repeat(" ", $expectedLineLength);
        $expectedString  = $this->addTag("12345");
        $expectedString .= $this->addTag("\e[{$expectedLineLength}D{$spaces}");

        // The current lineLength is equivalent to the entire expectedString up to this point, since we wrote a string
        // (+tag), wrote escape codes to move the cursor back (+tag), wrote the appropriate amount of spaces (5+tag)
        $newExpectedLineLength = strlen($expectedString);
        $expectedString .= $this->addTag("\e[{$newExpectedLineLength}D");

        $this->assertSame($expectedString, $this->getActualOutputAndClean());
    }

    public function testWriteErrorBlock()
    {
        $this->output->writeErrorBlock('error');

        $output = [
            $this->addTag(""),
            $this->addTag(" <error>┌───────┐</error>"),
            $this->addTag(" <error>│ error │</error>"),
            $this->addTag(" <error>└───────┘</error>"),
            $this->addTag(""),
            "",
        ];

        $this->assertSame(
            implode("\n", $output),
            $this->getActualOutputAndClean()
        );
    }

    public function testWriteInfoBlock()
    {
        $this->output->writeInfoBlock('info');

        $output = [
            $this->addTag(""),
            $this->addTag(" <info>┌──────┐</info>"),
            $this->addTag(" <info>│ info │</info>"),
            $this->addTag(" <info>└──────┘</info>"),
            $this->addTag(""),
            "",
        ];

        $this->assertSame(
            implode("\n", $output),
            $this->getActualOutputAndClean()
        );
    }

    public function testWriteSuccessBlock()
    {
        $this->output->writeSuccessBlock('success');

        $output = [
            $this->addTag(""),
            $this->addTag(" <success>┌─────────┐</success>"),
            $this->addTag(" <success>│ success │</success>"),
            $this->addTag(" <success>└─────────┘</success>"),
            $this->addTag(""),
            "",
        ];

        $this->assertSame(
            implode("\n", $output),
            $this->getActualOutputAndClean()
        );
    }

    public function testWriteBlockWithAnyTag()
    {
        $this->output->writeBlock('any block', 'anytag');

        $output = [
            $this->addTag(""),
            $this->addTag(" <anytag>┌───────────┐</anytag>"),
            $this->addTag(" <anytag>│ any block │</anytag>"),
            $this->addTag(" <anytag>└───────────┘</anytag>"),
            $this->addTag(""),
            "",
        ];

        $this->assertSame(
            implode("\n", $output),
            $this->getActualOutputAndClean()
        );
    }

    public function testWriteBlockWithTagsUsingMultipleTags()
    {
        $this->output->writeBlockWithTags('any block', ["1", "2", "3"]);

        $output = [
            $this->addTag(""),
            $this->addTag(" <1><2><3>┌───────────┐</1></2></3>"),
            $this->addTag(" <1><2><3>│ any block │</1></2></3>"),
            $this->addTag(" <1><2><3>└───────────┘</1></2></3>"),
            $this->addTag(""),
            "",
        ];

        $this->assertSame(
            implode("\n", $output),
            $this->getActualOutputAndClean()
        );
    }

    public function testWriteBlockWithTagsUsingNoTagsOutputsNoTags()
    {
        $this->output->writeBlockWithTags('any block', []);

        $output = [
            $this->addTag(""),
            $this->addTag(" ┌───────────┐"),
            $this->addTag(" │ any block │"),
            $this->addTag(" └───────────┘"),
            $this->addTag(""),
            "",
        ];

        $this->assertSame(
            implode("\n", $output),
            $this->getActualOutputAndClean()
        );
    }

    public function testParseTagsForRealThisTime()
    {
        $output = new \Parable\Console\Output();

        // Unknown tags are ignored and not replaced, but still get the defaultTag at the end to reset any styles
        $this->assertSame($this->addTag('<tag>unknown</tag>'), $output->parseTags('<tag>unknown</tag>'));

        // Since tags are escaped with the defaultTag at the end, we'll need 2
        $this->assertSame($this->addTag("\e[0;32mgreen", 2), $output->parseTags('<green>green</green>'));
        $this->assertSame($this->addTag("\e[0;31mred", 2), $output->parseTags('<red>red</red>'));

        // And a more complex one, with both a fore- and a background color
        // Since tags are escaped with the defaultTag at the end and there's two tags, we'll need 3
        $this->assertSame(
            $this->addTag("\e[0;31m\e[47mred on lightgray", 3),
            $output->parseTags('<red><lightgray_bg>red on lightgray</lightgray_bg></red>')
        );
    }
}
