<?php

namespace TjoPatchBuilder\Builder;

use TjoPatchBuilder\PatchBuffer;
use TjoPatchBuilder\PatchBuilder;

class PhpDiffBuilder implements PatchBuilder
{
    public function buildPatch($originalFile, $modfiedFileName, PatchBuffer $buffer)
    {
        $diff = new \Diff(
            $buffer->getOriginalContents(),
            $buffer->getModifiedContents()
        );

        $renderer = new \Diff_Renderer_Text_Unified();

        return "--- $originalFile\n"
            . "+++ $modfiedFileName\n"
            . $diff->render($renderer);

    }
}
