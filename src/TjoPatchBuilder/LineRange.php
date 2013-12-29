<?php

namespace TjoPatchBuilder;

use TjoPatchBuilder\Exception\InvalidLineNumberException;
use TjoPatchBuilder\Exception\InvalidLineRangeException;

class LineRange implements LineRangeInterface
{
    /**
     * @var int
     */
    private $start;

    /**
     * @var int
     */
    private $end;

    /**
     * @param int $start
     * @param int $end
     *
     * @throws InvalidLineNumberException
     * @throws InvalidLineRangeException
     */
    public function __construct($start, $end)
    {
        if ($start < 1) {
            throw new InvalidLineNumberException(0);
        }

        if ($start > $end) {
            throw InvalidLineRangeException::startGreaterThanEnd();
        }

        $this->start = (int) $start;
        $this->end   = (int) $end;
    }

    public function getStart()
    {
        return $this->start;
    }

    public function getEnd()
    {
        return $this->end;
    }

    public function getLength()
    {
        return $this->end - $this->start + 1;
    }
}
