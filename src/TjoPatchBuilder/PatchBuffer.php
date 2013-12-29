<?php

namespace TjoPatchBuilder;

use TjoPatchBuilder\Buffer\EditableLineBuffer;
use TjoPatchBuilder\Buffer\LineBuffer;

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

    /*
    public function replace(LineRangeInterface $range, array $lines)
    {
        $this->modified->replace($range, $lines);
    }

    public function removeLine($lineNumber)
    {
        $this->modified->delete(new LineRange($lineNumber, $lineNumber));
    }

    public function insert($lineNumber, array $lines)
    {
        $this->modified->insert($lineNumber, $lines);
    }

    public function getLine($lineNumber)
    {
        $lines = $this->modified->getLines(new LineRange($lineNumber, $lineNumber));

        return reset($lines);
    }
    */

    /**
     * @param string[] $contents
     *
     * @return PatchBuffer
     */
    public static function createWithContents(array $contents)
    {
        return new self(
            new LineBuffer($contents),
            new EditableLineBuffer($contents)
        );
    }
}
