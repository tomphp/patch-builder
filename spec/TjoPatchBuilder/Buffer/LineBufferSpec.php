<?php

namespace spec\TjoPatchBuilder\Buffer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use TjoPatchBuilder\Exception\RangePastEndOfBufferException;
use TjoPatchBuilder\LineRange;

class LineBufferSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(array(
            'buffer',
            'contents'
        ));
    }

    public function it_stores_contents()
    {
        $this->getContents()->shouldReturn(array('buffer', 'contents'));
    }

    public function it_calculates_its_length()
    {
        $this->getLength()->shouldReturn(2);
    }

    public function it_throws_if_getLines_gets_range_which_goes_past_end_of_buffer()
    {
        $this->shouldThrow(new RangePastEndOfBufferException('Range 2-5 goes beyond buffer with 2 lines.'))
             ->duringGetLines(new LineRange(2, 5));
    }

    public function it_fetches_one_line_from_the_buffer()
    {
        $this->getLines(new LineRange(2,2))
             ->shouldReturn(array('contents'));
    }

    public function it_fetches_two_lines_from_the_buffer()
    {
        $this->getLines(new LineRange(1, 2))
             ->shouldReturn(array('buffer', 'contents'));
    }
}
