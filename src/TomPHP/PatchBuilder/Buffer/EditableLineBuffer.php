<?php

namespace TomPHP\PatchBuilder\Buffer;

use TomPHP\PatchBuilder\Buffer\Exception\LineNumberPastEndOfBufferException;
use TomPHP\PatchBuilder\Types\LineRangeInterface;
use TomPHP\PatchBuilder\Types\LineNumber;

class EditableLineBuffer extends LineBuffer
{
    /**
     * @param string[] $lines
     */
    public function insert(LineNumber $lineNumber, array $lines)
    {
        $this->assertLineNumberIsWithinRange($lineNumber->getNumber());

        array_splice(
            $this->contents,
            $lineNumber->getNumber(),
            0,
            $lines
        );
    }

    /**
     * @param string[] $lines
     */
    public function replace(LineRangeInterface $range, array $lines)
    {
        $this->assertRangeIsInsideBuffer($range);

        array_splice(
            $this->contents,
            $range->getStart()->getNumber(),
            $range->getLength(),
            $lines
        );
    }

    public function delete(LineRangeInterface $range)
    {
        $this->replace($range, array());
    }

    /**
     * @param int $lineNumber
     *
     * @throws InvalidLineNumberException
     * @throw  LineNumberPastEndOfBufferException
     */
    private function assertLineNumberIsWithinRange($lineNumber)
    {
        if ($lineNumber > $this->getLength()) {
            throw new LineNumberPastEndOfBufferException($lineNumber);
        }
    }
}
