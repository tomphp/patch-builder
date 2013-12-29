<?php

namespace spec\TjoPatchBuilder;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use TjoPatchBuilder\Exception\InvalidLineNumberException;
use TjoPatchBuilder\Exception\InvalidLineRangeException;

class LineRangeSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(3, 6);
    }

    public function it_throws_if_start_line_is_negative()
    {
        $this->shouldThrow(new InvalidLineNumberException(0))
             ->during('__construct', array(0, 5));
    }

    public function it_throws_if_end_line_is_less_than_start()
    {
        $this->shouldThrow(new InvalidLineRangeException('Start line is greater than end line.'))
             ->during('__construct', array(4, 3));
    }

    public function it_implements_LineRangeInterface()
    {
        $this->shouldBeAnInstanceOf(
            'TjoPatchBuilder\LineRangeInterface'
        );
    }

    public function it_stores_start_line()
    {
        $this->getStart()->shouldReturn(3);
    }

    public function it_stores_end_line()
    {
        $this->getEnd()->shouldReturn(6);
    }

    public function it_returns_length()
    {
        // start and end lines are inclusive
        $this->getLength()->shouldReturn(4);
    }
}
