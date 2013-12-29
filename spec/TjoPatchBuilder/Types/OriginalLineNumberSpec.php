<?php

namespace spec\TjoPatchBuilder\Types;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class OriginalLineNumberSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(5);
    }

    public function it_a_line_number()
    {
        $this->shouldHaveType('TjoPatchBuilder\Types\LineNumber');
    }
}
