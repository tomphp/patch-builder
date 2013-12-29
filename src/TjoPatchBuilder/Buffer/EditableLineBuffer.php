<?php

namespace TjoPatchBuilder\Buffer;

use TjoPatchBuilder\Exception\InvalidLineNumberException;
use TjoPatchBuilder\Exception\LineNumberPastEndOfBufferException;
use TjoPatchBuilder\LineRangeInterface;

class EditableLineBuffer extends LineBuffer
{
    /**
     * @param int      $lineNumber
     * @param string[] $lines
     */
    public function insert($lineNumber, array $lines)
    {
        $this->assertLineNumberIsWithinRange($lineNumber);

        array_splice($this->contents, $lineNumber - 1, 0, $lines);
    }

    /**
     * @param string[] $lines
     */
    public function replace(LineRangeInterface $range, array $lines)
    {
        $this->assertRangeIsInsideBuffer($range);

        array_splice(
            $this->contents,
            $range->getStart() - 1,
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
