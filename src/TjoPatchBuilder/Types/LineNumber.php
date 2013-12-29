<?php

namespace TjoPatchBuilder\Types;

use TjoPatchBuilder\Types\Exception\InvalidLineNumberException;

class LineNumber
{
    /**
     * @var int
     */
    protected $lineNumber;

    public function __construct($lineNumber)
    {
        if ($lineNumber < 0) {
            throw new InvalidLineNumberException($lineNumber);
        }

        $this->lineNumber = (int) $lineNumber;
    }

    /**
     * @return int
     */
    public function getNumber()
    {
        return $this->lineNumber;
    }
}
