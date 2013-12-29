<?php

namespace TjoPatchBuilder\Exception;

use TjoPatchBuilder\LineRange;

class RangePastEndOfBufferException extends \RangeException
{
    /**
     * @param int $bufferLength
     *
     * @return RangePastEndOfBufferException
     */
    public static function fromRange(LineRange $range, $bufferLength)
    {
        return new self(sprintf(
            'Range %d-%d goes beyond buffer with %d lines.',
            $range->getStart(),
            $range->getEnd(),
            $bufferLength
        ));
    }
}
