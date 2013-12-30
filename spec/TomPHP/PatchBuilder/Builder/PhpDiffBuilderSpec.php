<?php

namespace spec\TjoPatchBuilder\Builder;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use TjoPatchBuilder\PatchBuffer;

class PhpDiffBuilderSpec extends ObjectBehavior
{
    public function it_is_a_patch_builder()
    {
        $this->shouldBeAnInstanceOf(
            'TjoPatchBuilder\PatchBuilder'
        );
    }

    public function it_creates_diff_from_buffer(PatchBuffer $buffer)
    {
        $originalFile = 'aaa/aaa/file.txt';
        $newFile = 'bbb/bbb/file.txt';

        $buffer->getOriginalContents()->willReturn(array(
            'line1',
            'line3',
            'line 4',
            'line5'
        ));

        $buffer->getModifiedContents()->willReturn(array(
            'line1',
            'line2',
            'line3',
            'line4',
            'line5'
        ));

        $this->buildPatch($originalFile, $newFile, $buffer)
             ->shouldReturn(
                "--- aaa/aaa/file.txt\n" .
                "+++ bbb/bbb/file.txt\n" .
                "@@ -1,4 +1,5 @@\n" .
                " line1\n" .
                "+line2\n" .
                " line3\n" .
                "-line 4\n" .
                "+line4\n" .
                " line5\n"
             );
    }
}
