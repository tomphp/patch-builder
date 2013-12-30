<?php

namespace TomPHP\PatchBuilder;

use TomPHP\PatchBuilder\Buffer\EditableLineBuffer;
use TomPHP\PatchBuilder\Buffer\LineBuffer;
use TomPHP\PatchBuilder\Types\LineRangeInterface;
use TomPHP\PatchBuilder\LineTracker\LineTracker;
use TomPHP\PatchBuilder\Types\OriginalLineNumber;
use TomPHP\PatchBuilder\Types\LineRange;
use TomPHP\PatchBuilder\Types\LineNumber;

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

    /**
     * @var LineTracker
     */
    private $lineTracker;

    /**
     * @param string[] $contents
     *
     * @return PatchBuffer
     */
    public static function createWithContents(array $contents)
    {
        return new self(
            new LineBuffer($contents),
            new EditableLineBuffer($contents),
            new LineTracker()
        );
    }

    public function __construct(
        LineBuffer $original,
        EditableLineBuffer $modified,
        LineTracker $lineTracker
    ) {
        // @todo Verify contents matches

        $this->original    = $original;
        $this->modified    = $modified;
        $this->lineTracker = $lineTracker;
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

    public function replace(LineRangeInterface $range, array $lines)
    {
        $range = $this->convertIfContainsOriginalLineNumbers($range);

        $this->modified->replace($range, $lines);
    }

    public function insert(LineNumber $lineNumber, array $lines)
    {
        if (empty($lines)) {
            return;
        }

        $lineNumber = $this->convertIfIsOriginalLineNumber($lineNumber);

        $this->modified->insert($lineNumber, $lines);

        $this->lineTracker->addLines($this->calcuteInsertRange($lineNumber, $lines));
    }

    private function calcuteInsertRange(LineNumber $lineNumber, array $lines)
    {
        $start = $lineNumber->getNumber();
        $end   = $start + count($lines) - 1;

        return LineRange::createFromNumbers($start, $end);
    }

    public function delete(LineRangeInterface $range)
    {
        $range = $this->convertIfContainsOriginalLineNumbers($range);

        $this->modified->delete($range);

        $this->lineTracker->deleteLines($range);
    }

    /**
     * @return string
     */
    public function getLine($lineNumber)
    {
        $lineNumber = $this->convertIfIsOriginalLineNumber($lineNumber);

        $lines = $this->modified->getLines(LineRange::createSingleLine($lineNumber->getNumber()));

        return reset($lines);
    }

    /**
     * @return LineNumber
     */
    private function convertIfIsOriginalLineNumber(LineNumber $lineNumber)
    {
        if ($lineNumber instanceof OriginalLineNumber) {
            $lineNumber = $this->lineTracker->trackLine($lineNumber);
        }

        return $lineNumber;
    }

    /**
     * @return LineRangeInterface
     */
    private function convertIfContainsOriginalLineNumbers(LineRangeInterface $range)
    {
        if ($range->getStart() instanceof OriginalLineNumber) {
            $range = $this->trackRange($range);
        }

        // @todo Throw if start and end types mismatch

        return $range;
    }

    /**
     * @return LineRange
     */
    private function trackRange(LineRangeInterface $range)
    {
        $range = new LineRange(
            $this->lineTracker->trackLine($range->getStart()),
            $this->lineTracker->trackLine($range->getEnd())
        );

        return $range;
    }
}
