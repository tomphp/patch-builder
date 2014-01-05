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

    /**
     * @param string[] $lines
     */
    public function replace(LineRangeInterface $range, array $lines)
    {
        if (empty($lines)) {
            return $this->delete($range);
        }

        $range = $this->convertIfContainsOriginalLineNumbers($range);

        $this->modified->replace($range, $lines);

        $this->lineTracker->deleteLines($range);
        $this->lineTracker->addLines($this->calculateInsertRange($range->getStart(), $lines));
    }

    /**
     * @param string[] $lines
     */
    public function insert(LineNumber $lineNumber, array $lines)
    {
        if (empty($lines)) {
            return;
        }

        $lineNumber = $this->convertIfIsOriginalLineNumber($lineNumber);

        $this->insertAtLineNumber($lineNumber, $lines);
    }

    /**
     * @param string[] $lines
     */
    public function append(LineNumber $lineNumber, array $lines)
    {
        if (empty($lines)) {
            return;
        }

        $lineNumber = $this->convertIfIsOriginalLineNumber($lineNumber);

        $lineNumber = new LineNumber($lineNumber->getNumber() + 1);

        $this->insertAtLineNumber($lineNumber, $lines);
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
     * @return boolean
     */
    public function isModified()
    {
        return $this->original->getContents() !== $this->modified->getContents();
    }

    private function insertAtLineNumber(LineNumber $lineNumber, array $lines)
    {
        $this->modified->insert($lineNumber, $lines);

        $this->lineTracker->addLines($this->calculateInsertRange($lineNumber, $lines));
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

    private function calculateInsertRange(LineNumber $lineNumber, array $lines)
    {
        $start = $lineNumber->getNumber();
        $end   = $start + count($lines) - 1;

        return LineRange::createFromNumbers($start, $end);
    }
}
