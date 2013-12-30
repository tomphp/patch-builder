<?php

namespace spec\TomPHP\PatchBuilder\LineTracker;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use TomPHP\PatchBuilder\LineTracker\Exception\DeletedLineException;
use TomPHP\PatchBuilder\Types\LineRange;
use TomPHP\PatchBuilder\Types\LineNumber;

class LineTrackerSpec extends ObjectBehavior
{
    public function it_returns_modified_line_numbers()
    {
        $this->trackLine(new LineNumber(5))
             ->shouldReturnAnInstanceOf('TomPHP\PatchBuilder\Types\ModifiedLineNumber');
    }

    public function it_returns_the_number_given_when_no_changes_have_been_made()
    {
        $this->trackLine(new LineNumber(5))->getNumber()->shouldReturn(5);
        $this->trackLine(new LineNumber(7))->getNumber()->shouldReturn(7);
    }

    public function it_tracks_lines_after_a_single_deleted_line()
    {
        $this->deleteLine(new LineNumber(5));

        $this->trackLine(new LineNumber(7))->getNumber()->shouldReturn(6);
    }

    public function it_doesnt_alter_a_line_before_a_deletion()
    {
        $this->deleteLine(new LineNumber(5));

        $this->trackLine(new LineNumber(2))->getNumber()->shouldReturn(2);
    }

    /*
    public function it_throws_exception_if_the_line_requested_has_been_deleted()
    {
        $this->deleteLine(new LineNumber(5));

        $this->shouldThrow(new DeletedLineException(5))
             ->duringTrackLine(5);
    }
    */

    public function it_tracks_a_line_after_2_deletions()
    {
        $this->deleteLine(new LineNumber(5));
        $this->deleteLine(new LineNumber(5));

        $this->trackLine(new LineNumber(7))->getNumber()->shouldReturn(5);
    }

    public function it_tracks_a_line_ones_between_2_deletes()
    {
        $this->deleteLine(new LineNumber(3));
        $this->deleteLine(new LineNumber(10));

        $this->trackLine(new LineNumber(6))->getNumber()->shouldReturn(5);
    }

    /*
    public function it_throws_if_a_line_has_ever_been_deleted()
    {
        $this->deleteLine(new LineNumber(5));
        $this->deleteLine(new LineNumber(5));

        $this->shouldThrow(new DeletedLineException(6))
             ->duringTrackLine(6);
    }
    */

    public function it_tracks_a_line_after_a_line_has_been_added()
    {
        $this->addLine(new LineNumber(4));

        $this->trackLine(new LineNumber(5))->getNumber()->shouldReturn(6);
    }

    public function it_doesnt_alter_a_line_before_an_addition()
    {
        $this->addLine(new LineNumber(5));

        $this->trackLine(new LineNumber(2))->getNumber()->shouldReturn(2);
    }

    public function it_tracks_a_line_after_2_additions()
    {
        $this->addLine(new LineNumber(5));
        $this->addLine(new LineNumber(5));

        $this->trackLine(new LineNumber(7))->getNumber()->shouldReturn(9);
    }

    public function it_tracks_a_line_between_2_additions()
    {
        $this->addLine(new LineNumber(5));
        $this->addLine(new LineNumber(10));

        $this->trackLine(new LineNumber(7))->getNumber()->shouldReturn(8);
    }

    public function it_tracks_add_then_delete_in_correct_order()
    {
        $this->addLine(new LineNumber(5));
        $this->deleteLine(new LineNumber(5));

        $this->trackLine(new LineNumber(5))->getNumber()->shouldReturn(5);
    }

    public function it_tracks_delete_then_add_in_correct_order()
    {
        $this->deleteLine(new LineNumber(5));
        $this->addLine(new LineNumber(7));

        $this->trackLine(new LineNumber(7))->getNumber()->shouldReturn(6);
    }

    public function it_tracks_line_after_deleted_block()
    {
        $this->deleteLines(LineRange::createFromNumbers(3, 5));

        $this->trackLine(new LineNumber(10))->getNumber()->shouldReturn(7);
    }

    public function it_tracks_block_deletes_in_correct_order()
    {
        $this->deleteLines(LineRange::createFromNumbers(3, 5));

        $this->trackLine(new LineNumber(6))->getNumber()->shouldReturn(3);
    }

    public function it_tracks_line_after_added_block()
    {
        $this->addLines(LineRange::createFromNumbers(3, 5));

        $this->trackLine(new LineNumber(6))->getNumber()->shouldReturn(9);
    }
}
