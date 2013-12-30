<?php

namespace spec\TomPHP\PatchBuilder\Types;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use TomPHP\PatchBuilder\Types\Exception\InvalidLineNumberException;

class LineNumberSpec extends ObjectBehavior
{
    public function it_stores_the_line_number()
    {
        $this->beConstructedWith(7);

        $this->getNumber()->shouldReturn(7);
    }

    public function it_throws_if_line_number_is_negative()
    {
        $this->shouldThrow(new InvalidLineNumberException(-1))
             ->during('__construct', array(-1));
    }
}
