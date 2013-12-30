<?php

namespace TomPHP\PatchBuilder\LineTracker;

use TomPHP\PatchBuilder\LineTracker\Exception\DeletedLineException;
use TomPHP\PatchBuilder\Types\LineRange;
use TomPHP\PatchBuilder\Types\LineNumber;
use TomPHP\PatchBuilder\Types\ModifiedLineNumber;

class LineTracker
{
    /**
     * @var array
     */
    private $actions = array();

    /**
     * @var int
     */
    private $trackedLine;

    /**
     * @param int $lineNumber
     *
     * @return int
     */
    public function trackLine(LineNumber $lineNumber)
    {
        $this->trackedLine = $lineNumber->getNumber();

        foreach ($this->actions as $action) {
            $actionLine = $action['line'];

            switch ($action['name']) {
                case 'add':
                    $this->trackLineAdded($actionLine);
                    break;

                case 'delete':
                    $this->trackLineDeleted($actionLine, $lineNumber->getNumber());
            }
        }

        return new ModifiedLineNumber($this->trackedLine);
    }

    public function deleteLine(LineNumber $lineNumber)
    {
        $this->actions[] = array(
            'name' => 'delete',
            'line' => $lineNumber->getNumber(),
        );
    }

    public function addLine(LineNumber $lineNumber)
    {
        $this->actions[] = array(
            'name' => 'add',
            'line' => $lineNumber->getNumber()
        );
    }

    public function deleteLines(LineRange $range)
    {
        for ($line = 0; $line < $range->getLength(); $line++) {
            $this->deleteLine($range->getStart());
        }
    }

    public function addLines(LineRange $range)
    {
        for ($line = $range->getStart()->getNumber(); $line <= $range->getEnd()->getNumber(); $line++) {
            $this->addLine($range->getStart());
        }
    }

    /**
     * @param int $actionLine
     * @param int $lineNumber
     */
    private function trackLineDeleted($actionLine, $lineNumber)
    {
        $this->assertTrackedLineWasNotDeleted($actionLine, $lineNumber);

        if ($actionLine < $this->trackedLine) {
            $this->trackedLine--;
        }
    }

    /**
     * @param int $actionLine
     */
    private function trackLineAdded($actionLine)
    {
        if ($actionLine <= $this->trackedLine) {
            $this->trackedLine++;
        }
    }

    /**
     * @param int $actionLine
     * @param int $lineNumber
     */
    private function assertTrackedLineWasNotDeleted($actionLine, $lineNumber)
    {
        if ($actionLine == $this->trackedLine) {
            throw new DeletedLineException($lineNumber);
        }
    }
}
