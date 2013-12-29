<?php

namespace TjoPatchBuilder\Types\Exception;

class InvalidLineRangeException extends \RangeException
{
    /**
     * @return InvalidLineRangeException
     */
    public static function startGreaterThanEnd()
    {
        return new self('Start line is greater than end line.');
    }
}
