<?php

namespace spec\TjoPatchBuilder\Types;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use TjoPatchBuilder\Types\Exception\InvalidLineNumberException;
use TjoPatchBuilder\Types\Exception\InvalidLineRangeException;
use TjoPatchBuilder\Types\LineNumber;

class LineRangeSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(new LineNumber(3), new LineNumber(6));
    }

    public function it_throws_if_start_line_is_negative()
    {
        $this->shouldThrow(new InvalidLineNumberException(0))
             ->during('__construct', array(new LineNumber(0), new LineNumber(5)));
    }

    public function it_throws_if_end_line_is_less_than_start()
    {
        $this->shouldThrow(new InvalidLineRangeException('Start line is greater than end line.'))
             ->during('__construct', array(new LineNumber(4), new LineNumber(3)));
    }

    public function it_implements_LineRangeInterface()
    {
        $this->shouldBeAnInstanceOf(
            'TjoPatchBuilder\Types\LineRangeInterface'
        );
    }

    public function it_stores_start_line()
    {
        $this->getStart()->getNumber()->shouldReturn(3);
    }

    public function it_stores_end_line()
    {
        $this->getEnd()->getNumber()->shouldReturn(6);
    }

    public function it_returns_length()
    {
        // start and end lines are inclusive
        $this->getLength()->shouldReturn(4);
    }

    public function it_has_factory_method_to_create_single_line_range()
    {
        $range = $this::createSingleLine(5);

        $range->getStart()->getNumber()->shouldReturn(5);
        $range->getEnd()->getNumber()->shouldReturn(5);
    }

    public function it_has_factory_method_to_create_from_integers()
    {
        $range = $this::createFromNumbers(3, 4);

        $range->getStart()->getNumber()->shouldReturn(3);
        $range->getEnd()->getNumber()->shouldReturn(4);
    }
}
