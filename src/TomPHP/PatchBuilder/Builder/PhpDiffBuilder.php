<?php

namespace TomPHP\PatchBuilder\Builder;

use TomPHP\PatchBuilder\PatchBuffer;
use TomPHP\PatchBuilder\PatchBuilder;

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
