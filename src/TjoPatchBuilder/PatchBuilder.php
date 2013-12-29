<?php

namespace TjoPatchBuilder;

interface PatchBuilder
{
    /**
     * @param string $originalFileName
     * @param string $modfiedFileName
     * @return string
     */
    public function buildPatch(
        $originalFileName,
        $modfiedFileName,
        PatchBuffer $buffer
    );
}
