<?php

namespace TomPHP\PatchBuilder\Buffer;

use TomPHP\PatchBuilder\Exception\InvalidLineNumberException;
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
            $lineNumber->getNumber() - 1,
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
            $range->getStart()->getNumber() - 1,
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
        if ($lineNumber < 1) {
            throw new InvalidLineNumberException($lineNumber);
        }

        if ($lineNumber > $this->getLength() + 1) {
            throw new LineNumberPastEndOfBufferException($lineNumber);
        }
    }
}