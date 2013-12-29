<?php

namespace TjoPatchBuilder\Buffer;

use TjoPatchBuilder\Exception\RangePastEndOfBufferException;
use TjoPatchBuilder\LineRangeInterface;

class LineBuffer
{
    /**
     * @var string[]
     */
    protected $contents;

    /**
     * @param string[] contents
     */
    public function __construct(array $contents = array())
    {
        $this->contents = $contents;
    }

    /**
     * @return string[]
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        return count($this->contents);
    }

    /**
     * @param LineRangeInterface $range
     *
     * @return string[]
     */
    public function getLines(LineRangeInterface $range)
    {
        $this->assertRangeIsInsideBuffer($range);

        return array_slice(
            $this->contents,
            $range->getStart() - 1,
            $range->getLength()
        );
    }

    protected function assertRangeIsInsideBuffer($range)
    {
        if ($range->getEnd() > $this->getLength()) {
            throw RangePastEndOfBufferException::fromRange(
                $range,
                $this->getLength()
            );
        }
    }
}
