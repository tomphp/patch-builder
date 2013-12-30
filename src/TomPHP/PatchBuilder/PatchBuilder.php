<?php

namespace TomPHP\PatchBuilder;

interface PatchBuilder
{
    /**
     * @param string $originalFileName
     * @param string $modfiedFileName
     *
     * @return string
     */
    public function buildPatch(
        $originalFileName,
        $modfiedFileName,
        PatchBuffer $buffer
    );
}
