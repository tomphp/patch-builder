<?php

namespace spec\TomPHP\PatchBuilder;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use TomPHP\PatchBuilder\Buffer\EditableLineBuffer;
use TomPHP\PatchBuilder\Buffer\LineBuffer;
use TomPHP\PatchBuilder\Types\LineRange;
use TomPHP\PatchBuilder\Types\ModifiedLineNumber;
use TomPHP\PatchBuilder\Types\OriginalLineNumber;
use TomPHP\PatchBuilder\LineTracker\LineTracker;
use TomPHP\PatchBuilder\Types\LineNumber;

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

    public function it_calls_delete_if_no_lines_are_given()
    {
        $range = LineRange::createFromNumbers(5, 7);

        $this->modified->delete($range)->shouldBeCalled();

        $this->replace($range, array());
    }

    public function it_calls_replace_on_modified_content()
    {
        $range = LineRange::createFromNumbers(5, 7);
        $lines = array('lines');

        $this->modified->replace($range, $lines)->shouldBeCalled();

        $this->replace($range, $lines);
    }

    public function it_converts_original_lines_to_modified_lines_when_replacing()
    {
        $original = new LineRange(new OriginalLineNumber(3), new OriginalLineNumber(6));
        $modified = new LineRange(new ModifiedLineNumber(3), new ModifiedLineNumber(6));

        $this->setupRangeConversionTests($original, $modified);

        $this->lineTracker->deleteLines(Argument::any())->willReturn();
        $this->lineTracker->addLines(Argument::any())->willReturn();

        $this->modified->replace($modified, Argument::any())->shouldBeCalled();

        $this->replace($original, array('x'));
    }

    public function it_tracks_lines_removed_when_replacing()
    {
        $range = LineRange::createFromNumbers(5, 10);

        $this->lineTracker
             ->deleteLines($range)
             ->shouldBeCalled();

        $this->lineTracker->addLines(Argument::any())->willReturn();

        $this->replace($range, array(''));
    }

    public function it_tracks_lines_inserted_when_replacing()
    {
        $this->lineTracker
             ->addLines(LineRange::createFromNumbers(3, 5))
             ->shouldBeCalled();

        $this->lineTracker->deleteLines(Argument::any())->willReturn();

        $this->replace(LineRange::createFromNumbers(3, 8), array('1', '2', '3'));
    }

    /*
     * insert()
     */

    public function it_skips_if_trying_to_insert_nothing(LineNumber $line)
    {
        $this->modified->insert()->shouldNotBeCalled();

        $this->insert($line, array());
    }

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

        $this->lineTracker->addLines(Argument::any())->willReturn();

        $this->modified
             ->insert($modified, Argument::any())
             ->shouldBeCalled();

        $this->insert($modified, array('x'));
    }

    public function it_tracks_inserting_of_lines()
    {
        $this->lineTracker
             ->addLines(LineRange::createFromNumbers(3, 5))
             ->shouldBeCalled();

        $this->insert(new LineNumber(3), array('1', '2', '3'));
    }

    /*
     * append()
     */

    public function it_skips_if_trying_to_append_nothing(LineNumber $line)
    {
        $this->modified->insert()->shouldNotBeCalled();

        $this->append($line, array());
    }

    public function it_calls_append_on_modified_content()
    {
        $lines = array('lines');

        $this->modified->insert(new LineNumber(7), $lines)->shouldBeCalled();

        $this->append(new LineNumber(6), $lines);
    }

    public function it_adds_1_to_modified_line_number_and_inserts()
    {
        $original = new OriginalLineNumber(3);
        $modified = new ModifiedLineNumber(8);
        $final    = new LineNumber(9);

        $lines = array('lines');

        $this->lineTracker
             ->trackLine($original)
             ->shouldBeCalled()
             ->willReturn($modified);

        $this->modified->insert($final, $lines)->shouldBeCalled();

        $this->lineTracker->addLines(Argument::any())->willReturn();

        $this->append($original, $lines);
    }

    public function it_tracks_appending()
    {
        $this->lineTracker
             ->addLines(LineRange::createFromNumbers(6, 7))
             ->shouldBeCalled();

        $this->append(new LineNumber(5), array('1', '2'));
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

        $this->lineTracker->deleteLines(Argument::any())->willReturn();

        $this->modified->delete($modified)->shouldBeCalled();

        $this->delete($original);
    }

    public function it_tracks_deletion_of_lines()
    {
        $range = LineRange::createFromNumbers(5, 10);

        $this->lineTracker
             ->deleteLines($range)
             ->shouldBeCalled();

        $this->delete($range);
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
