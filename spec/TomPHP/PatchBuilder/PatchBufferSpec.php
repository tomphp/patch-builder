<?php

namespace spec\TjoPatchBuilder;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use TjoPatchBuilder\Buffer\EditableLineBuffer;
use TjoPatchBuilder\Buffer\LineBuffer;
use TjoPatchBuilder\Types\LineRange;
use TjoPatchBuilder\Types\ModifiedLineNumber;
use TjoPatchBuilder\Types\OriginalLineNumber;
use TjoPatchBuilder\LineTracker\LineTracker;
use TjoPatchBuilder\Types\LineNumber;

class PatchBufferSpec extends ObjectBehavior
{
    /**
     * @var LineBuffer
     */
    private $original;

    /**
     * @var EditableLineBuffer
     */
    private $modified;

    /**
     * @var LineTracker
     */
    private $lineTracker;

    public function let(LineBuffer $original, EditableLineBuffer $modified, LineTracker $lineTracker)
    {
        $this->original    = $original;
        $this->modified    = $modified;
        $this->lineTracker = $lineTracker;

        $this->beConstructedWith($original, $modified, $lineTracker);
    }

    public function it_returns_original_buffer_contents()
    {
        $this->original->getContents()->willReturn('contents');

        $this->getOriginalContents()->shouldReturn('contents');
    }

    public function it_returns_modified_buffer_contents()
    {
        $this->modified->getContents()->willReturn('contents');

        $this->getModifiedContents()->shouldReturn('contents');
    }

    public function it_has_factory_method_which_creates_from_contents()
    {
        $contents = array('hello', 'world');

        $buffer = $this::createWithContents($contents);

        $buffer->getOriginalContents()->shouldReturn($contents);
        $buffer->getModifiedContents()->shouldReturn($contents);
    }

    /*
     * replace()
     */

    public function it_calls_replace_on_modified_content(LineRange $range)
    {
        $lines = array('lines');

        $this->modified->replace($range, $lines)->shouldBeCalled();

        $this->replace($range, $lines);
    }

    public function it_converts_original_lines_to_modified_lines_when_replacing()
    {
        $original = new LineRange(new OriginalLineNumber(3), new OriginalLineNumber(6));
        $modified = new LineRange(new ModifiedLineNumber(3), new ModifiedLineNumber(6));

        $this->setupRangeConversionTests($original, $modified);

        $this->modified->replace($modified, Argument::any())->shouldBeCalled();

        $this->replace($original, array());
    }

    /*
     * insert()
     */

    public function it_calls_insert_on_modified_content(LineNumber $line)
    {
        $lines = array('lines');

        $this->modified->insert($line, $lines)->shouldBeCalled();

        $this->insert($line, $lines);
    }

    public function it_converts_original_lines_to_modified_lines_when_inserting()
    {
        $original = new OriginalLineNumber(3);

        $modified = new ModifiedLineNumber(3);

        $this->lineTracker
             ->trackLine($original)
             ->willReturn($modified);

        $this->modified
             ->insert($modified, Argument::any())
             ->shouldBeCalled();

        $this->insert($modified, array());
    }

    /*
     * delete()
     */

    public function it_calls_delete_on_modified_content(LineRange $range)
    {
        $this->modified->delete($range)->shouldBeCalled();

        $this->delete($range);
    }

    public function it_converts_original_lines_to_modified_lines_when_deleting()
    {
        $original = new LineRange(new OriginalLineNumber(3), new OriginalLineNumber(6));
        $modified = new LineRange(new ModifiedLineNumber(3), new ModifiedLineNumber(6));

        $this->setupRangeConversionTests($original, $modified);

        $this->modified->delete($modified)->shouldBeCalled();

        $this->delete($original);
    }

    /*
     * getLine()
     */

    public function it_fetches_a_single_line_from_modified_buffer()
    {
        $this->modified
             ->getLines(LineRange::createSingleLine(7))
             ->shouldBeCalled()
             ->willReturn(array());

        $this->getLine(new LineNumber(7));
    }

    public function it_returns_single_line_from_getLine()
    {
        $this->modified
             ->getLines(Argument::any())
             ->willReturn(array('single line'));

        $this->getLine(new LineNumber(7))->shouldReturn('single line');
    }

    public function it_converts_original_line_to_modified_line_when_getting_a_single_line()
    {
        $original = new OriginalLineNumber(3);
        $modified = new ModifiedLineNumber(8);

        $this->lineTracker
             ->trackLine($original)
             ->willReturn($modified);

        $this->modified
             ->getLines(LineRange::createSingleLine(8))
             ->shouldBeCalled()
             ->willReturn(array());

        $this->getLine($original);
    }

    /*
     * Private methods
     */

    private function setupRangeConversionTests($original, $modified)
    {
        $this->lineTracker
             ->trackLine($original->getStart())
             ->willReturn($modified->getStart());

        $this->lineTracker
             ->trackLine($original->getEnd())
             ->willReturn($modified->getEnd());
    }
}
