<?php

namespace spec\TjoPatchBuilder\Buffer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use TjoPatchBuilder\Buffer\Exception\LineNumberPastEndOfBufferException;
use TjoPatchBuilder\Buffer\Exception\RangePastEndOfBufferException;
use TjoPatchBuilder\Types\LineRange;
use TjoPatchBuilder\Types\LineNumber;

class EditableLineBufferSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(array(
            'buffer',
            'contents'
        ));
    }

    public function it_extends_LineBuffer()
    {
        $this->shouldBeAnInstanceOf('TjoPatchBuilder\Buffer\LineBuffer');
    }

    /*
     * insert()
     */

    public function it_throws_when_trying_to_insert_at_a_line_after_end_of_buffer()
    {
        $this->shouldThrow(new LineNumberPastEndOfBufferException(4))
             ->duringInsert(new LineNumber(4), array(''));
    }

    public function it_inserts_at_a_given_line_number()
    {
        $this->insert(new LineNumber(2), array('new line'));

        $this->getContents()->shouldReturn(array('buffer', 'new line', 'contents'));
    }

    public function it_inserts_multiple_lines_at_a_given_line_number()
    {
        $this->insert(new LineNumber(2), array('line1', 'line2'));

        $this->getContents()->shouldReturn(
            array('buffer', 'line1', 'line2', 'contents')
        );
    }

    public function it_can_insert_at_beginning()
    {
        $this->insert(new LineNumber(1), array('new line'));

        $this->getContents()->shouldReturn(
            array('new line', 'buffer', 'contents')
        );
    }

    public function it_can_insert_at_end()
    {
        $this->insert(new LineNumber(3), array('new line'));

        $this->getContents()->shouldReturn(
            array('buffer', 'contents', 'new line')
        );
    }

    /*
     * delete
     */

    public function it_throws_if_delete_range_is_invalid()
    {
        $this->shouldThrow(new RangePastEndOfBufferException('Range 2-5 goes beyond buffer with 2 lines.'))
             ->duringDelete(LineRange::createFromNumbers(2, 5));
    }

    public function it_deletes_the_given_line()
    {
        $this->delete(LineRange::createSingleLine(1));

        $this->getContents()->shouldReturn(array('contents'));
    }

    public function it_deletes_a_line_range()
    {
        $this->delete(LineRange::createFromNumbers(1, 2));

        $this->getContents()->shouldReturn(array());
    }

    /*
     * replace()
     */

    public function it_throws_if_replace_range_is_invalid()
    {
        $this->shouldThrow(new RangePastEndOfBufferException('Range 2-5 goes beyond buffer with 2 lines.'))
             ->duringReplace(LineRange::createFromNumbers(2, 5), array());
    }

    public function it_replaces_a_given_line_with_lines()
    {
        $this->replace(LineRange::createSingleLine(1), array('line1', 'line2'));

        $this->getContents()->shouldReturn(array('line1', 'line2', 'contents'));
    }
}
