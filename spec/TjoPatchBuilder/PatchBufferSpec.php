<?php

namespace spec\TjoPatchBuilder;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use TjoPatchBuilder\Buffer\EditableLineBuffer;
use TjoPatchBuilder\Buffer\LineBuffer;

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

    public function let(LineBuffer $original, EditableLineBuffer $modified)
    {
        $this->original = $original;
        $this->modified = $modified;

        $this->beConstructedWith($original, $modified);
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

        $buffer = $this::createFromContents($contents);

        $buffer->getOriginalContents()->shouldReturn($contents);
        $buffer->getModifiedContents()->shouldReturn($contents);
    }
}
