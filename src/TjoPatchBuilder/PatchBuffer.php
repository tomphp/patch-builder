<?php

namespace TjoPatchBuilder;

use TjoPatchBuilder\Buffer\EditableLineBuffer;
use TjoPatchBuilder\Buffer\LineBuffer;
use TjoPatchBuilder\Exception\InvalidLineNumberException;
use TjoPatchBuilder\Exception\LineNumberPastEndOfBufferException;
use TjoPatchBuilder\Exception\RangePastEndOfBufferException;

class PatchBuffer
{
    /**
     * @var LineBuffer
     */
    private $original;

    /**
     * @var EditableLineBuffer
     */
    private $modified;

    public function __construct(LineBuffer $original, EditableLineBuffer $modified)
    {
        // @todo Verify contents matches

        $this->original = $original;
        $this->modified = $modified;
    }

    /**
     * @return string[]
     */
    public function getOriginalContents()
    {
        return $this->original->getContents();
    }

    /**
     * @return string[]
     */
    public function getModifiedContents()
    {
        return $this->modified->getContents();
    }

    /**
     * @param string[] $contents
     *
     * @return PatchBuffer
     */
    public static function createFromContents(array $contents)
    {
        return new self(
            new LineBuffer($contents),
            new EditableLineBuffer($contents)
        );
    }
}
