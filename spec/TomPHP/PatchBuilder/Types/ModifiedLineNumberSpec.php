<?php

namespace spec\TomPHP\PatchBuilder\Types;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ModifiedLineNumberSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(5);
    }

    public function it_a_line_number()
    {
        $this->shouldHaveType('TomPHP\PatchBuilder\Types\LineNumber');
    }
}
